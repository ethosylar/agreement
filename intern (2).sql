-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2025 at 02:40 AM
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
  `user_pass` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `depart`
--

INSERT INTO `depart` (`department`, `user_pass`) VALUES
('ACCIDENT AND EMERGENCY', 'abcd.1234'),
('ACCOUNTS', 'abcd.1234'),
('ADMINISTRATION', 'abcd.1234'),
('AUDIOLOGY', 'abcd.1234'),
('BUSINESS DEVELOPMENT', 'abcd.1234'),
('BUSINESS DEVELOPMENT/OFFICE', 'abcd.1234'),
('DIAGNOSTIC IMAGING SERVICES', 'abcd.1234'),
('DIETARY', 'abcd.1234'),
('ENDOSCOPY ROOM', 'abcd.1234'),
('FINANCE', 'abcd.1234'),
('HAEMODIALYSIS', 'abcd.1234'),
('HEALTH INFORMATION MANAGEMENT SERVICES', 'abcd.1234'),
('HEALTH SCREENING', 'abcd.1234'),
('HEALTHCARE ENGINEERING SERVICES', 'abcd.1234'),
('HUMAN RESOURCES MANAGEMENT', 'abcd.1234'),
('ICU/CCU/CICU', 'abcd.1234'),
('INFORMATION TECHNOLOGY', 'abcd.1234'),
('KLINIK WAQAF AN-NUR', 'abcd.1234'),
('KPJ WELLNESS SERVICES', 'abcd.1234'),
('MARKETING & CORPORATE COMMUNICATION', 'abcd.1234'),
('MARKETING DEPARTMENT', 'abcd.1234'),
('MATERNITY', 'abcd.1234'),
('MEDICAL OFFICER', 'abcd.1234'),
('MEDICAL RECORDS', 'abcd.1234'),
('MEDICAL WARD', 'abcd.1234'),
('NURSING ADMINISTRATION', '031'),
('OPERATION THEATRE', '032'),
('OPTOMETRIST', '033'),
('OUTSOURCE SERVICES', '034'),
('PAEDIATRIC WARD', '035'),
('PATIENT LIAISON SERVICES', '036'),
('PATIENT SERVICE', '037'),
('PHARMACY', '038'),
('PHYSIOTHERAPY', '039'),
('PREMIER WARD', '040'),
('PUBLIC RELATION DEPARTMENT', '041'),
('PUBLIC RELATIONS AND MARKETING', '042'),
('PURCHASING', '043'),
('QUALITY', '044'),
('RISK & COMPLIANCE SERVICES', '045'),
('SAFETY & HEALTH', '046'),
('SURGICAL WARD', '047');

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
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form`
--

INSERT INTO `form` (`id`, `category`, `pic`, `service`, `company`, `start`, `endDate`, `sqft`, `rent`, `remarks`, `filename`, `monthsLeft`, `department`, `upload_date`) VALUES
(3, 'licensing', 'nurhayati', 'INFORMATION TECHNOLOGY', 'Kementerian Kesihatan Malaysia', '2023-01-10', '2024-09-22', '', '', 'Valid, to submit renewal to UKAPS&lt; JKKNS by March 2024', 'Screenshot 2025-02-12 100543.png', -4, 'INFORMATION TECHNOLOGY', '2025-02-13 01:58:24'),
(4, 'service', 'Siti Baidura', 'Lab serviceÂ Â ', 'Lablink (M) Sdn BhdÂ ', '2022-01-01', '2024-12-31', '', '3500', '2+1 year , valid ', 'Screenshot 2024-03-24 122459.png', -1, 'ACCIDENT AND EMERGENCY', '2025-02-12 18:38:31'),
(13, 'licensing', 'licensing', 'licensing', 'singing', '2024-12-01', '2025-02-01', '', '454', '', 'Practical Training Report.docx.pdf, logbook.docx.pdf', 0, 'AUDIOLOGY', '2025-02-20 00:14:03'),
(14, 'licensing', 'eyyy', 'hehhe', '', '0000-00-00', '0000-00-00', '', '', '', '', 0, 'AUDIOLOGY', '2025-02-19 22:15:24'),
(15, 'licensing', 'mmmmm', 'mmmmm', 'mmmmmm', '2025-02-01', '2025-05-03', '', 'qqq', '', 'LOGBOOK  bulan 11.docx', 2, 'ADMINISTRATION', '2025-02-20 08:52:31');

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
  `filename` varchar(255) DEFAULT NULL,
  `remarks` text,
  `monthsLeft` int(11) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `termination_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `terminate`
--

INSERT INTO `terminate` (`id`, `category`, `pic`, `service`, `company`, `start`, `endDate`, `sqft`, `rent`, `filename`, `remarks`, `monthsLeft`, `department`, `termination_date`) VALUES
(1, 'biomedical-facilities', 'Amirah', 'Incubator/ Vital Sign/ Patient Monitor/ Stress Test/ RF Diathermy/ Tourniquet/ Splint Unit/ Hot-Pack/ CTG/ Warming Cabinet/ ESU/ Fetal Heart/ Holter/ ABPM/ BP SetÂ ', 'Damansara PMCÂ ', '2024-01-01', '2024-12-31', 0, '6480.00', 'LOGBOOK  bulan 11.docx', 'valid', -1, 'ADMINISTRATION', '2025-02-18 10:45:59'),
(2, 'licensing', '', '', '', '0000-00-00', '0000-00-00', 0, '0.00', 'LOGBOOK  bulan 11.docx', '', 0, 'ADMINISTRATION', '2025-02-18 10:56:08'),
(10, 'licensing', 'eyyy', '', '', '0000-00-00', '0000-00-00', NULL, '0.00', NULL, '', NULL, 'ADMINISTRATION', '2025-02-20 03:56:35'),
(12, 'licensing', 'eyyy', '', '', '0000-00-00', '0000-00-00', NULL, '0.00', NULL, '', NULL, 'INFORMATION TECHNOLOGY', '2025-02-20 04:13:50');

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `terminate`
--
ALTER TABLE `terminate`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
