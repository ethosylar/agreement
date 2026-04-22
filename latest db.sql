-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 06:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `intern`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `action` enum('INSERT','UPDATE','DELETE','ARCHIVE','LOGIN_SUCCESS','LOGIN_FAILURE','LOGOUT','ARCHIVE_INSERT','ARCHIVE_DELETE') NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `record_id` int(11) NOT NULL DEFAULT 0,
  `changed_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`changed_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`audit_id`, `user_id`, `department`, `action`, `table_name`, `record_id`, `changed_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'azean', 'Pn Azean', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\": \"azean\", \"message\": \"Admin login successful\"}', '10.10.10.5', 'Mozilla/5.0 Demo Browser', '2026-04-01 08:55:00'),
(2, 'klgshit', 'INFORMATION TECHNOLOGY', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\": \"klgshit\", \"message\": \"Department login successful\"}', '10.10.10.25', 'Mozilla/5.0 Demo Browser', '2026-04-01 09:00:00'),
(3, 'klghes', 'HEALTHCARE ENGINEERING SERVICES', 'INSERT', 'form', 1001, '{\"category\": \"service\", \"pic\": \"Nur Aisyah\", \"company\": \"Alam Hijau Sdn Bhd\", \"filename\": [\"waste_collection_contract.pdf\"]}', '10.10.10.31', 'Mozilla/5.0 Demo Browser', '2026-04-01 09:15:10'),
(4, 'klgshit', 'INFORMATION TECHNOLOGY', 'INSERT', 'form', 1002, '{\"category\": \"licensing\", \"pic\": \"Farid Rahman\", \"company\": \"Microsoft Malaysia\", \"filename\": [\"m365_license_agreement.pdf\"]}', '10.10.10.25', 'Mozilla/5.0 Demo Browser', '2026-04-01 09:30:10'),
(5, 'klgshadmin', 'ADMINISTRATION', 'INSERT', 'form', 1003, '{\"category\": \"tenant\", \"pic\": \"Siti Khadijah\", \"company\": \"BrewWave Ventures\", \"filename\": [\"brew_wave_tenant_agreement.pdf\"]}', '10.10.10.11', 'Mozilla/5.0 Demo Browser', '2026-04-01 10:00:10'),
(6, 'klgshsafety', 'SAFETY & HEALTH', 'INSERT', 'form', 1004, '{\"category\": \"outsource\", \"pic\": \"Haziq Firdaus\", \"company\": \"SafeGuard Security Sdn Bhd\", \"filename\": [\"security_outsource_main.pdf\", \"security_schedule.pdf\"]}', '10.10.10.41', 'Mozilla/5.0 Demo Browser', '2026-04-01 10:20:10'),
(7, 'klgshca', 'PATIENT SERVICES', 'INSERT', 'form', 1005, '{\"category\": \"clinical\", \"pic\": \"Dr. Aina\", \"company\": \"Dr. Aina Clinic\", \"filename\": [\"specialist_consultant_agreement.pdf\"]}', '10.10.10.51', 'Mozilla/5.0 Demo Browser', '2026-04-01 10:45:10'),
(8, 'klgshmarketing', 'MARKETING & CORPORATE COMMUNICATION', 'INSERT', 'form', 1006, '{\"category\": \"marcomm\", \"pic\": \"Hanafi\", \"company\": \"AIA Malaysia\", \"filename\": [\"aia_corporate_rate.pdf\"]}', '10.10.10.61', 'Mozilla/5.0 Demo Browser', '2026-04-01 11:10:10'),
(9, 'klgshout', 'OUTSOURCE SERVICES', 'INSERT', 'form', 1007, '{\"category\": \"service\", \"pic\": \"Puan Azlina\", \"company\": \"CleanCare Laundry Services\", \"filename\": [\"laundry_service_contract.pdf\"]}', '10.10.10.71', 'Mozilla/5.0 Demo Browser', '2026-04-01 11:25:10'),
(10, 'klgshxray', 'DIAGNOSTIC IMAGING SERVICES', 'INSERT', 'form', 1008, '{\"category\": \"service\", \"pic\": \"Rizal Hamdan\", \"company\": \"Meditech Solutions\", \"filename\": [\"biomedical_pm_agreement.pdf\"]}', '10.10.10.81', 'Mozilla/5.0 Demo Browser', '2026-04-01 11:40:10'),
(11, 'klgshbo', 'BUSINESS OFFICE', 'INSERT', 'form', 1009, '{\"category\": \"tenant\", \"pic\": \"Liyana\", \"company\": \"Bank Muamalat Malaysia\", \"filename\": [\"atm_rental_agreement.pdf\"]}', '10.10.10.91', 'Mozilla/5.0 Demo Browser', '2026-04-01 12:00:10'),
(12, 'klghes', 'HEALTHCARE ENGINEERING SERVICES', 'INSERT', 'form', 1010, '{\"category\": \"service\", \"pic\": \"Khairul Nizam\", \"company\": \"EcoPest Solutions\", \"filename\": [\"pest_control_agreement.pdf\"]}', '10.10.10.31', 'Mozilla/5.0 Demo Browser', '2026-04-01 12:20:10'),
(13, 'klgshit', 'INFORMATION TECHNOLOGY', 'INSERT', 'form', 1011, '{\"category\": \"licensing\", \"pic\": \"Aina Sofea\", \"company\": \"Kaspersky Enterprise\", \"filename\": [\"antivirus_subscription.pdf\"]}', '10.10.10.25', 'Mozilla/5.0 Demo Browser', '2026-04-01 13:00:10'),
(14, 'klgshdialysis', 'HAEMODIALYSIS', 'INSERT', 'form', 1012, '{\"category\": \"clinical\", \"pic\": \"Dr. Nazrin\", \"company\": \"RenalCare Supplies\", \"filename\": [\"dialysis_supply_agreement.pdf\"]}', '10.10.10.101', 'Mozilla/5.0 Demo Browser', '2026-04-01 13:20:10'),
(15, 'klgshdiet', 'DIETARY', 'INSERT', 'form', 1013, '{\"category\": \"service\", \"pic\": \"Shafiqah\", \"company\": \"Deli Rasa Enterprise\", \"filename\": [\"cafeteria_supply_contract.pdf\"]}', '10.10.10.111', 'Mozilla/5.0 Demo Browser', '2026-04-01 13:40:10'),
(16, 'klgshout', 'OUTSOURCE SERVICES', 'INSERT', 'form', 1014, '{\"category\": \"outsource\", \"pic\": \"Puan Suraya\", \"company\": \"CleanPro Facilities\", \"filename\": [\"cleaning_services_agreement.pdf\", \"cleaning_scope.pdf\"]}', '10.10.10.71', 'Mozilla/5.0 Demo Browser', '2026-04-01 14:00:10'),
(17, 'klghes', 'HEALTHCARE ENGINEERING SERVICES', 'INSERT', 'form', 1015, '{\"category\": \"service\", \"pic\": \"Amirul\", \"company\": \"PowerSafe Engineering\", \"filename\": [\"generator_maintenance_contract.pdf\"]}', '10.10.10.31', 'Mozilla/5.0 Demo Browser', '2026-04-01 14:20:10'),
(18, 'klgshadmin', 'ADMINISTRATION', 'INSERT', 'form', 1016, '{\"category\": \"tenant\", \"pic\": \"Noraini\", \"company\": \"MediPlus Retail\", \"filename\": [\"pharmacy_sublet_agreement.pdf\"]}', '10.10.10.11', 'Mozilla/5.0 Demo Browser', '2026-04-01 14:40:10'),
(19, 'klgshmarketing', 'MARKETING & CORPORATE COMMUNICATION', 'INSERT', 'form', 1017, '{\"category\": \"marcomm\", \"pic\": \"Faizal\", \"company\": \"Tune Protect\", \"filename\": [\"wellness_campaign_partnership.pdf\"]}', '10.10.10.61', 'Mozilla/5.0 Demo Browser', '2026-04-01 15:00:10'),
(20, 'klgshxray', 'DIAGNOSTIC IMAGING SERVICES', 'INSERT', 'form', 1018, '{\"category\": \"clinical\", \"pic\": \"Dr. Melissa\", \"company\": \"Radiant Diagnostics\", \"filename\": [\"radiology_reporting_service.pdf\"]}', '10.10.10.81', 'Mozilla/5.0 Demo Browser', '2026-04-01 15:20:10'),
(21, 'klgshit', 'INFORMATION TECHNOLOGY', 'INSERT', 'form', 1019, '{\"category\": \"service\", \"pic\": \"Izzati\", \"company\": \"Telekom Malaysia\", \"filename\": [\"leased_line_agreement.pdf\"]}', '10.10.10.25', 'Mozilla/5.0 Demo Browser', '2026-04-01 15:40:10'),
(22, 'klgshae', 'ACCIDENT AND EMERGENCY', 'INSERT', 'form', 1020, '{\"category\": \"outsource\", \"pic\": \"Rahimah\", \"company\": \"Rapid Medical Transport\", \"filename\": [\"ambulance_driver_outsource.pdf\"]}', '10.10.10.121', 'Mozilla/5.0 Demo Browser', '2026-04-01 16:00:10'),
(23, 'klgshit', 'INFORMATION TECHNOLOGY', 'UPDATE', 'form', 1002, '{\"old\": {\"rent\": \"4000.00\", \"filename\": \"m365_license_agreement_old.pdf\"}, \"new\": {\"rent\": \"4200.00\", \"filename\": \"m365_license_agreement.pdf\"}}', '10.10.10.25', 'Mozilla/5.0 Demo Browser', '2026-04-03 10:15:00'),
(24, 'klgshmarketing', 'MARKETING & CORPORATE COMMUNICATION', 'UPDATE', 'form', 1006, '{\"old\": {\"status\": \"active\", \"remarks\": \"Corporate package pending review\"}, \"new\": {\"status\": \"expired\", \"remarks\": \"Contract period ended and pending renewal\"}}', '10.10.10.61', 'Mozilla/5.0 Demo Browser', '2026-04-05 15:10:00'),
(25, 'azean', 'Pn Azean', 'ARCHIVE_INSERT', 'terminate', 1016, '{\"id\": 1016, \"category\": \"tenant\", \"company\": \"MediPlus Retail\", \"status\": \"expired\"}', '10.10.10.5', 'Mozilla/5.0 Demo Browser', '2026-04-10 09:20:00'),
(26, 'azean', 'Pn Azean', 'ARCHIVE_DELETE', 'form', 1016, '{\"id\": 1016, \"category\": \"tenant\", \"company\": \"MediPlus Retail\", \"status\": \"expired\"}', '10.10.10.5', 'Mozilla/5.0 Demo Browser', '2026-04-10 09:20:05'),
(27, 'klgshit', 'INFORMATION TECHNOLOGY', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\":\"klgshit\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 11:53:16'),
(28, 'klgshit', 'INFORMATION TECHNOLOGY', 'LOGOUT', 'depart', 0, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:08:02'),
(29, 'itservices', 'IT Services', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\":\"itservices\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:08:11'),
(30, 'itservices', 'IT Services', 'LOGOUT', 'depart', 0, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:09:23'),
(31, 'azean', 'Pn Azean', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\":\"azean\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:09:30'),
(32, 'azean', 'Pn Azean', 'LOGOUT', 'depart', 0, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:10:29'),
(33, 'itservices', 'IT Services', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\":\"itservices\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:10:36'),
(34, 'itservices', 'IT Services', 'LOGOUT', 'depart', 0, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:13:17'),
(35, 'klgshit', 'INFORMATION TECHNOLOGY', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\":\"klgshit\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:13:23'),
(36, 'klgshit', 'INFORMATION TECHNOLOGY', 'ARCHIVE_INSERT', 'terminate', 1011, '{\"id\":1011,\"category\":\"licensing\",\"pic\":\"Aina Sofea\",\"service\":\"Antivirus Enterprise Subscription\",\"company\":\"Kaspersky Enterprise\",\"start\":\"2025-09-01\",\"endDate\":\"2026-08-31\",\"no_end_date\":0,\"sqft\":\"\",\"rent\":\"3100.00\",\"remarks\":\"Enterprise device protection subscription.\",\"filename\":\"antivirus_subscription.pdf\",\"monthsLeft\":4,\"department\":\"INFORMATION TECHNOLOGY\",\"status\":\"active\",\"duration\":\"1 year\",\"created_at\":\"2026-04-01 13:00:00\",\"updated_at\":\"2026-04-01 13:00:00\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:13:33'),
(37, 'klgshit', 'INFORMATION TECHNOLOGY', 'ARCHIVE_DELETE', 'form', 1011, '{\"id\":1011,\"category\":\"licensing\",\"pic\":\"Aina Sofea\",\"service\":\"Antivirus Enterprise Subscription\",\"company\":\"Kaspersky Enterprise\",\"start\":\"2025-09-01\",\"endDate\":\"2026-08-31\",\"no_end_date\":0,\"sqft\":\"\",\"rent\":\"3100.00\",\"remarks\":\"Enterprise device protection subscription.\",\"filename\":\"antivirus_subscription.pdf\",\"monthsLeft\":4,\"department\":\"INFORMATION TECHNOLOGY\",\"status\":\"active\",\"duration\":\"1 year\",\"created_at\":\"2026-04-01 13:00:00\",\"updated_at\":\"2026-04-01 13:00:00\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:13:33'),
(38, 'klgshit', 'INFORMATION TECHNOLOGY', 'LOGOUT', 'depart', 0, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:14:14'),
(39, 'itservices', 'IT Services', 'LOGIN_SUCCESS', 'depart', 0, '{\"user_id\":\"itservices\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:14:21'),
(40, 'itservices', 'IT Services', 'LOGOUT', 'depart', 0, '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 12:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `depart`
--

CREATE TABLE `depart` (
  `department` varchar(100) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `depart`
--

INSERT INTO `depart` (`department`, `user_id`, `user_pass`, `created_at`, `updated_at`) VALUES
('ACCIDENT AND EMERGENCY', 'klgshae', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('ACCOUNTS', 'klgshac', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('ADMINISTRATION', 'klgshadmin', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('AUDIOLOGY', 'klgshaud', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-22 11:46:24'),
('BUSINESS OFFICE', 'klgshbo', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('CUSTOMER SERVICE EXPERIENCE', 'klgshcs', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('DIAGNOSTIC IMAGING SERVICES', 'klgshxray', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('DIETARY', 'klgshdiet', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('ENDOSCOPY ROOM', 'klgshdayward', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('HAEMODIALYSIS', 'klgshdialysis', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('HEALTH INFORMATION MANAGEMENT SERVICES', 'klgshmr', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('HEALTH SCREENING', 'klgshwellness', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('HEALTH TOURISM', 'klgshht', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('HEALTHCARE ENGINEERING SERVICES', 'klgshhes', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('HUMAN RESOURCES MANAGEMENT', 'klgshhr', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('ICU/CCU/CICU', 'klgshicu', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('INFORMATION TECHNOLOGY', 'klgshit', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('IT Services', 'itservices', 'abcd.1234', '2026-04-17 16:58:37', '2026-04-17 16:58:37'),
('KLINIK WAQAF AN-NUR', 'waqaf', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('MARKETING & CORPORATE COMMUNICATION', 'klgshmarketing', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('MATERNITY', 'klgshmat', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('MEDICAL WARD', 'klgshmed', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('NURSING ADMINISTRATION', 'klgshnurse', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('OPERATION THEATER', 'klgshot', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('OUTSOURCE SERVICES', 'klgshout', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('PAEDIATRIC WARD', 'klgshpaed', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('PATIENT SERVICES', 'klgshca', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('PHARMACY', 'klgshphar', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('PHYSIOTHERAPY', 'klgshphysio', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('Pn Azean', 'azean', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('PREMIER WARD', 'klgshprem', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('PUBLIC RELATION', 'klgshpr', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('PURCHASING', 'klgshpurch', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('QUALITY', 'klgshquality', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('RISK & COMPLIANCE SERVICES', 'klgshrisk', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('SAFETY & HEALTH', 'klgshsafety', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49'),
('SURGICAL WARD', 'klgshsurg', 'abcd.1234', '2026-04-17 16:55:49', '2026-04-17 16:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE `form` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `pic` varchar(150) NOT NULL,
  `service` varchar(500) NOT NULL,
  `company` varchar(500) NOT NULL,
  `start` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `no_end_date` tinyint(1) NOT NULL DEFAULT 0,
  `sqft` varchar(80) DEFAULT NULL,
  `rent` varchar(120) NOT NULL,
  `remarks` text DEFAULT NULL,
  `filename` text DEFAULT NULL,
  `monthsLeft` int(11) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `status` enum('active','expired') NOT NULL DEFAULT 'active',
  `duration` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `form`
--

INSERT INTO `form` (`id`, `category`, `pic`, `service`, `company`, `start`, `endDate`, `no_end_date`, `sqft`, `rent`, `remarks`, `filename`, `monthsLeft`, `department`, `status`, `duration`, `created_at`, `updated_at`) VALUES
(1001, 'service', 'Nur Aisyah', 'Clinical Waste Collection Service', 'Alam Hijau Sdn Bhd', '2025-01-01', '2026-12-31', 0, '', '1800.00', 'Annual clinical waste collection contract.', 'waste_collection_contract.pdf', 8, 'HEALTHCARE ENGINEERING SERVICES', 'active', '2 years', '2026-04-01 09:15:00', '2026-04-01 09:15:00'),
(1002, 'licensing', 'Farid Rahman', 'Microsoft 365 Business Licensing', 'Microsoft Malaysia', '2025-06-01', '2026-05-31', 0, '', '4200.00', 'Renewable annual software licensing agreement.', 'm365_license_agreement.pdf', 1, 'INFORMATION TECHNOLOGY', 'active', '1 year', '2026-04-01 09:30:00', '2026-04-01 09:30:00'),
(1003, 'tenant', 'Siti Khadijah', 'Retail Space Rental - Coffee Booth', 'BrewWave Ventures', '2024-01-01', '2026-12-31', 0, '120', '3500.00', 'Tenant agreement for lobby coffee booth.', 'brew_wave_tenant_agreement.pdf', 8, 'ADMINISTRATION', 'active', '3 years', '2026-04-01 10:00:00', '2026-04-01 10:00:00'),
(1004, 'outsource', 'Haziq Firdaus', 'Security Guard Outsourcing', 'SafeGuard Security Sdn Bhd', '2024-07-01', '2026-06-30', 0, '', '12500.00', '24-hour outsourced security services.', 'security_outsource_main.pdf, security_schedule.pdf', 2, 'SAFETY & HEALTH', 'active', '2 years', '2026-04-01 10:20:00', '2026-04-01 10:20:00'),
(1005, 'clinical', 'Dr. Aina', 'Specialist Visiting Consultant Agreement', 'Dr. Aina Clinic', '2025-02-01', '2027-01-31', 0, '', '8000.00', 'Visiting consultant arrangement for ENT clinic.', 'specialist_consultant_agreement.pdf', 9, 'PATIENT SERVICES', 'active', '2 years', '2026-04-01 10:45:00', '2026-04-01 10:45:00'),
(1006, 'marcomm', 'Hanafi', 'AIA Corporate Health Screening Package', 'AIA Malaysia', '2024-04-01', '2026-03-31', 0, '', 'N/A', 'Corporate package and rate card for health screening.', 'aia_corporate_rate.pdf', -1, 'MARKETING & CORPORATE COMMUNICATION', 'expired', '2 years', '2026-04-01 11:10:00', '2026-04-01 11:10:00'),
(1007, 'service', 'Puan Azlina', 'Laundry Services for Ward Linen', 'CleanCare Laundry Services', '2025-03-01', '2026-08-31', 0, '', '5400.00', 'Ward linen laundry and collection service.', 'laundry_service_contract.pdf', 4, 'OUTSOURCE SERVICES', 'active', '1 year 6 months', '2026-04-01 11:25:00', '2026-04-01 11:25:00'),
(1008, 'service', 'Rizal Hamdan', 'Biomedical Equipment Preventive Maintenance', 'Meditech Solutions', '2025-05-15', '2026-05-14', 0, '', '6800.00', 'Preventive maintenance for patient monitoring equipment.', 'biomedical_pm_agreement.pdf', 1, 'DIAGNOSTIC IMAGING SERVICES', 'active', '1 year', '2026-04-01 11:40:00', '2026-04-01 11:40:00'),
(1009, 'tenant', 'Liyana', 'ATM Space Rental Agreement', 'Bank Muamalat Malaysia', '2023-09-01', '2026-08-31', 0, '40', '2200.00', 'Rental agreement for ATM kiosk at hospital lobby.', 'atm_rental_agreement.pdf', 4, 'BUSINESS OFFICE', 'active', '3 years', '2026-04-01 12:00:00', '2026-04-01 12:00:00'),
(1010, 'service', 'Khairul Nizam', 'Pest Control Services', 'EcoPest Solutions', '2025-01-15', '2026-01-14', 0, '', '950.00', 'Monthly pest control service for hospital compound.', 'pest_control_agreement.pdf', -3, 'HEALTHCARE ENGINEERING SERVICES', 'expired', '1 year', '2026-04-01 12:20:00', '2026-04-01 12:20:00'),
(1012, 'clinical', 'Dr. Nazrin', 'Dialysis Consumables Supply Agreement', 'RenalCare Supplies', '2025-07-01', '2026-06-30', 0, '', '14500.00', 'Consumables supply contract for dialysis services.', 'dialysis_supply_agreement.pdf', 2, 'HAEMODIALYSIS', 'active', '1 year', '2026-04-01 13:20:00', '2026-04-01 13:20:00'),
(1013, 'service', 'Shafiqah', 'Cafeteria Food Supply Contract', 'Deli Rasa Enterprise', '2024-10-01', '2026-09-30', 0, '', '7600.00', 'Food and beverage supply contract for cafeteria operations.', 'cafeteria_supply_contract.pdf', 5, 'DIETARY', 'active', '2 years', '2026-04-01 13:40:00', '2026-04-01 13:40:00'),
(1014, 'outsource', 'Puan Suraya', 'Cleaning Services Agreement', 'CleanPro Facilities', '2025-01-01', '2026-12-31', 0, '', '11800.00', 'General cleaning services for public areas and wards.', 'cleaning_services_agreement.pdf, cleaning_scope.pdf', 8, 'OUTSOURCE SERVICES', 'active', '2 years', '2026-04-01 14:00:00', '2026-04-01 14:00:00'),
(1015, 'service', 'Amirul', 'Generator Maintenance Contract', 'PowerSafe Engineering', '2025-04-01', '2027-03-31', 0, '', '8900.00', 'Quarterly generator servicing and emergency support.', 'generator_maintenance_contract.pdf', 11, 'HEALTHCARE ENGINEERING SERVICES', 'active', '2 years', '2026-04-01 14:20:00', '2026-04-01 14:20:00'),
(1016, 'tenant', 'Noraini', 'Pharmacy Sub-Lot Rental', 'MediPlus Retail', '2023-01-01', '2025-12-31', 0, '180', '6500.00', 'Retail sub-lot tenancy for external pharmacy operator.', 'pharmacy_sublet_agreement.pdf', -4, 'ADMINISTRATION', 'expired', '3 years', '2026-04-01 14:40:00', '2026-04-01 14:40:00'),
(1017, 'marcomm', 'Faizal', 'Corporate Wellness Campaign Partnership', 'Tune Protect', '2025-11-01', '2026-10-31', 0, '', '2500.00', 'Marketing collaboration for wellness campaign packages.', 'wellness_campaign_partnership.pdf', 6, 'MARKETING & CORPORATE COMMUNICATION', 'active', '1 year', '2026-04-01 15:00:00', '2026-04-01 15:00:00'),
(1018, 'clinical', 'Dr. Melissa', 'Radiology Reporting Service Agreement', 'Radiant Diagnostics', '2025-08-01', '2026-07-31', 0, '', '9200.00', 'After-hours radiology reporting service.', 'radiology_reporting_service.pdf', 3, 'DIAGNOSTIC IMAGING SERVICES', 'active', '1 year', '2026-04-01 15:20:00', '2026-04-01 15:20:00'),
(1019, 'service', 'Izzati', 'Internet Leased Line Agreement', 'Telekom Malaysia', '2025-01-01', '2027-12-31', 0, '', '4900.00', 'Dedicated leased line for hospital network operations.', 'leased_line_agreement.pdf', 20, 'INFORMATION TECHNOLOGY', 'active', '3 years', '2026-04-01 15:40:00', '2026-04-01 15:40:00'),
(1020, 'outsource', 'Rahimah', 'Ambulance Driver Outsourcing Agreement', 'Rapid Medical Transport', '2025-05-01', '2026-04-30', 0, '', '6800.00', 'On-call outsourced ambulance driver services.', 'ambulance_driver_outsource.pdf', 0, 'ACCIDENT AND EMERGENCY', 'active', '1 year', '2026-04-01 16:00:00', '2026-04-01 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `terminate`
--

CREATE TABLE `terminate` (
  `id` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `pic` varchar(150) DEFAULT NULL,
  `service` varchar(500) DEFAULT NULL,
  `company` varchar(500) DEFAULT NULL,
  `start` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `no_end_date` tinyint(1) NOT NULL DEFAULT 0,
  `sqft` varchar(80) DEFAULT NULL,
  `rent` varchar(120) DEFAULT NULL,
  `filename` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `monthsLeft` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `status` enum('active','expired') NOT NULL DEFAULT 'expired',
  `duration` varchar(100) DEFAULT NULL,
  `termination_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terminate`
--

INSERT INTO `terminate` (`id`, `category`, `pic`, `service`, `company`, `start`, `endDate`, `no_end_date`, `sqft`, `rent`, `filename`, `remarks`, `monthsLeft`, `department`, `status`, `duration`, `termination_date`, `created_at`, `updated_at`) VALUES
(1011, 'licensing', 'Aina Sofea', 'Antivirus Enterprise Subscription', 'Kaspersky Enterprise', '2025-09-01', '2026-08-31', 0, NULL, '3100', 'antivirus_subscription.pdf', 'Enterprise device protection subscription.', 4, 'INFORMATION TECHNOLOGY', 'active', '1 year', '2026-04-22 12:13:33', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_audit_created_at` (`created_at`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_action` (`action`),
  ADD KEY `idx_audit_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_audit_department_created` (`department`,`created_at`);

--
-- Indexes for table `depart`
--
ALTER TABLE `depart`
  ADD PRIMARY KEY (`department`),
  ADD UNIQUE KEY `uk_depart_user_id` (`user_id`);

--
-- Indexes for table `form`
--
ALTER TABLE `form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_form_department` (`department`),
  ADD KEY `idx_form_status` (`status`),
  ADD KEY `idx_form_endDate` (`endDate`),
  ADD KEY `idx_form_no_end_date` (`no_end_date`),
  ADD KEY `idx_form_monthsLeft` (`monthsLeft`),
  ADD KEY `idx_form_created_at` (`created_at`),
  ADD KEY `idx_form_updated_at` (`updated_at`);

--
-- Indexes for table `terminate`
--
ALTER TABLE `terminate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_terminate_department` (`department`),
  ADD KEY `idx_terminate_termination_date` (`termination_date`),
  ADD KEY `idx_terminate_status` (`status`),
  ADD KEY `idx_terminate_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `form`
--
ALTER TABLE `form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1021;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `form`
--
ALTER TABLE `form`
  ADD CONSTRAINT `fk_form_department` FOREIGN KEY (`department`) REFERENCES `depart` (`department`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
