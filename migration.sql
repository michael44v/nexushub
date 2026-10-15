-- Database Migration Script for NexusHub Trading Platform
-- Run this migration to update existing databases with KYC, Messenger, and Special Payments support.

-- 1. Add KYC Verification columns to user_details table
ALTER TABLE `user_details`
  ADD COLUMN IF NOT EXISTS `kyc_status` VARCHAR(50) DEFAULT 'Not Submitted',
  ADD COLUMN IF NOT EXISTS `kyc_doc_type` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `kyc_doc_url` VARCHAR(500) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `kyc_submitted_at` TIMESTAMP NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `kyc_rejection_reason` TEXT DEFAULT NULL;

-- 2. Create Messages Table for Messenger Support
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uid` INT NOT NULL,
  `sender` VARCHAR(50) DEFAULT 'user',
  `message` TEXT NOT NULL,
  `tm` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Special Payment Pages Table
CREATE TABLE IF NOT EXISTS `special_payment_pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(64) NOT NULL UNIQUE,
  `token_symbol` VARCHAR(20) NOT NULL DEFAULT 'USDT',
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `from_address` VARCHAR(255),
  `to_address` VARCHAR(255) NOT NULL,
  `crypto_amount` VARCHAR(50) NOT NULL,
  `usd_amount` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create Special Payments Submissions Table
CREATE TABLE IF NOT EXISTS `special_payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `page_id` INT NOT NULL,
  `code` VARCHAR(64) NOT NULL,
  `token_symbol` VARCHAR(20) NOT NULL,
  `from_address` VARCHAR(255),
  `to_address` VARCHAR(255) NOT NULL,
  `crypto_amount` VARCHAR(50) NOT NULL,
  `usd_amount` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Processing',
  `user_note` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
