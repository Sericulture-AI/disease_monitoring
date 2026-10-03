@echo off
REM Pushes this folder to GitHub: https://github.com/Sericulture-AI/disease_monitoring
REM Double-click after every change. A browser window may ask you to sign in to GitHub the first time.
cd /d "%~dp0"
where git >nul 2>nul || (echo Git is not installed. Get it from https://git-scm.com/download/win & pause & exit /b 1)
set REPO=https://github.com/Sericulture-AI/disease_monitoring.git
if not exist ".git" git init -b main
git remote get-url origin >nul 2>nul && git remote set-url origin %REPO% || git remote add origin %REPO%
git config user.name >nul 2>nul || git config user.name "Selvaprakash R"
git config user.email >nul 2>nul || git config user.email "selvaprakashak@gmail.com"
git branch -M main
git add -A
git commit -m "Update disease monitoring %date% %time%" || echo Nothing new to commit.
git push -u origin main || (echo GitHub already has files - merging them in, keeping this folder's versions... & git pull --no-rebase --allow-unrelated-histories -X ours --no-edit origin main && git push -u origin main)
echo.
if errorlevel 1 (echo PUSH FAILED - read the message above.) else (echo Done. Open https://github.com/Sericulture-AI/disease_monitoring)
pause
