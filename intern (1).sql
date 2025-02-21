-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2025 at 03:50 AM
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
  `user_pass` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `depart`
--

INSERT INTO `depart` (`department`, `user_pass`) VALUES
('ACCIDENT AND EMERGENCY', '001'),
('ACCOUNTS', '002'),
('ADMINISTRATION', '003'),
('AUDIOLOGY', '004'),
('BUSINESS DEVELOPMENT', '005'),
('BUSINESS DEVELOPMENT/OFFICE', '006'),
('BUSINESS OFFICE', '007'),
('CLINICAL SERVICES & CLINICAL QUALITY', '008'),
('CONSULTANT CLINIC', '009'),
('CUSTOMER CARE & EXPERIENCE', '010'),
('CUSTOMER SERVICE', '011'),
('DIAGNOSTIC IMAGING SERVICES', '012'),
('DIETARY', '013'),
('ENDOSCOPY ROOM', '014'),
('FINANCE', '015'),
('HAEMODIALYSIS', '016'),
('HEALTH INFORMATION MANAGEMENT SERVICES', '017'),
('HEALTH SCREENING', '018'),
('HEALTHCARE ENGINEERING SERVICES', '019'),
('HUMAN RESOURCES MANAGEMENT', '020'),
('ICU/CCU/CICU', '021'),
('INFORMATION TECHNOLOGY', '022'),
('KLINIK WAQAF AN-NUR', '023'),
('KPJ WELLNESS SERVICES', '024'),
('MARKETING & CORPORATE COMMUNICATION', '025'),
('MARKETING DEPARTMENT', '026'),
('MATERNITY', '027'),
('MEDICAL OFFICER', '028'),
('MEDICAL RECORDS', '029'),
('MEDICAL WARD', '030'),
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
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
`id` int(11) NOT NULL,
  `idForm` int(11) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE IF NOT EXISTS `form` (
`idForm` int(11) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form`
--

INSERT INTO `form` (`idForm`, `category`, `pic`, `service`, `company`, `start`, `endDate`, `sqft`, `rent`, `remarks`, `filename`, `monthsLeft`, `department`, `upload_date`) VALUES
(3, 'licensing', 'nurhayati', 'INFORMATION TECHNOLOGY', 'Kementerian Kesihatan Malaysia', '2023-01-10', '2024-09-22', '', '', 'Valid, to submit renewal to UKAPS&lt; JKKNS by March 2024', 'Screenshot 2025-02-12 100543.png', -4, 'INFORMATION TECHNOLOGY', '2025-02-13 01:58:24'),
(4, 'service', 'Siti Baidura', 'Lab serviceÂ Â ', 'Lablink (M) Sdn BhdÂ ', '2022-01-01', '2024-12-31', '', '3500', '2+1 year , valid ', 'Screenshot 2024-03-24 122459.png', -1, 'ACCIDENT AND EMERGENCY', '2025-02-12 18:38:31'),
(5, 'clinical', 'Hajah Zakiah', 'KLASER TREATMENT CUBE 4Â ', 'Quick stop solution Sdn BhdÂ ', '2021-10-12', '2026-11-07', '', '50% Profit from klaser usage every monthÂ ', 'Profit sharingÂ ', 'Screenshot (19).png', 20, 'ACCOUNTS', '2025-02-12 18:41:02'),
(7, 'support', 'Ahmad Habib', 'Encoremed Appointment SystemÂ ', 'Encoremed Sdn BhdÂ ', '2024-01-01', '2024-12-31', '', 'RM 77,062Â ', 'valid', 'CSC264 INDIVIDUAL PROJECT UserManual.docx', -1, 'ACCOUNTS', '2025-02-12 18:45:09');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_name` varchar(50) NOT NULL,
  `user_pass` varchar(6) NOT NULL,
  `department` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_name`, `user_pass`, `department`) VALUES
('ahmad', '112', 'It'),
('aini', '333', 'It'),
('jaja', '444', 'hr'),
('mia', '222', 'marketing'),
('samsiah', '123', 'finance'),
('zaki', '111', 'marketing');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `depart`
--
ALTER TABLE `depart`
 ADD PRIMARY KEY (`department`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
 ADD PRIMARY KEY (`id`), ADD KEY `idForm` (`idForm`);

--
-- Indexes for table `form`
--
ALTER TABLE `form`
 ADD PRIMARY KEY (`idForm`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form`
--
ALTER TABLE `form`
MODIFY `idForm` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`idForm`) REFERENCES `form` (`idForm`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
