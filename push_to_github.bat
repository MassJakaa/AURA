@echo off
title Push AURA to GitHub
echo ========================================================
echo   Mengunggah AURA ke GitHub...
echo ========================================================
echo.
git push -u origin main
echo.
if %ERRORLEVEL% EQU 0 (
    echo [SUKSES] Seluruh kode AURA berhasil di-upload ke GitHub!
) else (
    echo [INFO] Jika gagal, pastikan Anda sudah membuat repositori kosong di:
    echo        https://github.com/new
)
echo.
pause
