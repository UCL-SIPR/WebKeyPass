-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 15, 2013 at 10:35 AM
-- Server version: 5.5.30
-- PHP Version: 5.4.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `webkeypass`
--

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hostname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_857FE8453D8E604F` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `parent`, `name`, `hostname`, `type`, `comment`, `icon`) VALUES
(1, NULL, 'Linux', '', 0, '', 'linux'),
(2, NULL, 'Solaris', '', 0, '', 'solaris'),
(3, 1, 'Server 1', 'server1.sipr.ucl.ac.be', 1, 'Virtualisation avec Xen', ''),
(4, 1, 'Server 2', 'server2.sipr.ucl.ac.be', 1, '', ''),
(5, 3, 'Virtual Machine A', 'server1_vmA.sipr.ucl.ac.be', 2, 'Mail', ''),
(6, 3, 'Virtual Machine B', 'server1_vmB.sipr.ucl.ac.be', 2, 'Site web', ''),
(7, NULL, 'Windows', NULL, 0, NULL, 'microsoft');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `FK_857FE8453D8E604F` FOREIGN KEY (`parent`) REFERENCES `node` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
