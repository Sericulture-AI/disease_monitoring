@echo off
REM Use this if the push went to the wrong place or failed.
REM It asks for the repository URL you created on GitHub, points this folder at it, and pushes.
cd /d "%~dp0"
where git >/dev/null 2>/dev/null || (echo Git is not installed. Get it from https://git-scm.com/download/win & pause & exit /b 1)
if not exist ".git" git init -b main
echo Current remote:
git remote -v
echo.
set /p URL=Paste your GitHub repository URL (e.g. https://github.com/NAME/disease-monitoring.git): 
if "%URL%"=="" (echo No URL entered. & pause & exit /b 1)
git remote remove origin 2>nul
git remote add origin %URL%
git branch -M main
git add -A
git commit -m "Disease monitoring system" 2>/dev/null || echo Nothing new to commit.
git push -u origin main
echo.
if errorlevel 1 (echo PUSH FAILED - read the message above.) else (echo Done. Open %URL:.git=%)
pause
