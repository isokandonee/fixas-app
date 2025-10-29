-- phpMyAdmin SQL Dump
-- Enhanced version with security features and audit trails

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fixas_bank`
--
CREATE DATABASE IF NOT EXISTS `fixas_bank` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fixas_bank`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Stores hashed password',
  `phone` varchar(20) NOT NULL,
  `two_factor_secret` varchar(32) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_password_change` datetime DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT '0',
  `email_verification_token` varchar(100) DEFAULT NULL,
  `status` enum('active','suspended','locked') DEFAULT 'active',
  `failed_login_attempts` int(11) DEFAULT '0',
  `lockout_until` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `minimum_balance` decimal(15,2) DEFAULT '0.00',
  `interest_rate` decimal(5,2) DEFAULT '0.00',
  `daily_transaction_limit` decimal(15,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `account_statuses`
--

CREATE TABLE `account_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `user_accounts`
--

CREATE TABLE `user_accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_type_id` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `available_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` char(3) NOT NULL DEFAULT 'NGN',
  `status_id` int(11) NOT NULL,
  `last_transaction_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transaction_types`
--

CREATE TABLE `transaction_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `transaction_type_id` int(11) NOT NULL,
  `source_account_id` int(11) NOT NULL,
  `destination_account_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'NGN',
  `exchange_rate` decimal(15,6) DEFAULT '1.000000',
  `status` enum('pending','completed','failed','reversed') NOT NULL DEFAULT 'pending',
  `description` text,
  `metadata` json DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `status` enum('success','failed') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `session_tokens`
--

CREATE TABLE `session_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `status_index` (`status`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `account_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `user_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_type_id` (`account_type_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

ALTER TABLE `transaction_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `transaction_type_id` (`transaction_type_id`),
  ADD KEY `source_account_id` (`source_account_id`),
  ADD KEY `destination_account_id` (`destination_account_id`),
  ADD KEY `status_index` (`status`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `approved_by` (`approved_by`);

ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_record` (`table_name`,`record_id`);

ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `email` (`email`);

ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `session_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `account_types` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `account_statuses` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_accounts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `transaction_types` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `transactions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `audit_logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `login_attempts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `password_history` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `session_tokens` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Initial Data
--

INSERT INTO `account_types` (`name`, `description`, `minimum_balance`, `interest_rate`) VALUES
('savings', 'Standard savings account', 1000.00, 2.50),
('current', 'Business current account', 5000.00, 0.00),
('domiciliary', 'Foreign currency account', 0.00, 0.50);

INSERT INTO `account_statuses` (`name`, `description`) VALUES
('active', 'Account is active and can perform transactions'),
('inactive', 'Account is temporarily inactive'),
('blocked', 'Account is blocked due to suspicious activity'),
('frozen', 'Account is frozen by bank authority'),
('closed', 'Account is permanently closed');

INSERT INTO `transaction_types` (`name`, `description`) VALUES
('deposit', 'Cash or transfer deposit'),
('withdrawal', 'Cash or transfer withdrawal'),
('transfer', 'Account to account transfer'),
('reversal', 'Transaction reversal'),
('charge', 'Bank charges and fees');

--
-- Constraints for dumped tables
--

ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `users_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

ALTER TABLE `user_accounts`
  ADD CONSTRAINT `accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `accounts_type_id` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`),
  ADD CONSTRAINT `accounts_status_id` FOREIGN KEY (`status_id`) REFERENCES `account_statuses` (`id`),
  ADD CONSTRAINT `accounts_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `accounts_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_type_id` FOREIGN KEY (`transaction_type_id`) REFERENCES `transaction_types` (`id`),
  ADD CONSTRAINT `transactions_source` FOREIGN KEY (`source_account_id`) REFERENCES `user_accounts` (`id`),
  ADD CONSTRAINT `transactions_destination` FOREIGN KEY (`destination_account_id`) REFERENCES `user_accounts` (`id`),
  ADD CONSTRAINT `transactions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `login_attempts`
  ADD CONSTRAINT `login_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `password_history`
  ADD CONSTRAINT `password_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `session_tokens`
  ADD CONSTRAINT `session_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Triggers
--

DELIMITER //

CREATE TRIGGER `before_update_user_accounts`
BEFORE UPDATE ON `user_accounts`
FOR EACH ROW
BEGIN
    SET NEW.available_balance = NEW.balance - 
        COALESCE((SELECT minimum_balance FROM account_types WHERE id = NEW.account_type_id), 0);
END //

CREATE TRIGGER `after_insert_users`
AFTER INSERT ON `users`
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, table_name, record_id, new_values)
    VALUES (NEW.created_by, 'INSERT', 'users', NEW.id, 
        JSON_OBJECT(
            'first_name', NEW.first_name,
            'last_name', NEW.last_name,
            'email', NEW.email,
            'phone', NEW.phone,
            'status', NEW.status
        ));
END //

CREATE TRIGGER `after_update_users`
AFTER UPDATE ON `users`
FOR EACH ROW
BEGIN
    IF OLD.first_name != NEW.first_name OR 
       OLD.last_name != NEW.last_name OR 
       OLD.email != NEW.email OR 
       OLD.phone != NEW.phone OR 
       OLD.status != NEW.status THEN
        INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values)
        VALUES (NEW.updated_by, 'UPDATE', 'users', NEW.id,
            JSON_OBJECT(
                'first_name', OLD.first_name,
                'last_name', OLD.last_name,
                'email', OLD.email,
                'phone', OLD.phone,
                'status', OLD.status
            ),
            JSON_OBJECT(
                'first_name', NEW.first_name,
                'last_name', NEW.last_name,
                'email', NEW.email,
                'phone', NEW.phone,
                'status', NEW.status
            ));
    END IF;
END //

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;