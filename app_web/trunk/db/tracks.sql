-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: SavCo-Station
-- Generation Time: Dec 29, 2013 at 10:51 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tracks`
--

-- --------------------------------------------------------

--
-- Table structure for table `apicalls`
--

CREATE TABLE IF NOT EXISTS `apicalls` (
  `apicall_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `apicall_name` varchar(125) DEFAULT NULL COMMENT 'The name that will be used for the page of the site. Presently user can not use same name twice.',
  `apicall_string` varchar(400) DEFAULT NULL,
  `apicall_status` tinyint(4) DEFAULT NULL,
  `apicall_type` varchar(20) NOT NULL COMMENT '0=User, 1=Space,2=Track',
  `apiresponse_type` smallint(6) NOT NULL COMMENT '0=JSON,1=HTML,2=STREAM',
  `tsCreated` int(11) NOT NULL,
  `tsModified` int(11) DEFAULT NULL,
  PRIMARY KEY (`apicall_id`),
  UNIQUE KEY `album_id` (`apicall_name`,`tsCreated`),
  KEY `album_name` (`apicall_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `apicalls`
--

INSERT INTO `apicalls` (`apicall_id`, `apicall_name`, `apicall_string`, `apicall_status`, `apicall_type`, `apiresponse_type`, `tsCreated`, `tsModified`) VALUES
(1, 'Authenticate a User', '/api/authenticate?usernameEmail=profileName&password=demo&device_id=1&version_id=1.1.3&lat=34.017&lon=-118.495', 1, '0', 0, 1343316813, 1368246156),
(2, 'Register User (Authenticate)', '/api/register?fullname=John Doe&email=enter real address for email.com&password=passwd&username=savco&phone=3105555545&userPhoto=file,/dirname/filename/&lat=34.017&lon=-118.495', 1, '0', 0, 1388302317, 0),
(3, 'User Profile with Credentials', '/api/userprofilewithcredentials?userId=text,1&authToken=text,user authentication token&lat=34.017&lon=-118.495', 1, '0', 0, 1357661759, NULL),
(4, 'User Social with Credentials', '/api/usersocialwithcredentials?userId=text,1&authToken=text, user authentication token&lat=34.017&lon=-118.495', 1, '0', 0, 1357661910, NULL),
(5, 'Get User with Id', '/api/userandvideoswithid?userId=text,1&authToken=text, user authentication token&requestUserId=2&limit=(opt.)20&offset=(opt)0&lastId=0&lat=34.017&lon=-118.495', 1, '0', 0, 1357780610, 1368402922),
(6, 'FB Connect with User', '/api/fbconnect?fbId=fbId&fbName=Name from Facebook&fbFName=firstName&fbMName=MiddleName&fbLName=LastName&fbEmail=EmailAddress&fbGender=&fbProfileURL=&fbPermissions=&fbUsername=&fbLocale=iOS Location;&device_id=1&device_version=5.1.3&device_token=sbkafgagfyatewyfgtywe78&version=1.1.3&lat=34.017&lon=-118.495', 1, '0', 0, 1355359951, 1370826158);

-- --------------------------------------------------------

--
-- Table structure for table `apicalls_profile`
--

CREATE TABLE IF NOT EXISTS `apicalls_profile` (
  `apicall_id` bigint(20) unsigned NOT NULL,
  `profile_key` varchar(5) NOT NULL,
  `profile_value` text NOT NULL,
  UNIQUE KEY `album_id` (`apicall_id`,`profile_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enumdt_devicetypes`
--

CREATE TABLE IF NOT EXISTS `enumdt_devicetypes` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `tsCreated` int(11) NOT NULL,
  `tsModified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `enumdt_devicetypes`
--

INSERT INTO `enumdt_devicetypes` (`id`, `name`, `tsCreated`, `tsModified`) VALUES
(1, 'web', 1358667391, NULL),
(2, 'ipad', 1358667391, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enumrt_responsetypes`
--

CREATE TABLE IF NOT EXISTS `enumrt_responsetypes` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `tsCreated` int(11) NOT NULL,
  `tsModified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `enumrt_responsetypes`
--

INSERT INTO `enumrt_responsetypes` (`id`, `name`, `tsCreated`, `tsModified`) VALUES
(1, 'accept', 1368986337, NULL),
(2, 'decline', 1368986337, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enumup_userprofilefields`
--

CREATE TABLE IF NOT EXISTS `enumup_userprofilefields` (
  `profileField_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `profileField_name` varchar(30) NOT NULL,
  `tsCreated` int(11) NOT NULL,
  `tsModified` int(11) DEFAULT NULL,
  PRIMARY KEY (`profileField_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

--
-- Dumping data for table `enumup_userprofilefields`
--

INSERT INTO `enumup_userprofilefields` (`profileField_id`, `profileField_name`, `tsCreated`, `tsModified`) VALUES
(1, 'firstName', 1263886686, 0),
(2, 'middleName', 1263886686, 0),
(3, 'lastName', 1263886686, 0),
(4, 'birthDate', 1263886686, 0),
(5, 'gender', 1263886686, 0),
(6, 'profileName', 1263886869, 0),
(7, 'phone10', 1263886869, 0),
(8, 'profilePicImageId', 1263886869, 0),
(9, 'phone10Carrier_id', 1263886869, 0),
(10, 'new_phone10_ts', 1263886869, 0),
(11, 'new_phone10_key', 1263886869, 0),
(12, 'new_phone10', 1263886869, 0),
(13, 'new_phone10Carrier_id', 1263886869, 0),
(14, 'currentLocationID_ts', 1263886869, 0),
(15, 'globalMileage', 1263886869, 0),
(16, 'fb_accessToken', 1263887205, 0),
(17, 'fb_profileURL', 1263887205, 0),
(18, 'fb_email', 1263887205, 0),
(19, 'fb_picURL', 1263887205, 0),
(20, 'fb_profilename', 1341191502, 0),
(21, 'fb_permissions', 1263887205, 1355599085),
(22, 'fb_locale', 0, 0),
(23, 'fb_name', 1263887205, 0),
(24, 'hZipCode', 1263887205, 0),
(25, 'hPhone10', 1263887205, 0),
(26, 'hPhone10on', 1263887376, 0),
(27, 'aboutMe', 1263887376, 0),
(28, 'profileImageId', 1263887541, 0),
(29, 'personalURL', 1263887541, 0),
(30, 'language', 1264202763, 0),
(31, 'timezone', 1264202763, 0),
(32, 'emailVerified', 1264202763, 0),
(33, 'emailVerifiedCode', 1264202890, 0),
(34, 'hCountryID', 1264202958, 0),
(35, 'wCountryID', 1264203014, 0),
(36, 'hPhone10CarrierID', 1264203014, 0),
(37, 'privacySettings', 1264203014, 0),
(38, 'pageViews', 1264203014, 0),
(39, 'avgRating', 1264203014, 0),
(40, 'setLocation', 1264202856, 0),
(41, 'timezone', 1264202856, 0),
(42, 'emailVerified', 1264202856, 0),
(43, 'emailVerifiedCode', 1264202856, 0),
(44, 'hPhone10Verified', 1264202856, 0),
(45, 'hPhone10Code', 1264202856, 0),
(46, 'location_home', 1264202856, 0),
(47, 'location_work', 1264202856, 0),
(48, 'location_manual', 1264202856, 0),
(49, 'location_ip', 1264202856, 0),
(50, 'ip', 1264203648, 0),
(51, 'ipLat', 1264203648, 0),
(52, 'ipLng', 1264203648, 0),
(53, 'mobileDeviceID', 1264209561, 0),
(54, 'mileageDistance', 1264220421, 0),
(55, 'metric', 1264220421, 0),
(56, 'new_password', 1299434715, 0),
(57, 'new_password_ts', 1299434715, 0),
(58, 'new_password_key', 1299434715, 0),
(59, 'twitterRequestToken', 1298280421, 1298280844),
(60, 'twitterAccessToken', 1298280875, 0),
(61, 'onTwitter', 1300311085, 0);

-- --------------------------------------------------------

--
-- Table structure for table `enumut_usertype`
--

CREATE TABLE IF NOT EXISTS `enumut_usertype` (
  `userType_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `userType_name` varchar(30) NOT NULL,
  `tsCreate` int(11) NOT NULL,
  `tsUpdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`userType_id`),
  UNIQUE KEY `profile_field_id` (`userType_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `enumut_usertype`
--

INSERT INTO `enumut_usertype` (`userType_id`, `userType_name`, `tsCreate`, `tsUpdate`) VALUES
(1, 'admin', 1264416217, 0),
(2, 'guest', 1319939599, 0),
(3, 'member', 1319939599, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fbId` varchar(100) DEFAULT NULL,
  `usernameEmail` varchar(255) NOT NULL,
  `profileName` varchar(25) DEFAULT NULL,
  `password` varchar(125) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `bucks` mediumint(9) DEFAULT NULL,
  `points` mediumint(9) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `tsCreated` int(11) NOT NULL,
  `tsLastLogin` int(11) DEFAULT NULL,
  `expiry` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `profileName` (`profileName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_images`
--

CREATE TABLE IF NOT EXISTS `users_images` (
  `file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `ranking` int(10) unsigned NOT NULL,
  `privacy` mediumint(9) DEFAULT NULL,
  `tsCreated` int(11) NOT NULL,
  `tsModified` int(11) DEFAULT NULL,
  `tsDeleted` int(11) DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `image_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_profile`
--

CREATE TABLE IF NOT EXISTS `users_profile` (
  `user_id` bigint(20) unsigned NOT NULL,
  `profile_key` varchar(5) NOT NULL,
  `profile_value` text NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`profile_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

CREATE TABLE IF NOT EXISTS `users_sessions` (
  `user_id` int(11) NOT NULL,
  `sessionId` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `session_type` smallint(6) NOT NULL,
  `device_id` mediumint(9) NOT NULL,
  `device_version` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `version` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'The Version number - 1.1.3',
  `lat` decimal(10,6) DEFAULT NULL,
  `lon` decimal(10,6) DEFAULT NULL,
  `tsCreated` int(11) NOT NULL,
  `tsModified` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
