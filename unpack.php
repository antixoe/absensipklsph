<?php

declare(strict_types=1);

set_time_limit(0);

function respond(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

function loadDeployConfig(string $projectRoot): array
{
    $paths = [
        $projectRoot . DIRECTORY_SEPARATOR . 'deploy.remote.json',
        $projectRoot . DIRECTORY_SEPARATOR . 'deploy.pack.json',
    ];

    foreach ($paths as $path) {
        if (!is_file($path)) {
            continue;
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    respond(500, [
        'success' => false,
        'message' => 'Deployment config not found.',
    ]);

    return [];
}

function normalizeRelativePath(string $path): string
{
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#/+#', '/', $path) ?? $path;
    return ltrim($path, '/');
}

function isSafeRelativePath(string $path): bool
{
    if ($path === '' || str_contains($path, "\0")) {
        return false;
    }

    if (preg_match('~^[A-Za-z]:~', $path) === 1) {
        return false;
    }

    foreach (explode('/', $path) as $segment) {
        if ($segment === '' || $segment === '.' || $segment === '..') {
            return false;
        }
    }

    return true;
}

function joinPath(string $base, string $relative): string
{
    return rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
}

function createDirectoryIfNeeded(string $path): void
{
    if (is_dir($path)) {
        return;
    }

    if (!mkdir($path, 0775, true) && !is_dir($path)) {
        throw new RuntimeException('Unable to create directory: ' . $path);
    }
}

function extractPack(string $archivePath, string $destination): int
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('ZipArchive extension is not available on this server.');
    }

    $zip = new ZipArchive();
    if ($zip->open($archivePath) !== true) {
        throw new RuntimeException('Unable to open archive: ' . basename($archivePath));
    }

    createDirectoryIfNeeded($destination);

    $extracted = 0;
    try {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false) {
                continue;
            }

            $name = normalizeRelativePath($name);
            if (!isSafeRelativePath($name)) {
                throw new RuntimeException('Unsafe path found in archive: ' . $name);
            }

            $targetPath = joinPath($destination, $name);

            if (str_ends_with($name, '/')) {
                createDirectoryIfNeeded($targetPath);
                continue;
            }

            $dir = dirname($targetPath);
            if (!is_dir($dir)) {
                createDirectoryIfNeeded($dir);
            }

            $stream = $zip->getStream($zip->getNameIndex($i));
            if ($stream === false) {
                throw new RuntimeException('Unable to read archive entry: ' . $name);
            }

            $out = fopen($targetPath, 'wb');
            if ($out === false) {
                fclose($stream);
                throw new RuntimeException('Unable to write file: ' . $targetPath);
            }

            stream_copy_to_stream($stream, $out);
            fclose($stream);
            fclose($out);
            $extracted++;
        }
    } finally {
        $zip->close();
    }

    return $extracted;
}

$projectRoot = dirname(__DIR__);
$config = loadDeployConfig($projectRoot);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
    respond(405, [
        'success' => false,
        'message' => 'Only GET is allowed.',
    ]);
}

$token = (string) ($_GET['token'] ?? '');
$expectedToken = (string) ($config['unpack_token'] ?? '');

if ($expectedToken === '') {
    respond(500, [
        'success' => false,
        'message' => 'Unpack token is not configured.',
    ]);
}

if (!hash_equals($expectedToken, $token)) {
    respond(403, [
        'success' => false,
        'message' => 'Invalid token.',
    ]);
}

$packFilename = (string) ($config['pack_filename'] ?? 'project.pack');
$archivePath = joinPath($projectRoot, $packFilename);

if (!is_file($archivePath)) {
    respond(404, [
        'success' => false,
        'message' => 'Pack file not found.',
        'archive' => $packFilename,
    ]);
}

$extractDir = (string) ($config['remote_extract_dir'] ?? '');
if ($extractDir === '') {
    $extractDir = $projectRoot;
} elseif (!str_starts_with($extractDir, DIRECTORY_SEPARATOR)) {
    $extractDir = joinPath($projectRoot, normalizeRelativePath($extractDir));
}

try {
    $count = extractPack($archivePath, $extractDir);
} catch (Throwable $e) {
    respond(500, [
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}

$cleanup = isset($_GET['cleanup']) && ((string) $_GET['cleanup'] === '1' || strtolower((string) $_GET['cleanup']) === 'true');
if ($cleanup && is_file($archivePath)) {
    @unlink($archivePath);
}

respond(200, [
    'success' => true,
    'message' => 'Archive extracted successfully.',
    'archive' => basename($archivePath),
    'extracted_to' => $extractDir,
    'files_extracted' => $count,
    'cleanup' => $cleanup,
]);
