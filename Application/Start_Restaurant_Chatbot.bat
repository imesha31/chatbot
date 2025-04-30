@echo off
echo Starting Restaurant Chatbot...
echo.
echo Please wait while services are starting...

cd /d "%~dp0"

REM Start Apache and MySQL
start /min app\xampp-control.exe

REM Wait for services to start
timeout /t 10 /nobreak > nul

REM Open the application in the default browser
start http://localhost/restaurant_chatbot/

echo.
echo Restaurant Chatbot is now running!
echo Please do not close this window while using the application.
echo To stop the application, close this window and the browser.
echo.
pause