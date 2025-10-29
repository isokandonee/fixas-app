@echo off
echo Setting up testing environment...

:: Check for PHP installation
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo PHP is not installed. Please install PHP 7.4 or higher:
    echo 1. Download PHP from https://windows.php.net/download/
    echo 2. Extract to C:\php
    echo 3. Add C:\php to your PATH environment variable
    echo 4. Copy php.ini-development to php.ini
    echo 5. Enable required extensions in php.ini:
    echo    - extension=pdo_mysql
    echo    - extension=mysqli
    echo    - extension=mbstring
    echo    - extension=openssl
    pause
    exit /b 1
)

:: Check for Composer installation
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Composer is not installed. Please install Composer:
    echo 1. Download from https://getcomposer.org/Composer-Setup.exe
    echo 2. Run the installer
    echo 3. Follow the installation wizard
    pause
    exit /b 1
)

:: Check for MySQL installation
where mysql >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo MySQL is not installed. Please install MySQL:
    echo 1. Download MySQL Community Server from https://dev.mysql.com/downloads/mysql/
    echo 2. Run the installer
    echo 3. Follow the installation wizard
    echo 4. Remember to note down the root password
    pause
    exit /b 1
)

echo Checking PHP version...
php -v

echo.
echo Checking Composer version...
composer -V

echo.
echo Setting up test database...
echo Please enter your MySQL root password:
set /p MYSQL_PASSWORD=

mysql -u root -p%MYSQL_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS fixas_bank_test;"
mysql -u root -p%MYSQL_PASSWORD% fixas_bank_test < db/user.sql

echo.
echo Installing project dependencies...
composer install

echo.
echo Running tests...
vendor\bin\phpunit

pause