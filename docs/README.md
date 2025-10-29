# Fixas-Bank Application Documentation

## Table of Contents
1. [Overview](#overview)
2. [System Requirements](#system-requirements)
3. [Installation](#installation)
4. [Project Structure](#project-structure)
5. [Configuration](#configuration)
6. [API Documentation](#api-documentation)
7. [Database Schema](#database-schema)
8. [Security Features](#security-features)
9. [Testing](#testing)
10. [Deployment](#deployment)

## Overview
Fixas-Bank is a secure banking application that provides essential banking operations including account creation, deposits, and withdrawals. The application is built with PHP and uses MySQL for data storage.

## System Requirements
- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx web server
- SSL certificate (for production)
- Composer (for dependency management)
- PHPUnit (for testing)

## Installation
1. Clone the repository:
```bash
git clone https://github.com/isokandonee/fixas-app.git
cd fixas-app
```

2. Install dependencies:
```bash
composer install
```

3. Configure database:
- Create a new MySQL database
- Import the schema from `db/user.sql`
- Update database credentials in `controller/connect.php`

4. Configure web server:
- Set document root to project root directory
- Enable URL rewriting
- Configure SSL (for production)

## Project Structure
```
fixas-app/
├── controller/        # Application controllers
│   ├── account.php   # Account management
│   ├── connect.php   # Database connection
│   ├── deposit.php   # Deposit handling
│   ├── login.php     # Authentication
│   └── withdraw.php  # Withdrawal handling
├── css/              # Stylesheets
├── dashboard/        # Dashboard views
├── db/               # Database scripts
├── docs/             # Documentation
├── include/          # Shared components
├── script/          # JavaScript files
└── tests/           # Test suites
```

## Configuration
### Database Configuration
Located in `controller/connect.php`:
```php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "fixas_bank";
```

### Session Configuration
Session settings in `include/header.php`:
- Session timeout
- Security settings
- Cookie parameters

## API Documentation
### Account Management
#### Create Account
- Endpoint: `/controller/account.php`
- Method: POST
- Parameters:
  - account_name (string)
  - account_type (string)
  - initial_deposit (float)

#### Deposit
- Endpoint: `/controller/deposit.php`
- Method: POST
- Parameters:
  - account_number (string)
  - amount (float)

#### Withdraw
- Endpoint: `/controller/withdraw.php`
- Method: POST
- Parameters:
  - account_number (string)
  - amount (float)

## Database Schema
### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP
);
```

### Accounts Table
```sql
CREATE TABLE accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    account_number VARCHAR(10) UNIQUE,
    account_type VARCHAR(20),
    balance DECIMAL(10,2),
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Transactions Table
```sql
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT,
    type ENUM('deposit', 'withdrawal'),
    amount DECIMAL(10,2),
    created_at TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
);
```

## Security Features
1. Authentication
   - Session-based authentication
   - Password hashing using bcrypt
   - CSRF protection
   - Rate limiting

2. Input Validation
   - Server-side validation
   - Prepared statements
   - XSS prevention
   - SQL injection prevention

3. Session Security
   - Secure session configuration
   - Session timeout
   - HTTP-only cookies
   - Secure cookie flags

## Testing
Tests are located in the `tests/` directory and can be run using PHPUnit.

### Running Tests
```bash
./vendor/bin/phpunit tests
```

### Test Coverage
- Unit Tests: Controllers, Models
- Integration Tests: API endpoints
- Security Tests: Authentication, Authorization
- Performance Tests: Load testing

## Deployment
1. Production Environment Setup
   - Configure SSL certificate
   - Set up production database
   - Configure firewall rules

2. Deployment Checklist
   - Update dependencies
   - Run tests
   - Backup database
   - Deploy code
   - Verify security settings

3. Monitoring
   - Error logging
   - Performance monitoring
   - Security monitoring