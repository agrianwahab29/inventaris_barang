@echo off
REM Linear API Helper for Windows
REM Usage: linear-helper.bat [teams|create] [title] [description]

REM Konfigurasi - GANTI DENGAN DATA ANDA
set LINEAR_API_KEY=YOUR_LINEAR_API_KEY
set TEAM_ID=YOUR_TEAM_ID

if "%1"=="teams" goto :teams
if "%1"=="create" goto :create
goto :help

:teams
echo Mencari teams...
curl -X POST https://api.linear.app/graphql ^
    -H "Content-Type: application/json" ^
    -H "Authorization: %LINEAR_API_KEY%" ^
    -d "{\"query\": \"query { teams { nodes { id name key } } }\"}"
goto :eof

:create
if "%2"=="" goto :help
if "%3"=="" goto :help

echo Creating issue: %2
curl -X POST https://api.linear.app/graphql ^
    -H "Content-Type: application/json" ^
    -H "Authorization: %LINEAR_API_KEY%" ^
    -d "{\"query\": \"mutation { issueCreate(input: { title: \\\"%2\\\" description: \\\"%3\\\" teamId: \\\"%TEAM_ID%\\\" }) { success issue { id identifier url } } }\"}"
goto :eof

:help
echo Linear API Helper for Windows
echo.
echo Usage:
echo   %0 teams                    - List all teams
echo   %0 create "Title" "Desc"     - Create new issue
echo.
echo Example:
echo   %0 create "[ANALYSIS] Security Audit" "Security audit description..."
echo.
echo NOTE: Edit this file and set LINEAR_API_KEY dan TEAM_ID terlebih dahulu!
