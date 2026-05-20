@echo off
setlocal

set "APP_ROOT=%~dp0.."
set "PHP_EXE=C:\xampp\php\php.exe"
if not exist "%PHP_EXE%" set "PHP_EXE=php"

if not exist "%APP_ROOT%\database\database.sqlite" (
  echo Missing database file: "%APP_ROOT%\database\database.sqlite"
  exit /b 1
)

cd /d "%APP_ROOT%"
set "DB_CONNECTION=sqlite"
set "DB_DATABASE=%APP_ROOT%\database\database.sqlite"
set "APP_URL=http://127.0.0.1:8000"

echo Starting Laravel on http://127.0.0.1:8000
echo Using SQLite database at "%DB_DATABASE%"
echo Press Ctrl+C to stop.
"%PHP_EXE%" -S 127.0.0.1:8000 -t public public/router.php
