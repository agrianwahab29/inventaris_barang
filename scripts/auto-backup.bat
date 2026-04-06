@echo off
REM Skrip backup otomatis untuk inventaris-kantor (Windows)
REM Cara pakai: scripts\auto-backup.bat

cd /d "%~dp0\.."

REM Periksa apakah ada perubahan
for /f "tokens=*" %%a in ('git status --porcelain') do (
    echo 📦 Perubahan terdeteksi, membuat backup...
    
    REM Tambahkan semua perubahan
    git add .
    
    REM Commit dengan timestamp
    for /f "tokens=2-4 delims=/ " %%b in ('date /t') do (
        for /f "tokens=1-2 delims=: " %%e in ('time /t') do (
            git commit -m "auto-backup: %%c-%%b-%%d %%e:%%f"
        )
    )
    
    REM Dapatkan branch aktif
    for /f "tokens=*" %%i in ('git rev-parse --abbrev-ref HEAD') do (
        set current_branch=%%i
    )
    
    REM Push ke GitHub
    git push origin %current_branch%
    
    echo ✅ Backup selesai dan push ke GitHub
    echo 📍 Branch: %current_branch%
    goto :eof
)

echo ℹ️ Tidak ada perubahan untuk di-backup
