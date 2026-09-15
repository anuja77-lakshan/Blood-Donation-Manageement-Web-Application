SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create Database
CREATE DATABASE IF NOT EXISTS `bloodlink_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bloodlink_db`;

-- 1. Users Table (Donors, Staff, Admins)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL DEFAULT 'O+',
  `role` enum('donor','hospital','camp_organizer','admin') NOT NULL DEFAULT 'donor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Blood Camps Table
CREATE TABLE IF NOT EXISTS `blood_camps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `camp_name` varchar(255) NOT NULL,
  `org_name` varchar(255) NOT NULL,
  `camp_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `cover_image` varchar(255) DEFAULT '../images/card1.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Contact Messages Table
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Donations Table (with Foreign Key linked to Users)
CREATE TABLE IF NOT EXISTS `donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `donation_date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `camp_name` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_donations_user` (`user_id`),
  CONSTRAINT `fk_donations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- INSERTING DUMMY DATA FOR TESTING
-- --------------------------------------------------------

-- Insert Users (Resolved ID conflicts)
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `phone`, `blood_group`, `role`) VALUES
(1, 'Senith Chethiya', 'sc@gmail.com', '0771234567', 'O+', 'donor'),
(2, 'Kasun Perera', 'kasun.p@gmail.com', '0771112222', 'O+', 'donor'),
(3, 'Nimali Fernando', 'nimali.f@gmail.com', '0719876543', 'B+', 'donor'),
(4, 'Amal Silva', 'amal.s@gmail.com', '0765551234', 'AB-', 'donor');

-- Insert Blood Camps
INSERT IGNORE INTO `blood_camps` (`id`, `camp_name`, `org_name`, `camp_date`, `start_time`, `end_time`, `location`, `cover_image`) VALUES
(1, 'Blood camp', 'RUSL', '2026-11-20', '06:00:00', '11:00:00', 'Rajarata University', '../uploads/1789362488_6aa7813843048.jpg'),
(2, 'City Central Blood Drive', 'Karapitiya National Hospital', '2026-09-15', '09:00:00', '13:00:00', 'Karapitiya Hospital, Galle.', '../uploads/1789365849_6aa78e59ca041.png');

-- Insert Contact Messages
INSERT IGNORE INTO `contact_messages` (`id`, `first_name`, `last_name`, `email`, `message`, `submitted_at`) VALUES
(1, 'Senith', 'chethiya', 'sc@gmail.com', 'bbb', '2026-09-14 11:45:42');

-- Insert Donations (Linked to the correct users)
INSERT IGNORE INTO `donations` (`id`, `user_id`, `donation_date`, `location`, `camp_name`, `status`) VALUES
(1, 1, '2026-08-12 10:00:00', 'Colombo', 'National Blood Transfusion Service', 'Completed'),
(2, 1, '2026-02-15 11:30:00', 'Kandy', 'Kandy General Hospital Camp', 'Completed'),
(3, 1, '2025-08-10 09:15:00', 'Galle', 'Karapitiya Blood Bank', 'Completed'),
(4, 2, '2025-11-15 10:30:00', 'National Blood Centre - Narahenpita', 'Colombo Central Drive', 'Completed'),
(5, 2, '2026-03-10 14:15:00', 'Teaching Hospital Anuradhapura', 'Rajarata University Camp', 'Completed');

COMMIT;