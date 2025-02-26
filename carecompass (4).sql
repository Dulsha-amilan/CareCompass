-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2025 at 03:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carecompass`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'Admin', 'admin@hospital.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Scheduled','Cancelled','Completed') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `doctor_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `doctor_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `appointment_date`, `appointment_time`, `status`, `created_at`, `doctor_id`, `notes`, `doctor_name`) VALUES
(7, 18, '2025-02-20', '22:47:00', '', '2025-02-24 15:17:03', 4, NULL, NULL),
(8, 18, '2025-02-25', '23:48:00', '', '2025-02-24 15:18:36', 5, NULL, NULL),
(9, 18, '2025-02-25', '23:43:00', 'Completed', '2025-02-24 16:13:14', 4, NULL, NULL),
(10, 18, '2025-02-25', '13:18:00', 'Completed', '2025-02-24 17:48:20', 4, NULL, NULL),
(11, 19, '2025-02-21', '04:10:00', 'Scheduled', '2025-02-24 19:40:44', 4, NULL, NULL),
(12, 18, '2025-02-27', '14:54:00', 'Completed', '2025-02-26 07:24:16', 5, NULL, NULL),
(13, 20, '2025-02-25', '23:00:00', 'Scheduled', '2025-02-26 13:30:18', 4, NULL, NULL),
(14, 20, '2025-02-27', '22:07:00', 'Scheduled', '2025-02-26 13:37:53', 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `message`, `created_at`) VALUES
(1, 18, 'saad', '2025-02-24 18:19:16'),
(2, 18, 'saadsasasasa', '2025-02-24 18:20:06'),
(3, 18, 'dffd', '2025-02-24 18:45:49'),
(4, 21, 'hghv', '2025-02-26 14:20:23'),
(5, 18, 'good bad', '2025-02-26 14:23:11');

-- --------------------------------------------------------

--
-- Table structure for table `lab_reports`
--

CREATE TABLE `lab_reports` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `report_file` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `report_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_reports`
--

INSERT INTO `lab_reports` (`id`, `patient_id`, `file_name`, `uploaded_at`, `report_file`, `uploaded_by`, `report_type`, `upload_date`, `report_date`) VALUES
(2, 18, 'IT21357930”Kiddo Labs” Child-Friendly Reading and Comprehension Coach .pdf', '2025-02-24 17:57:39', 'uploads/reports/67bcb31352acd.pdf', 4, 'Blood Test', '2025-02-24 17:57:39', '2025-02-26');

-- --------------------------------------------------------

--
-- Table structure for table `patient_feedback`
--

CREATE TABLE `patient_feedback` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `comments` text DEFAULT NULL,
  `service_type` varchar(50) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_public` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` datetime NOT NULL,
  `status` enum('Pending','Completed','Failed','Refunded') DEFAULT 'Completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `appointment_id`, `patient_id`, `amount`, `payment_method`, `transaction_id`, `payment_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 9, 18, 75.04, 'Credit Card', '234234', '2025-02-24 23:11:00', 'Completed', '2025-02-24 17:41:00', '2025-02-24 17:41:00'),
(2, 10, 18, 75.00, 'PayPal', 'as', '2025-02-26 12:53:23', 'Completed', '2025-02-26 07:23:23', '2025-02-26 07:23:23');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Doctor','Nurse','Receptionist','Admin') NOT NULL,
  `specialization` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `password`, `role`, `specialization`, `created_at`, `status`) VALUES
(4, 'Emanu', 'dulshainfo@gmail.com', '$2y$10$2WeJebB.YBV0t3SFi9rmyexd7R5diOl6uUmkrP0KPI3AhkV9FEBkq', 'Doctor', 'Pediatrician', '2025-02-24 14:04:01', 'active'),
(5, 'Ema', 'emanuinfo@gmail.com', '$2y$10$p8591RWRPCdV9Xhq9S6Q8.lQkfHQkRIeIMDIN4Pg4ajwRm3prYlEW', 'Doctor', 'Oncologist', '2025-02-24 14:14:18', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('patient','staff','admin') NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `address`, `phone_number`, `date_of_birth`, `profile_picture`) VALUES
(18, 'Dulsha Senavirathna', 'dulshainfo@gmail.com', '$2y$10$yWxSERANY6s9VA878IAPxu58IS.XgNP2cfC6Gh3fMFucmjwVvjJju', 'patient', 'dulsha1Aa', '0772267021', '2025-02-23', NULL),
(19, 'Emanu', 'emanuinfo@gmail.com', '$2y$10$HuArbp18qWBmiYxNwrDfVuo5StcW3gidT/uxzrcft7SYf5MV3lLAa', 'patient', 'dulsha1Aa', '0772267021', '2025-02-27', NULL),
(20, 'karunarathna', 'dulshakaru@gmail.com', '$2y$10$qVMPn5MXlwb34dKxu8PtjOw3FkGUu0ydwDGPwbhLiwE2KDRm8r.3C', 'patient', 'swarnawatapoth nivithigal', '0772267021', '2025-02-25', NULL),
(21, 'uhu', 'hihihi@fdgfd', '', 'patient', '', '', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lab_reports`
--
ALTER TABLE `lab_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patient_feedback`
--
ALTER TABLE `patient_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `idx_appointment_patient` (`appointment_id`,`patient_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_reports`
--
ALTER TABLE `lab_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient_feedback`
--
ALTER TABLE `patient_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_reports`
--
ALTER TABLE `lab_reports`
  ADD CONSTRAINT `lab_reports_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
