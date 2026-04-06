@echo off
REM Skrip pemantau otomatis untuk Windows - memantau perubahan file dan melakukan commit/push otomatis
REM Cara pakai: scripts\auto-watch.bat (jalankan dan biarkan window tetap terbuka)

cd /d "%~dp0\.."

for /f "tokens=*" %%i in ('git rev-parse --abbrev-ref HEAD') do (
    set current_branch=%%i
)

echo 👁️  Pemantauan otomatis dimulai - Memantau perubahan file...
echo 📍 Lokasi repository: %cd%
echo 🌿 Branch aktif: %current_branch%
echo.
echo ⚠️  Jangan tutup window ini! Biarkan berjalan di background.
echo.

:loop
REM Periksa apakah ada perubahan
for /f "tokens=*" %%a in ('git status --porcelain') do (
    echo 📦 Perubahan terdeteksi pada %date% %time%
    
    REM Tambahkan semua perubahan
    git add .
    
    REM Commit dengan timestamp
    git commit -m "auto: backup pada %date% %time%"
    
    REM Push ke GitHub
    git push origin %current_branch%
    
    if %errorlevel% == 0 (
        echo ✅ Berhasil push otomatis ke GitHub - Branch: %current_branch%
    ) else (
        echo ❌ Gagal push - akan mencoba lagi pada perubahan berikutnya
    )
    echo.
    
    goto :continue
)

:continue
REM Tunggu 30 detik sebelum memeriksa lagi
timeout /t 30 /nobreak >nul
goto :loop
