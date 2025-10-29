@echo off
echo Cleaning up and reorganizing project structure...

:: Create backup of current state
echo Creating backup...
xcopy /E /I "." "backup-%date:~-4,4%%date:~-10,2%%date:~-7,2%" > nul

:: Move core PHP files to src
echo Moving controllers...
move controller\*.php src\Controllers\ > nul 2>&1

echo Moving models...
move db\*.php src\Models\ > nul 2>&1

echo Moving views...
move dashboard\*.php src\Views\ > nul 2>&1
move include\*.php src\Views\ > nul 2>&1

:: Move public assets
echo Moving assets...
move css\*.* public\assets\css\ > nul 2>&1
move script\*.* public\assets\js\ > nul 2>&1

:: Move configuration files
echo Moving configuration files...
move *.env config\ > nul 2>&1
move connect.php config\ > nul 2>&1

:: Move entry points to public
echo Moving public files...
move index.php public\ > nul 2>&1
move login.php public\ > nul 2>&1
move reset-password.php public\ > nul 2>&1
move new-password.php public\ > nul 2>&1
move setup-2fa.php public\ > nul 2>&1
move approve-transactions.php public\ > nul 2>&1
move audit-trail.php public\ > nul 2>&1

:: Clean up empty directories
echo Cleaning up empty directories...
for /f "delims=" %%d in ('dir /s /b /ad ^| sort /r') do rd "%%d" 2>nul

:: Remove unrelated files and folders
echo Removing unrelated files...
del "2024 06 - JUNE 2024  NATIONAL SALARY.xlsx" > nul 2>&1
rd /s /q "LD85 NEW BOARD" > nul 2>&1

:: Create new .gitignore
echo Creating .gitignore...
echo /vendor/ > .gitignore
echo .env >> .gitignore
echo /node_modules/ >> .gitignore
echo .DS_Store >> .gitignore
echo debug.log >> .gitignore
echo /logs/ >> .gitignore
echo /backup-*/ >> .gitignore
echo /coverage/ >> .gitignore
echo .phpunit.result.cache >> .gitignore

:: Update composer.json autoload paths
echo Updating composer.json...
echo {
echo     "name": "isokandonee/fixas-app",
echo     "description": "A secure banking application",
echo     "type": "project",
echo     "require": {
echo         "php": "^7.4",
echo         "ext-pdo": "*"
echo     },
echo     "require-dev": {
echo         "phpunit/phpunit": "^9.5",
echo         "phpstan/phpstan": "^1.10",
echo         "squizlabs/php_codesniffer": "^3.7"
echo     },
echo     "autoload": {
echo         "psr-4": {
echo             "App\\": "src/"
echo         }
echo     },
echo     "autoload-dev": {
echo         "psr-4": {
echo             "Tests\\": "tests/"
echo         }
echo     },
echo     "scripts": {
echo         "test": "phpunit",
echo         "test-coverage": "phpunit --coverage-html coverage",
echo         "check": [
echo             "@test",
echo             "@cs",
echo             "@stan"
echo         ],
echo         "cs": "phpcs",
echo         "cs-fix": "phpcbf",
echo         "stan": "phpstan analyse"
echo     }
echo } > composer.json

echo Creating README.md...
echo # Fixas-Bank Application > README.md
echo. >> README.md
echo A secure banking application with modern PHP practices. >> README.md
echo. >> README.md
echo ## Project Structure >> README.md
echo. >> README.md
echo ```plaintext >> README.md
echo fixas-app/ >> README.md
echo ├── config/           # Configuration files >> README.md
echo ├── public/          # Public files and assets >> README.md
echo │   ├── assets/     # CSS, JavaScript, images >> README.md
echo │   └── index.php   # Application entry point >> README.md
echo ├── src/            # Application source code >> README.md
echo │   ├── Controllers/ # Application controllers >> README.md
echo │   ├── Models/     # Database models >> README.md
echo │   └── Views/      # Template files >> README.md
echo ├── tests/          # Test suites >> README.md
echo └── vendor/         # Composer dependencies >> README.md
echo ``` >> README.md

echo Done! Your project has been cleaned and reorganized.
echo A backup has been created in case you need to revert changes.
pause