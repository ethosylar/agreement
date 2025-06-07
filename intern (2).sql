-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2025 at 05:42 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `intern`
--

-- --------------------------------------------------------

--
-- Table structure for table `depart`
--

CREATE TABLE IF NOT EXISTS `depart` (
  `department` varchar(50) NOT NULL,
  `user_pass` varchar(10) NOT NULL,
  `user_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `depart`
--

INSERT INTO `depart` (`department`, `user_pass`, `user_id`) VALUES
('ACCIDENT AND EMERGENCY', 'abcd.1234', 'klgshae'),
('ACCOUNTS', 'abcd.1234', 'klgshac'),
('ADMINISTRATION', 'abcd.1234', 'klgshadmin'),
('AUDIOLOGY', 'abcd.1234', ''),
('BUSINESS OFFICE', 'abcd.1234', 'klgshbo'),
('CUSTOMER SERVICE EXPERIENCE', 'abcd.1234', 'klgshcs'),
('DIAGNOSTIC IMAGING SERVICES', 'abcd.1234', 'klgshxray'),
('DIETARY', 'abcd.1234', 'klgshdiet'),
('ENDOSCOPY ROOM', 'abcd.1234', 'klgshdayward'),
('HAEMODIALYSIS', 'abcd.1234', 'klgshdialysis'),
('HEALTH INFORMATION MANAGEMENT SERVICES', 'abcd.1234', 'klgshmr'),
('HEALTH SCREENING', 'abcd.1234', 'klgshwellness'),
('HEALTH TOURISM', 'abcd.1234', 'klgshht'),
('HEALTHCARE ENGINEERING SERVICES', 'abcd.1234', 'klgshhes'),
('HUMAN RESOURCES MANAGEMENT', 'abcd.1234', 'klgshhr'),
('ICU/CCU/CICU', 'abcd.1234', 'klgshicu'),
('INFORMATION TECHNOLOGY', 'abcd.1234', 'klgshit'),
('KLINIK WAQAF AN-NUR', 'abcd.1234', 'waqaf'),
('MARKETING & CORPORATE COMMUNICATION', 'abcd.1234', 'klgshmarketing'),
('MATERNITY', 'abcd.1234', 'klgshmat'),
('MEDICAL WARD', 'abcd.1234', 'klgshmed'),
('NURSING ADMINISTRATION', 'abcd.1234', 'klgshnurse'),
('OPERATION THEATER', 'abcd.1234', 'klgshot'),
('OUTSOURCE SERVICES', 'abcd.1234', 'klgshout'),
('PAEDIATRIC WARD', 'abcd.1234', 'klgshpaed'),
('PATIENT SERVICE', 'abcd.1234', 'klgshca'),
('PHARMACY', 'abcd.1234', 'klgshphar'),
('PHYSIOTHERAPY', 'abcd.1234', 'klgshphysio'),
('PREMIER WARD', 'abcd.1234', 'klgshprem'),
('PUBLIC RELATION ', 'abcd.1234', 'klgshpr'),
('PURCHASING', 'abcd.1234', 'klgshpurch'),
('QUALITY', 'abcd.1234', 'klgshquality'),
('RISK & COMPLIANCE SERVICES', 'abcd.1234', 'klgshrisk'),
('SAFETY & HEALTH', 'abcd.1234', 'klgshsafety'),
('SURGICAL WARD', 'abcd.1234', 'klgshsurg');

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE IF NOT EXISTS `form` (
`id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `pic` varchar(100) NOT NULL,
  `service` varchar(500) NOT NULL,
  `company` varchar(500) NOT NULL,
  `start` date NOT NULL,
  `endDate` date NOT NULL,
  `sqft` varchar(80) NOT NULL,
  `rent` varchar(80) NOT NULL,
  `remarks` varchar(500) NOT NULL,
  `filename` varchar(500) NOT NULL,
  `monthsLeft` int(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','expired') NOT NULL,
  `duration` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form`
--

INSERT INTO `form` (`id`, `category`, `pic`, `service`, `company`, `start`, `endDate`, `sqft`, `rent`, `remarks`, `filename`, `monthsLeft`, `department`, `upload_date`, `status`, `duration`) VALUES
(18, 'biomedical-facilities', 'aini', 'Camera System, Washer, ESU, Endosonic, Flush PumpÂ ', 'Olympus (Malaysia) Sdn BhdÂ ', '2023-01-01', '2025-02-28', '', '17370', 'valid', 'TAC401.pdf', -3, 'HEALTH TOURISM', '2025-06-06 06:27:58', 'expired', '3 years'),
(19, 'clinical', 'nur', 'KLASER TREATMENT CUBE 4Â ', 'Quick stop solution Sdn BhdÂ ', '2021-12-08', '2026-11-07', '', '50% Profit from klaser usage every monthÂ ', 'Profit sharingÂ ', '', 17, 'HEALTH TOURISM', '2025-06-06 06:43:43', 'active', '');

-- --------------------------------------------------------

--
-- Table structure for table `terminate`
--

CREATE TABLE IF NOT EXISTS `terminate` (
`id` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `start` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `sqft` int(11) DEFAULT NULL,
  `rent` decimal(10,2) DEFAULT NULL,
  `filename` varchar(500) DEFAULT NULL,
  `remarks` text,
  `monthsLeft` int(11) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `termination_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','expired') NOT NULL,
  `duration` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `depart`
--
ALTER TABLE `depart`
 ADD PRIMARY KEY (`department`);

--
-- Indexes for table `form`
--
ALTER TABLE `form`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terminate`
--
ALTER TABLE `terminate`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form`
--
ALTER TABLE `form`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `terminate`
--
ALTER TABLE `terminate`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
