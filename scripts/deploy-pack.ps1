#!/usr/bin/env pwsh
[CmdletBinding()]
param(
    [string]$ConfigPath,
    [switch]$BuildOnly
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

if (-not ('System.IO.Compression.ZipArchiveMode' -as [type])) {
    throw 'System.IO.Compression.ZipArchiveMode is unavailable. Run this on Windows PowerShell 5.1 or PowerShell 7 with the .NET compression assemblies available.'
}

if ([string]::IsNullOrWhiteSpace($ConfigPath)) {
    $ConfigPath = Join-Path $PSScriptRoot '..\deploy.pack.json'
}

function Get-RepoRoot {
    return (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
}

function Read-JsonFile {
    param([Parameter(Mandatory = $true)][string]$Path)

    if (-not (Test-Path -LiteralPath $Path)) {
        throw "Config file not found: $Path"
    }

    return (Get-Content -LiteralPath $Path -Raw -Encoding UTF8) | ConvertFrom-Json
}

function Write-JsonFile {
    param(
        [Parameter(Mandatory = $true)]$Object,
        [Parameter(Mandatory = $true)][string]$Path
    )

    $json = $Object | ConvertTo-Json -Depth 10
    [System.IO.File]::WriteAllText($Path, $json, [System.Text.Encoding]::UTF8)
}

function New-RandomToken {
    $bytes = New-Object byte[] 32
    $rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    try {
        $rng.GetBytes($bytes)
    }
    finally {
        $rng.Dispose()
    }

    return ([System.BitConverter]::ToString($bytes) -replace '-', '').ToLowerInvariant()
}

function Normalize-PathSegment {
    param([Parameter(Mandatory = $true)][string]$Path)

    return ($Path -replace '\\', '/').Trim('/')
}

function Join-RemotePath {
    param([Parameter(Mandatory = $true)][string[]]$Parts)

    $cleanParts = @()
    foreach ($part in $Parts) {
        if ([string]::IsNullOrWhiteSpace($part)) {
            continue
        }

        $cleanParts += (Normalize-PathSegment -Path $part)
    }

    return ($cleanParts -join '/')
}

function Test-ExcludedPath {
    param([Parameter(Mandatory = $true)][string]$RelativePath)

    $p = $RelativePath -replace '\\', '/'

    $patterns = @(
        '^\.git(/|$)',
        '^\.fleet(/|$)',
        '^\.idea(/|$)',
        '^\.nova(/|$)',
        '^\.vscode(/|$)',
        '^\.zed(/|$)',
        '^\.sixth(/|$)',
        '^Downloads(/|$)',
        '^node_modules(/|$)',
        '^storage/(framework|logs)(/|$)',
        '^bootstrap/cache(/|$)',
        '^public/hot$',
        '^public/unpack\.php$',
        '^deploy\.pack\.json$',
        '^deploy\.remote\.json$',
        '^.*\.pack$',
        '^\.env($|\.backup$|\.production$)'
    )

    foreach ($pattern in $patterns) {
        if ($p -match $pattern) {
            return $true
        }
    }

    return $false
}

function New-FtpBaseUri {
    param(
        [Parameter(Mandatory = $true)][string]$Host,
        [Parameter(Mandatory = $true)][int]$Port
    )

    return "ftp://$Host`:$Port/"
}

function New-FtpRequest {
    param(
        [Parameter(Mandatory = $true)][string]$Uri,
        [Parameter(Mandatory = $true)][string]$Method,
        [Parameter(Mandatory = $true)][System.Net.NetworkCredential]$Credential
    )

    $request = [System.Net.FtpWebRequest]::Create($Uri)
    $request.Credentials = $Credential
    $request.Method = $Method
    $request.UseBinary = $true
    $request.UsePassive = $true
    $request.KeepAlive = $false
    $request.Timeout = 600000
    $request.ReadWriteTimeout = 600000
    return $request
}

function Invoke-FtpCommand {
    param(
        [Parameter(Mandatory = $true)][string]$BaseUri,
        [Parameter(Mandatory = $true)][string]$RelativePath,
        [Parameter(Mandatory = $true)][string]$Method,
        [Parameter(Mandatory = $true)][System.Net.NetworkCredential]$Credential
    )

    $target = ($BaseUri.TrimEnd('/') + '/' + (Normalize-PathSegment -Path $RelativePath))
    $request = New-FtpRequest -Uri $target -Method $Method -Credential $Credential
    $response = $null
    try {
        $response = $request.GetResponse()
    }
    catch [System.Net.WebException] {
        if ($Method -eq [System.Net.WebRequestMethods+Ftp]::MakeDirectory) {
            $ftpResponse = $_.Exception.Response
            if ($ftpResponse -and $ftpResponse.StatusCode -eq [System.Net.FtpStatusCode]::ActionNotTakenFileUnavailable) {
                return
            }
        }

        throw
    }
    finally {
        if ($response) {
            $response.Close()
        }
    }
}

function Ensure-FtpDirectory {
    param(
        [Parameter(Mandatory = $true)][string]$BaseUri,
        [Parameter(Mandatory = $true)][string]$RelativeDir,
        [Parameter(Mandatory = $true)][System.Net.NetworkCredential]$Credential
    )

    $clean = Normalize-PathSegment -Path $RelativeDir
    if ([string]::IsNullOrWhiteSpace($clean)) {
        return
    }

    $segments = $clean -split '/'
    $current = @()
    foreach ($segment in $segments) {
        if ([string]::IsNullOrWhiteSpace($segment)) {
            continue
        }

        $current += $segment
        $path = $current -join '/'
        try {
            Invoke-FtpCommand -BaseUri $BaseUri -RelativePath $path -Method ([System.Net.WebRequestMethods+Ftp]::MakeDirectory) -Credential $Credential
        }
        catch {
            $ftpResponse = $_.Exception.Response
            if (-not ($ftpResponse -and $ftpResponse.StatusCode -eq [System.Net.FtpStatusCode]::ActionNotTakenFileUnavailable)) {
                throw
            }
        }
    }
}

function Upload-FtpFile {
    param(
        [Parameter(Mandatory = $true)][string]$LocalPath,
        [Parameter(Mandatory = $true)][string]$BaseUri,
        [Parameter(Mandatory = $true)][string]$RemotePath,
        [Parameter(Mandatory = $true)][System.Net.NetworkCredential]$Credential
    )

    $target = ($BaseUri.TrimEnd('/') + '/' + (Normalize-PathSegment -Path $RemotePath))
    $request = New-FtpRequest -Uri $target -Method ([System.Net.WebRequestMethods+Ftp]::UploadFile) -Credential $Credential
    $request.ContentLength = (Get-Item -LiteralPath $LocalPath).Length

    $buffer = New-Object byte[] 65536
    $fileStream = [System.IO.File]::OpenRead($LocalPath)
    $requestStream = $null
    $response = $null
    try {
        $requestStream = $request.GetRequestStream()
        while (($read = $fileStream.Read($buffer, 0, $buffer.Length)) -gt 0) {
            $requestStream.Write($buffer, 0, $read)
        }

        $requestStream.Flush()
        $response = $request.GetResponse()
    }
    finally {
        if ($requestStream) {
            $requestStream.Close()
        }
        if ($response) {
            $response.Close()
        }
        $fileStream.Close()
    }
}

$repoRoot = Get-RepoRoot
$config = Read-JsonFile -Path (Resolve-Path $ConfigPath).Path

if ([string]::IsNullOrWhiteSpace([string]$config.pack_filename)) {
    $config.pack_filename = 'project.pack'
}

if ([string]::IsNullOrWhiteSpace([string]$config.unpack_token)) {
    $config.unpack_token = New-RandomToken
    Write-JsonFile -Object $config -Path (Resolve-Path $ConfigPath).Path
    Write-Host "Generated a new unpack token and saved it to deploy.pack.json."
}

$packPath = Join-Path $repoRoot $config.pack_filename
if (Test-Path -LiteralPath $packPath) {
    Remove-Item -LiteralPath $packPath -Force
}

$files = Get-ChildItem -LiteralPath $repoRoot -Recurse -Force -File | Where-Object {
    $relative = $_.FullName.Substring($repoRoot.Length).TrimStart('\', '/')
    -not (Test-ExcludedPath -RelativePath $relative)
}

Write-Host "Building pack archive: $packPath"

$zip = [System.IO.Compression.ZipFile]::Open($packPath, [System.IO.Compression.ZipArchiveMode]::Create)
try {
    foreach ($file in $files) {
        $relative = $file.FullName.Substring($repoRoot.Length).TrimStart('\', '/')
        $entryName = $relative -replace '\\', '/'
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
            $zip,
            $file.FullName,
            $entryName,
            [System.IO.Compression.CompressionLevel]::Optimal
        ) | Out-Null
    }
}
finally {
    $zip.Dispose()
}

$packInfo = Get-Item -LiteralPath $packPath
Write-Host "Pack created: $($packInfo.FullName) [$([Math]::Round($packInfo.Length / 1MB, 2)) MB]"

if ($BuildOnly) {
    Write-Host "Build-only mode enabled. Skipping FTP upload."
    exit 0
}

$ftpHost = [string]$config.ftp_host
$ftpPort = [int]$config.ftp_port
$ftpUser = [string]$config.ftp_user
$ftpPassword = [string]$config.ftp_password

if ([string]::IsNullOrWhiteSpace($ftpHost) -or [string]::IsNullOrWhiteSpace($ftpUser) -or [string]::IsNullOrWhiteSpace($ftpPassword)) {
    throw "FTP host, user, and password are required in deploy.pack.json."
}

$credential = New-Object System.Net.NetworkCredential($ftpUser, $ftpPassword)
    $remoteRoot = Normalize-PathSegment -Path ([string]$config.ftp_remote_dir)
    $baseUri = New-FtpBaseUri -Host $ftpHost -Port $ftpPort

$remoteConfig = [pscustomobject]@{
        pack_filename      = [string]$config.pack_filename
        unpack_token       = [string]$config.unpack_token
        remote_extract_dir = [string]$config.remote_extract_dir
    }

$remoteConfigPath = Join-Path $env:TEMP ("deploy.remote.{0}.json" -f ([guid]::NewGuid().ToString('N')))
Write-JsonFile -Object $remoteConfig -Path $remoteConfigPath

try {
    if (-not [string]::IsNullOrWhiteSpace($remoteRoot)) {
        Ensure-FtpDirectory -BaseUri $baseUri -RelativeDir $remoteRoot -Credential $credential
    }

    Ensure-FtpDirectory -BaseUri $baseUri -RelativeDir (Join-RemotePath @($remoteRoot, 'public')) -Credential $credential

    Upload-FtpFile -LocalPath $packPath -BaseUri $baseUri -RemotePath (Join-RemotePath @($remoteRoot, [string]$config.pack_filename)) -Credential $credential
    Upload-FtpFile -LocalPath $remoteConfigPath -BaseUri $baseUri -RemotePath (Join-RemotePath @($remoteRoot, 'deploy.remote.json')) -Credential $credential
    Upload-FtpFile -LocalPath (Join-Path $repoRoot 'public\unpack.php') -BaseUri $baseUri -RemotePath (Join-RemotePath @($remoteRoot, 'public', 'unpack.php')) -Credential $credential
}
finally {
    if (Test-Path -LiteralPath $remoteConfigPath) {
        Remove-Item -LiteralPath $remoteConfigPath -Force
    }
}

$unpackUrl = $null
if (-not [string]::IsNullOrWhiteSpace([string]$config.domain)) {
    $unpackUrl = ([string]$config.domain).TrimEnd('/') + '/unpack.php?token=' + [System.Uri]::EscapeDataString([string]$config.unpack_token)
}

Write-Host "Upload complete."
Write-Host "Pack file: $packPath"
Write-Host "Remote unpack script: public/unpack.php"
if ($unpackUrl) {
    Write-Host "Trigger URL: $unpackUrl"
}
