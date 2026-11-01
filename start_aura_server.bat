@echo off
title AURA Backend Server
echo ========================================================
echo   AURA Life System - Backend Server
echo ========================================================
echo.
cd /d "%~dp0\backend"
echo Menjalankan server AURA di http://0.0.0.0:8000 ...
echo Tekan Ctrl+C untuk menghentikan server.
echo.
php artisan serve --host=0.0.0.0 --port=8000
pause
