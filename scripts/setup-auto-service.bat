@echo off
REM Setup Auto-Watch sebagai Windows Service
REM Jalankan sebagai Administrator

echo ==========================================
echo Setup Layanan Auto-Watch Git Backup
echo ==========================================
echo.

REM Periksa apakah dijalankan sebagai admin
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Harap jalankan sebagai Administrator!
    pause
    exit /b 1
)

set REPO_PATH=C:\laragon\www\inventaris-barang2\inventaris-kantor
set SCRIPT_PATH=%REPO_PATH%\scripts\auto-watch.bat

echo 📍 Lokasi repository: %REPO_PATH%
echo 📝 Lokasi skrip: %SCRIPT_PATH%
echo.

REM Buat scheduled task untuk berjalan setiap 5 menit
echo Membuat scheduled task...
schtasks /create /tn "GitAutoWatch" /tr "%SCRIPT_PATH%" /sc minute /mo 5 /rl highest /f

if %errorlevel% == 0 (
    echo ✅ Scheduled task berhasil dibuat!
    echo.
    echo Task akan berjalan setiap 5 menit
echo.
    echo Perintah untuk mengontrol:
    echo    schtasks /run /tn "GitAutoWatch"    - Jalankan sekarang
    echo    schtasks /end /tn "GitAutoWatch"     - Hentikan
    echo    schtasks /delete /tn "GitAutoWatch" /f  - Hapus service
) else (
    echo ❌ Gagal membuat scheduled task
)

echo.
pause
