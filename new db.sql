CREATE DATABASE IF NOT EXISTS `intern`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `intern`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `audit_log`;
DROP TABLE IF EXISTS `terminate`;
DROP TABLE IF EXISTS `form`;
DROP TABLE IF EXISTS `depart`;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================
-- 1) LOGIN / DEPARTMENT TABLE
-- =========================================
CREATE TABLE `depart` (
  `department` VARCHAR(100) NOT NULL,
  `user_id` VARCHAR(50) NOT NULL,
  `user_pass` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`department`),
  UNIQUE KEY `uk_depart_user_id` (`user_id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 2) MAIN AGREEMENT TABLE
-- =========================================
CREATE TABLE `form` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(50) NOT NULL,
  `pic` VARCHAR(150) NOT NULL,
  `service` VARCHAR(500) NOT NULL,
  `company` VARCHAR(500) NOT NULL,
  `start` DATE NOT NULL,
  `endDate` DATE NULL,
  `no_end_date` TINYINT(1) NOT NULL DEFAULT 0,
  `sqft` VARCHAR(80) DEFAULT NULL,
  `rent` VARCHAR(120) NOT NULL,
  `remarks` TEXT DEFAULT NULL,
  `filename` TEXT DEFAULT NULL,
  `monthsLeft` INT DEFAULT NULL,
  `department` VARCHAR(100) NOT NULL,
  `status` ENUM('active','expired') NOT NULL DEFAULT 'active',
  `duration` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_form_department` (`department`),
  KEY `idx_form_status` (`status`),
  KEY `idx_form_endDate` (`endDate`),
  KEY `idx_form_no_end_date` (`no_end_date`),
  KEY `idx_form_monthsLeft` (`monthsLeft`),
  KEY `idx_form_created_at` (`created_at`),
  CONSTRAINT `fk_form_department`
    FOREIGN KEY (`department`)
    REFERENCES `depart` (`department`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 3) ARCHIVED / TERMINATED AGREEMENTS
-- keep same id as original form record, since your PHP archive flow uses it that way
-- =========================================
CREATE TABLE `terminate` (
  `id` INT NOT NULL,
  `category` VARCHAR(50) DEFAULT NULL,
  `pic` VARCHAR(150) DEFAULT NULL,
  `service` VARCHAR(500) DEFAULT NULL,
  `company` VARCHAR(500) DEFAULT NULL,
  `start` DATE DEFAULT NULL,
  `endDate` DATE NULL,
  `no_end_date` TINYINT(1) NOT NULL DEFAULT 0,
  `sqft` VARCHAR(80) DEFAULT NULL,
  `rent` VARCHAR(120) DEFAULT NULL,
  `filename` TEXT DEFAULT NULL,
  `remarks` TEXT DEFAULT NULL,
  `monthsLeft` INT DEFAULT NULL,
  `department` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('active','expired') NOT NULL DEFAULT 'expired',
  `duration` VARCHAR(100) DEFAULT NULL,
  `termination_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_terminate_department` (`department`),
  KEY `idx_terminate_termination_date` (`termination_date`),
  KEY `idx_terminate_status` (`status`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 4) AUDIT LOG
-- use VARCHAR for action instead of ENUM so future actions do not require ALTER TABLE
-- =========================================
CREATE TABLE `audit_log` (
  `audit_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(50) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `table_name` VARCHAR(64) NOT NULL,
  `record_id` INT NOT NULL DEFAULT 0,
  `changed_data` JSON NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_id`),
  KEY `idx_audit_created_at` (`created_at`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_table_record` (`table_name`, `record_id`),
  KEY `idx_audit_department_created` (`department`, `created_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 5) OPTIONAL: REINSERT YOUR EXISTING DEPARTMENT USERS
-- reuse/adapt from your old SQL dump
-- =========================================
INSERT INTO `depart` (`department`, `user_id`, `user_pass`) VALUES
('ACCIDENT AND EMERGENCY', 'klgshae', 'abcd.1234'),
('ACCOUNTS', 'klgshac', 'abcd.1234'),
('ADMINISTRATION', 'klgshadmin', 'abcd.1234'),
('AUDIOLOGY', '', 'abcd.1234'),
('BUSINESS OFFICE', 'klgshbo', 'abcd.1234'),
('CUSTOMER SERVICE EXPERIENCE', 'klgshcs', 'abcd.1234'),
('DIAGNOSTIC IMAGING SERVICES', 'klgshxray', 'abcd.1234'),
('DIETARY', 'klgshdiet', 'abcd.1234'),
('ENDOSCOPY ROOM', 'klgshdayward', 'abcd.1234'),
('HAEMODIALYSIS', 'klgshdialysis', 'abcd.1234'),
('HEALTH INFORMATION MANAGEMENT SERVICES', 'klgshmr', 'abcd.1234'),
('HEALTH SCREENING', 'klgshwellness', 'abcd.1234'),
('HEALTH TOURISM', 'klgshht', 'abcd.1234'),
('HEALTHCARE ENGINEERING SERVICES', 'klgshhes', 'abcd.1234'),
('HUMAN RESOURCES MANAGEMENT', 'klgshhr', 'abcd.1234'),
('ICU/CCU/CICU', 'klgshicu', 'abcd.1234'),
('INFORMATION TECHNOLOGY', 'klgshit', 'abcd.1234'),
('KLINIK WAQAF AN-NUR', 'waqaf', 'abcd.1234'),
('MARKETING & CORPORATE COMMUNICATION', 'klgshmarketing', 'abcd.1234'),
('MATERNITY', 'klgshmat', 'abcd.1234'),
('MEDICAL WARD', 'klgshmed', 'abcd.1234'),
('NURSING ADMINISTRATION', 'klgshnurse', 'abcd.1234'),
('OPERATION THEATER', 'klgshot', 'abcd.1234'),
('OUTSOURCE SERVICES', 'klgshout', 'abcd.1234'),
('PAEDIATRIC WARD', 'klgshpaed', 'abcd.1234'),
('PATIENT SERVICES', 'klgshca', 'abcd.1234'),
('PHARMACY', 'klgshphar', 'abcd.1234'),
('PHYSIOTHERAPY', 'klgshphysio', 'abcd.1234'),
('PREMIER WARD', 'klgshprem', 'abcd.1234'),
('PUBLIC RELATION', 'klgshpr', 'abcd.1234'),
('PURCHASING', 'klgshpurch', 'abcd.1234'),
('QUALITY', 'klgshquality', 'abcd.1234'),
('RISK & COMPLIANCE SERVICES', 'klgshrisk', 'abcd.1234'),
('SAFETY & HEALTH', 'klgshsafety', 'abcd.1234'),
('SURGICAL WARD', 'klgshsurg', 'abcd.1234');

-- =========================================
-- 6) OPTIONAL ADMIN ACCOUNT
-- add only if you want azean to log in through depart as well
-- =========================================
INSERT INTO `depart` (`department`, `user_id`, `user_pass`)
VALUES ('Pn Azean', 'azean', 'abcd.1234')
ON DUPLICATE KEY UPDATE
  `user_pass` = VALUES(`user_pass`);