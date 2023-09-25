-- Create a new database if needed
CREATE DATABASE IF NOT EXISTS retail_customers;
USE retail_customers;

-- Create `funds` table
DROP TABLE IF EXISTS `funds`;

CREATE TABLE `funds` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `symbol` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `net_asset_value` decimal(10,2) UNSIGNED NOT NULL,
    `risk_factor` INT UNSIGNED NOT NULL,
    CHECK (risk_factor BETWEEN 1 AND 10),
    `ongoing_charge` decimal(4,2) UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    `deleted_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Populate `funds` table with dummy data
INSERT INTO `funds` (`id`,`symbol`, `name`, `net_asset_value`, `risk_factor`, `ongoing_charge`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1,'CUSH', 'Cushon Equities Fund', 120.25, 5, 1.25, NOW(), NOW(), null),
(2,'AAPL', 'Apple Inc Fund', 150.25, 5, 1.25, NOW(), NOW(), null),
(3,'GOOGL', 'Alphabet Inc Fund', 250.75, 4, 1.50, NOW(), NOW(), null),
(4,'MSFT', 'Microsoft Corp Fund', 300.50, 3, 1.20, NOW(), NOW(), null),
(5,'AMZN', 'Amazon.com Inc Fund', 350.30, 6, 1.75, NOW(), NOW(), null),
(6,'TSLA', 'Tesla Inc Fund', 700.10, 7, 2.00, NOW(), NOW(), null),
(7,'JPM', 'JPMorgan Chase & Co Fund', 120.45, 2, 0.95, NOW(), NOW(), null),
(8,'WMT', 'Walmart Inc Fund', 140.60, 4, 1.10, NOW(), NOW(), null),
(9,'GOOG', 'Alphabet Inc Class C Fund', 260.80, 5, 1.45, NOW(), NOW(), null),
(10,'FB', 'Meta Platforms Inc Fund', 330.20, 6, 1.60, NOW(), NOW(), null),
(11,'NVDA', 'NVIDIA Corp Fund', 220.75, 8, 2.10, NOW(), NOW(), null);

-- Create `investment_transactions` table
DROP TABLE IF EXISTS `investment_transactions`;

CREATE TABLE `investment_transactions` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `account_id` UUID NOT NULL,
    `fund_id` int(11) NOT NULL,
    `amount` decimal(10,2) UNSIGNED NOT NULL,
    `type` ENUM('withdraw', 'deposit') NOT NULL,
    `creted_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create `account_types` table
DROP TABLE IF EXISTS `account_types`;

CREATE TABLE `account_types` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Populate `account_types` table with dummy data
INSERT INTO `account_types` (`id`, `name`, `description`) VALUES
(1, 'Cash ISA', 'A savings account that pays interest free of Income Tax'),
(2, 'Pension', "The World's first Net Zero pension"),
(3, 'Lifetime ISA', 'A tax-free savings account to help first time home buyers aged 18-39 get on the property ladder and/or save money towards retirement'),
(4, 'Cushon Investment Account', "A savings account where you hold investments, but unlike an ISA, isn't tax-free.");

-- Create `customers` table
DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
    `id` UUID NOT NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` char(64) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    `deleted_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Populate `customers` table with dummy data
INSERT INTO `customers` (`id`, `name`, `email`, `username`, `password`, `created_at`, `updated_at`, `deleted_at`) VALUES
('550e8400-e29b-41d4-a716-446655440000', 'Jane Doe', 'jane.doe@example.com', 'jd1234', 'b2d2d1f5c536fb0a6c28e128d340fa8c9675ac4c9cf6959b5e48c874b1a37627', NOW(), NOW(), null);

-- Create `accounts` table
DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
    `id` UUID NOT NULL,
    `customer_id` UUID NOT NULL,
    `account_type_id` int(11) UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL,
    `deleted_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Populate `accounts` table with dummy data
INSERT INTO `accounts` (`id`, `customer_id`, `account_type_id`, `created_at`, `deleted_at`) VALUES
('10027d8e-5afb-11ee-8c99-0242ac120002','550e8400-e29b-41d4-a716-446655440000', 4, NOW(), NULL);

-- Add indexes, foreign keys, and other constraints if needed
-- Example: CREATE INDEX index_name ON table_name (column_name);
-- Example: ALTER TABLE table_name ADD FOREIGN KEY (column_name) REFERENCES other_table_name (other_column_name);
    -- KEY `fk_investment_transactions_funds` (`fund_id`),
    -- CONSTRAINT `fk_investment_transactions_funds` FOREIGN KEY (`fund_id`) REFERENCES `funds` (`id`),
    -- KEY `fk_investment_transactions_accounts` (`account_id`),
    -- CONSTRAINT `fk_investment_transactions_accou` FOREIGN KEY (`fund_id`) REFERENCES `funds` (`id`)
