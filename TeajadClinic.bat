@echo off
echo Starting the Dental Clinic System...
echo Please do not close this window while using the system.

:: 1. Start the Laravel Server in the background
start /min cmd /k "php artisan serve"

:: 2. Wait 3 seconds for the server to wake up
timeout /t 3 /nobreak >nul

:: 3. Open the Chrome Browser (or default browser) automatically
start http://127.0.0.1:8000

:: 4. Keep this window open so they know it's running
echo System is running! Minimizing this window...