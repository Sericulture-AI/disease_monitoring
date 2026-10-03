@echo off
REM Pushes this folder to GitHub (private repo Sericulture-AI/disease-monitoring).
REM 1) First create an EMPTY private repo at https://github.com/new
REM    Owner: Sericulture-AI   Name: disease-monitoring   (no README, no .gitignore)
REM 2) Double-click this file. A browser window may ask you to sign in to GitHub once.
cd /d "%~dp0"
where git >nul 2>nul || (echo Git is not installed. Get it from https://git-scm.com/download/win & pause & exit /b 1)
if not exist ".git" (
  git init -b main
  git remote add origin https://github.com/Sericulture-AI/disease-monitoring.git
)
git config user.name >nul 2>nul || git config user.name "Selvaprakash R"
git config user.email >nul 2>nul || git config user.email "selvaprakashak@gmail.com"
git add -A
git commit -m "Update disease monitoring %date% %time%" || echo Nothing new to commit.
git push -u origin main
echo.
echo Done. Open https://github.com/Sericulture-AI/disease-monitoring
pause
