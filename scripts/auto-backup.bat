@echo off
REM Auto-backup script untuk inventaris-kantor (Windows)
REM Usage: scripts\auto-backup.bat

cd /d "%~dp0\.."

REM Check apakah ada perubahan
for /f "tokens=*" %%a in ('git status --porcelain') do (
    echo 📦 Changes detected, creating backup...
    
    REM Add semua perubahan
    git add .
    
    REM Commit dengan timestamp
    for /f "tokens=2-4 delims=/ " %%b in ('date /t') do (
        for /f "tokens=1-2 delims=: " %%e in ('time /t') do (
            git commit -m "auto-backup: %%c-%%b-%%d %%e:%%f"
        )
    )
    
    REM Get current branch
    for /f "tokens=*" %%i in ('git rev-parse --abbrev-ref HEAD') do (
        set current_branch=%%i
    )
    
    REM Push ke GitHub
    git push origin %current_branch%
    
    echo ✅ Backup completed and pushed to GitHub
    echo 📍 Branch: %current_branch%
    goto :eof
)

echo ℹ️ No changes to backup
