@echo off
title AURA Web Companion Server
echo ========================================================
echo   AURA Virtual Companion - Web Application Server
echo ========================================================
echo.
cd /d "%~dp0\backend"

if not exist ".env" (
    echo [INFO] Menyalin .env.example ke .env...
    copy .env.example .env
    call php artisan key:generate
)

if not exist "database\database.sqlite" (
    echo [INFO] Membuat database SQLite...
    type nul > database\database.sqlite
    call php artisan migrate --force
    call php artisan db:seed --force
)

echo [INFO] Menjalankan server AURA di http://localhost:8000 ...
echo Akses AURA di browser PC / Handphone melalui: http://localhost:8000
echo Tekan Ctrl+C untuk menghentikan server.
echo.
php artisan serve --host=0.0.0.0 --port=8000
pause
