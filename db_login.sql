-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 09:59 AM
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
-- Database: `db_login`
--

-- --------------------------------------------------------

--
-- Table structure for table `competency`
--

CREATE TABLE `competency` (
  `competency_id` int(11) NOT NULL,
  `competencyname` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency`
--

INSERT INTO `competency` (`competency_id`, `competencyname`, `description`) VALUES
(1, 'Communication', 'Demonstrates an understanding of the views of others and communicates in a realistic and practical manner using appropriate language. Listens attentively to views and issues of others. Selects appropriate methods of communication for each situation. Conveys and receives information effectively and builds positive working relationships.'),
(2, 'Adaptability/Flexibility', 'Adapts and works effectively in different situations, in order to carry out a variety of tasks and remains calm and level headed under pressure. Remains positive and puts difficulties in perspective.'),
(3, 'Planning and Organising', 'Is able to organise own time effectively, creates own work schedules, prioritises, prepares in advance and sets realistic timescales. Has the ability to visualise a sequence of actions needed to achieve a specific goal and how to estimate the resources required.'),
(4, 'Continuous Improvement', 'Is able to set and meet challenging goals, creating own measures and consistently seeks ways of improving performance. Is aware of own shortfalls and takes charge of personal development.'),
(5, 'Problem Solving and Decision Making', 'Is able to analyse situations, diagnose problems, identify the key issues, establish and evaluate alternative courses of action and produce a logical, practical and acceptable solution.\r\nIs able to make effective decisions on a day-to-day basis, taking ownership of decisions, demonstrating sound judgement in escalating issues where necessary.'),
(6, 'Managing and Developing Performance ', 'Is able to inspire individuals to give their best to achieve a desired result and maintains effective relationships with individuals and the team as a whole, to ensure that the team is equipped to achieve objectives set according to the overall business need. Manages the development and performance of staff through coaching, mentoring and peer support. Has the ability to understand how individuals (at all levels) operate and how best to use that understanding to achieve objectives in the most efficient and effective way. Employs an individual and supportive approach when dealing with staff issues and problems. Promotes a trusting and empathetic environment and equality of opportunity.'),
(7, 'Creative and Analytical Thinking', 'Identifies issues and takes a proactive approach to dealing with them. Seeks ways to provide added value. Formulates distinctive strategies emphasising high levels of creativethinking. Can demonstrate recognition and development of new ideas and market opportunities. Demonstrates innovation. Is able to understand, link and analyse information to understand issues, identify options and support sound decision making.'),
(8, 'Strategic Thinking and Leadership', 'Takes an overview and identifies patterns, trends and long term possibilities. Creates and shapes a vision of the future that fits in with the University’s long term objectives. Is able to articulate strategy to a wider audience.');

-- --------------------------------------------------------

--
-- Table structure for table `idp`
--

CREATE TABLE `idp` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `objectives` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `other_objective` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'To be reviewed.',
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `idp`
--

INSERT INTO `idp` (`id`, `userID`, `approved_by`, `objectives`, `other_objective`, `created_at`, `status`, `remarks`) VALUES
(4, 7, NULL, 'To meet competencies of current position/designation', 'No other objective specified', '2025-04-15 16:41:38', 'To be reviewed.', ''),
(5, 8, NULL, 'To meet competencies of current position/designation', 'N/A', '2025-04-16 07:57:27', 'Approved', '');

-- --------------------------------------------------------

--
-- Table structure for table `idp_competencies`
--

CREATE TABLE `idp_competencies` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `idp_id` int(11) DEFAULT NULL,
  `competency_id` int(11) DEFAULT NULL,
  `priority_no` int(11) DEFAULT NULL,
  `workplace_learning` varchar(5000) DEFAULT NULL,
  `social_learning` varchar(5000) DEFAULT NULL,
  `structured_learning` varchar(5000) DEFAULT NULL,
  `resources_needed` varchar(500) DEFAULT NULL,
  `accomplishment_indicator` varchar(500) DEFAULT NULL,
  `fromdate` varchar(50) NOT NULL,
  `todate` varchar(50) NOT NULL,
  `estimated_budget` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `idp_competencies`
--

INSERT INTO `idp_competencies` (`id`, `userID`, `idp_id`, `competency_id`, `priority_no`, `workplace_learning`, `social_learning`, `structured_learning`, `resources_needed`, `accomplishment_indicator`, `fromdate`, `todate`, `estimated_budget`) VALUES
(8, 7, 4, 1, 1, 'test', 'test', 'test', 'test', 'test', 'Q1', 'Q2', 123.00),
(9, 8, 5, 2, 2, 'test', 'test', 'test', 'test', 'test', 'Q2', 'Q3', 111.00);

-- --------------------------------------------------------

--
-- Table structure for table `learningapplication`
--

CREATE TABLE `learningapplication` (
  `learningapp_id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `trainingid` int(20) NOT NULL,
  `function` varchar(500) NOT NULL,
  `activity` varchar(500) NOT NULL,
  `period` varchar(255) NOT NULL,
  `resource_needed` varchar(255) NOT NULL,
  `moneval` varchar(1000) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `office`
--

CREATE TABLE `office` (
  `officeID` int(11) NOT NULL,
  `officeName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `office`
--

INSERT INTO `office` (`officeID`, `officeName`) VALUES
(1, 'Office of the Chancellor'),
(2, 'Office of the Campus Secretary'),
(3, 'Internal Audit Services Unit'),
(4, 'Office of Communications'),
(5, 'Office of the Budget Management'),
(6, 'Security and Investigation Division'),
(7, 'Office for Legal Services'),
(8, 'Center for Information and Communication Technology'),
(9, 'MSU-IIT Liaison Office'),
(10, 'College of Arts and Social Sciences'),
(11, 'College of Economics, Business and Accountancy'),
(12, 'College of Education'),
(13, 'College of Engineering'),
(14, 'College of Health Sciences'),
(15, 'College of Science and Mathematics'),
(16, 'College of Computer Studies'),
(17, 'Research Institute of Engineering and Innovative Technology'),
(18, 'Premier Research Institute of Science and Mathematics'),
(19, 'School of Interdisciplinary Studies'),
(20, 'Office of the Vice Chancellor for Academic Affairs'),
(21, 'Center for General Education'),
(22, 'Center for Advanced Education and Lifelong Learning'),
(23, 'Office of the University Registrar'),
(24, 'Center for Pedagogical Innovation'),
(25, 'University Library'),
(26, 'Office of Admissions, Scholarships and Grants'),
(27, 'Office of the National Services Training Program'),
(28, 'Office of the Vice Chancellor for Administration and Finance'),
(29, 'Cashiering Division'),
(30, 'Accounting Division'),
(31, 'Human Resource Management Division'),
(32, 'Infrastructure Services Division'),
(33, 'Supply and Property Management Division'),
(34, 'Procurement Management Division'),
(35, 'Office of Business Affairs'),
(36, 'Office of the Vice Chancellor for Research and Enterprise'),
(37, 'Office of Research Dissemination'),
(38, 'Office of Research Management'),
(39, 'Knowledge and Technology Transfer Office'),
(40, 'Technology Application and Promotion Unit'),
(41, 'MSU-IIT FabLab Mindanao'),
(42, 'iDEYA: Center for Technopreneurship Innovation'),
(43, 'Research Integrity and Compliance Office');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `roleID` int(11) NOT NULL,
  `roleName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`roleID`, `roleName`) VALUES
(1, 'Office Head (Non-Academic)'),
(2, 'Admin'),
(3, 'Learning and Development (L&D)'),
(4, 'Chief Administrative Officer (CAO)'),
(5, 'Department Head (Chairperson)'),
(6, 'Faculty (Academics)'),
(7, 'College Dean'),
(8, 'College Staff'),
(9, 'Vice Chancellor'),
(10, 'Chancellor'),
(11, 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `terminal`
--

CREATE TABLE `terminal` (
  `terminalid` int(20) NOT NULL,
  `userID` int(20) NOT NULL,
  `trainingid` int(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sponsor` varchar(100) NOT NULL,
  `fromdate` date NOT NULL,
  `todate` date NOT NULL,
  `days` int(20) NOT NULL,
  `hours` int(20) NOT NULL,
  `briefreport` varchar(2000) NOT NULL,
  `synthesis` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `trainingid` int(20) NOT NULL,
  `userID` int(20) NOT NULL,
  `idpcompetency` varchar(255) NOT NULL,
  `trainingtitle` varchar(255) NOT NULL,
  `objective` varchar(255) NOT NULL,
  `description` varchar(500) NOT NULL,
  `prioNum` int(11) NOT NULL,
  `venue` varchar(100) NOT NULL,
  `courseobjective` varchar(500) NOT NULL,
  `prevEmployNum` int(11) NOT NULL,
  `trainingdate` date NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'To be reviewed.',
  `trainingdate_end` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `empname` varchar(45) NOT NULL,
  `roleID` int(11) NOT NULL,
  `mobilenumber` varchar(20) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `empname`, `roleID`, `mobilenumber`, `email`, `password`) VALUES
(5, 'Alizedney M. Ditucalan', 10, '09056069845', 'alizedney.ditucalan@g.msuiit.edu.ph', '123'),
(6, 'Emelyn Mordeno', 4, '09060616051', 'emelyn.mordeno@g.msuiit.edu.ph', '1234'),
(7, 'Redeen Mascarinas', 6, '09056069845', 'redeenjr.mascarinas@g.msuiit.edu.ph', '123'),
(8, 'Jervene Solatorio', 8, '09655743164', 'jervenesolatorio@g.msuiit.edu.ph', '123');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `UserDetailsID` int(20) NOT NULL,
  `userID` int(20) NOT NULL,
  `officeID` int(11) NOT NULL,
  `position` varchar(50) NOT NULL,
  `jobdescription` varchar(200) NOT NULL,
  `employmentstatus` varchar(50) NOT NULL,
  `datehired` date NOT NULL,
  `monthsintheposition` int(10) NOT NULL,
  `yearsiniit` int(10) NOT NULL,
  `profile_picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`UserDetailsID`, `userID`, `officeID`, `position`, `jobdescription`, `employmentstatus`, `datehired`, `monthsintheposition`, `yearsiniit`, `profile_picture`) VALUES
(5, 7, 16, 'faculty', 'Test', 'Regular', '2024-02-16', 12, 1, '7_b7d234_69b90c8aea904ccc9c7bc0e153eb6008~mv2.jpg'),
(6, 8, 10, 'staff', 'teest', 'test', '2025-04-16', 24, 2, '8_b7d234_69b90c8aea904ccc9c7bc0e153eb6008~mv2.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `competency`
--
ALTER TABLE `competency`
  ADD PRIMARY KEY (`competency_id`);

--
-- Indexes for table `idp`
--
ALTER TABLE `idp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_idp` (`userID`);

--
-- Indexes for table `idp_competencies`
--
ALTER TABLE `idp_competencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idp_main` (`idp_id`),
  ADD KEY `competency_idp` (`competency_id`),
  ADD KEY `fk_userID` (`userID`);

--
-- Indexes for table `learningapplication`
--
ALTER TABLE `learningapplication`
  ADD PRIMARY KEY (`learningapp_id`),
  ADD KEY `userid` (`userID`),
  ADD KEY `fk_learning_training` (`trainingid`);

--
-- Indexes for table `office`
--
ALTER TABLE `office`
  ADD PRIMARY KEY (`officeID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`roleID`);

--
-- Indexes for table `terminal`
--
ALTER TABLE `terminal`
  ADD PRIMARY KEY (`terminalid`),
  ADD KEY `terminal` (`userID`),
  ADD KEY `training` (`trainingid`);

--
-- Indexes for table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`trainingid`),
  ADD KEY `training` (`userID`),
  ADD KEY `title` (`trainingtitle`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`UserDetailsID`),
  ADD KEY `details` (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `competency`
--
ALTER TABLE `competency`
  MODIFY `competency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `idp`
--
ALTER TABLE `idp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `idp_competencies`
--
ALTER TABLE `idp_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `learningapplication`
--
ALTER TABLE `learningapplication`
  MODIFY `learningapp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `office`
--
ALTER TABLE `office`
  MODIFY `officeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `terminal`
--
ALTER TABLE `terminal`
  MODIFY `terminalid` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `trainingid` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `UserDetailsID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `idp`
--
ALTER TABLE `idp`
  ADD CONSTRAINT `user_idp` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `idp_competencies`
--
ALTER TABLE `idp_competencies`
  ADD CONSTRAINT `competency_idp` FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`),
  ADD CONSTRAINT `fk_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `idp_main` FOREIGN KEY (`idp_id`) REFERENCES `idp` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
