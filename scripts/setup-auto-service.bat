@echo off
REM Setup Auto-Watch as Windows Service
REM Run as Administrator

echo ==========================================
echo Setup Auto-Watch Git Backup Service
echo ==========================================
echo.

REM Check if running as admin
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Please run as Administrator!
    pause
    exit /b 1
)

set REPO_PATH=C:\laragon\www\inventaris-barang2\inventaris-kantor
set SCRIPT_PATH=%REPO_PATH%\scripts\auto-watch.bat

echo 📍 Repository: %REPO_PATH%
echo 📝 Script: %SCRIPT_PATH%
echo.

REM Create scheduled task to run every 5 minutes
echo Creating scheduled task...
schtasks /create /tn "GitAutoWatch" /tr "%SCRIPT_PATH%" /sc minute /mo 5 /rl highest /f

if %errorlevel% == 0 (
    echo ✅ Scheduled task created successfully!
    echo.
    echo Task will run every 5 minutes
    echo.
    echo To start now:
    echo    schtasks /run /tn "GitAutoWatch"
    echo.
    echo To stop:
    echo    schtasks /end /tn "GitAutoWatch"
    echo.
    echo To delete:
    echo    schtasks /delete /tn "GitAutoWatch" /f
) else (
    echo ❌ Failed to create scheduled task
)

echo.
pause
