@echo off
REM Auto-watch script for Windows - monitors file changes and auto-commits/pushes
REM Usage: scripts\auto-watch.bat (run and keep window open)

cd /d "%~dp0\.."

for /f "tokens=*" %%i in ('git rev-parse --abbrev-ref HEAD') do (
    set current_branch=%%i
)

echo 👁️  Auto-watch started - Monitoring for changes...
echo 📍 Repository: %cd%
echo 🌿 Branch: %current_branch%
echo.
echo ⚠️  Jangan tutup window ini! Biarkan berjalan di background.
echo.

:loop
REM Check if there are changes
for /f "tokens=*" %%a in ('git status --porcelain') do (
    echo 📦 Changes detected at %date% %time%
    
    REM Add all changes
    git add .
    
    REM Commit with timestamp
    git commit -m "auto: backup at %date% %time%"
    
    REM Push to GitHub
    git push origin %current_branch%
    
    if %errorlevel% == 0 (
        echo ✅ Auto-pushed to GitHub - Branch: %current_branch%
    ) else (
        echo ❌ Push failed - will retry on next change
    )
    echo.
    
    goto :continue
)

:continue
REM Wait 30 seconds before checking again
timeout /t 30 /nobreak >nul
goto :loop
