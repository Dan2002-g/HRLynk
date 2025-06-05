-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 13, 2025 at 01:40 AM
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
  `description` varchar(1000) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency`
--

INSERT INTO `competency` (`competency_id`, `competencyname`, `description`, `remarks`) VALUES
(1, 'Leadership and Navigation', 'The knowledge, skills and abilities (KSAOs) needed to navigate the organization and accomplish HR goals, to create a compelling vision and mission for HR that aligns with the strategic direction and culture of the organization, to lead and promote organizational change, to manage the implementation and execution of HR initiatives, and to promote the role of HR as a key business partner.', 'Test remark'),
(2, 'Ethical Practice', 'The knowledge, skills and abilities (KSAOs) needed to maintain high levels of personal and professional integrity, and to act as an ethical agent who promotes core values, integrity and accountability throughout the organization.', 'gwapo ko'),
(3, ' Relationship Management', 'The knowledge, skills and abilities (KSAOs) needed to create and maintain a network of professional contacts within and outside of the organization, to build and maintain relationships, to work as an effective member of a team, and to manage conflict while supporting the organization.', NULL),
(4, 'Communication', 'The knowledge, skills and abilities (KSAOs) needed to effectively craft and deliver concise and informative communications, to listen to and address the concerns of others, and to transfer and translate information from one level or unit of the organization to another.', 'teststs'),
(5, 'Global and Cultural Effectiveness', 'The knowledge, skills and abilities (KSAOs) needed to value and consider the perspectives and backgrounds of all parties, to interact with others in a global context, and to promote a diverse and inclusive workplace.The knowledge, skills and abilities (KSAOs) needed to value and consider the perspectives and backgrounds of all parties, to interact with others in a global context, and to promote a diverse and inclusive workplace.', NULL),
(6, 'Business Acumen', 'The knowledge, skills and abilities (KSAOs) needed to understand the organization\'s operations, functions, and external environment, and to apply business tools and analyses that inform HR initiatives and operations consistent with the overall strategic direction of the organization.', NULL),
(7, 'Consultation', 'The knowledge, skills and abilities (KSAOs) needed to work with organizational stakeholders to assess needs, provide guidance, and recommend solutions that align with organizational goals.', NULL),
(8, 'Critical Evaluation', 'The knowledge, skills and abilities (KSAOs) needed to collect and analyze qualitative and quantitative data, and to interpret and promote findings that evaluate HR initiatives and inform business decisions and recommendations.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `idp`
--

CREATE TABLE `idp` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `objectives` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `other_objective` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'To be reviewed.',
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `idp`
--

INSERT INTO `idp` (`id`, `userID`, `objectives`, `other_objective`, `created_at`, `status`, `remarks`) VALUES
(42, 10, 'To increase competencies of current position/designation', 'Training Development', '2025-05-02 02:53:43', 'Approved', ''),
(43, 8, 'To increase competencies of current position/designation', 'No other objective specified', '2025-05-02 02:53:47', 'Approved', '');

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
(53, 10, 42, 3, 2, 'abc', 'abv', 'Seminar with interactive role-playing exercises\n\n', 'ABC', 'ABV', 'Q4', 'Q4', 123.00),
(54, 10, 42, 4, 1, 'ABC', 'ABC', 'Blended course (combination of online modules and in-person workshops)', 'ABV', 'ABV', 'Q3', 'Q4', 555555.00),
(55, 8, 43, 2, 1, 'ABVD', 'ABCD', 'E-learning module with scenario-based ethical dilemmas', 'ABVCD', 'ABCD', 'Q4', 'Q4', 6666666.00);

-- --------------------------------------------------------

--
-- Table structure for table `learningapplication`
--

CREATE TABLE `learningapplication` (
  `learningapp_id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `trainingid` int(20) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `other_type` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `organizer` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `num_days` int(11) DEFAULT NULL,
  `num_hours` int(11) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `brief_learning` text DEFAULT NULL,
  `function` varchar(500) NOT NULL,
  `activity` varchar(500) NOT NULL,
  `period` varchar(255) NOT NULL,
  `resource_needed` varchar(255) NOT NULL,
  `moneval` varchar(1000) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learningapplication`
--

INSERT INTO `learningapplication` (`learningapp_id`, `userID`, `trainingid`, `type`, `other_type`, `title`, `organizer`, `from_date`, `to_date`, `num_days`, `num_hours`, `venue`, `brief_learning`, `function`, `activity`, `period`, `resource_needed`, `moneval`, `file_path`, `created_at`) VALUES
(28, 8, 40, 'Others', 'testing', 'test', 'test', '2025-05-21', '2025-06-06', 17, 11, 'test', 'test', 'test', 'test', 'teestt', 'test', 'test', 'documents/8/mrc01 (20(3).pdf,documents/8/KAPUY HAHAHAHA (1)(3).pdf', '2025-05-12 22:18:35');

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
  `type` varchar(255) NOT NULL,
  `others_specify` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `sponsor` varchar(100) NOT NULL,
  `fromdate` date NOT NULL,
  `todate` date NOT NULL,
  `days` int(20) NOT NULL,
  `hours` int(20) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `objectives` text NOT NULL,
  `briefreport` varchar(2000) NOT NULL,
  `synthesis` varchar(2000) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `terminal`
--

INSERT INTO `terminal` (`terminalid`, `userID`, `trainingid`, `type`, `others_specify`, `title`, `sponsor`, `fromdate`, `todate`, `days`, `hours`, `venue`, `objectives`, `briefreport`, `synthesis`, `submission_date`) VALUES
(27, 8, 40, 'Training', '', 'test', 'test', '2025-05-21', '2025-05-28', 8, 12, 'test', 'test', 'test', 'test', '2025-05-12 21:22:45');

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
  `trainingdate_end` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`trainingid`, `userID`, `idpcompetency`, `trainingtitle`, `objective`, `description`, `prioNum`, `venue`, `courseobjective`, `prevEmployNum`, `trainingdate`, `submitted_at`, `file_path`, `status`, `trainingdate_end`, `remarks`) VALUES
(38, 10, ' Relationship Management', 'Building Trust and Managing Workplace Relationships', 'Reg. Fee & Allow. Allowances', 'ABCCC', 2, 'ABV', 'ABC', 0, '2025-05-07', '2025-05-01 18:20:17', 'documents/10/test (1).pdf,documents/10/test (2).pdf', 'Rejected', '2025-05-08', NULL),
(39, 10, 'Communication', 'Mastering Professional Communication: Writing, Speaking, and Listening', 'Allowable Allowances', 'ABCC', 1, 'ABV', 'ABC', 0, '2025-05-02', '2025-05-01 18:22:14', 'documents/10/test (1).pdf,documents/10/test (2).pdf', 'Rejected', '2025-05-06', NULL),
(40, 8, 'Ethical Practice', 'Upholding Integrity: Ethics and Accountability in the Workplace', 'Reg. Fee Only', 'abccc', 1, 'ABC', 'abc', 0, '2025-05-18', '2025-05-01 18:25:41', 'documents/8/test (1).pdf,documents/8/test (2).pdf', 'Completed', '2025-05-21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training_approvals`
--

CREATE TABLE `training_approvals` (
  `approval_id` int(20) NOT NULL,
  `training_id` int(20) NOT NULL,
  `approver_role` int(11) NOT NULL,
  `approver_office` varchar(50) DEFAULT NULL,
  `approval_order` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_approvals`
--

INSERT INTO `training_approvals` (`approval_id`, `training_id`, `approver_role`, `approver_office`, `approval_order`, `status`, `remarks`, `approved_at`, `created_at`) VALUES
(1, 28, 9, '10', 1, 'pending', NULL, NULL, '2025-04-27 05:48:27'),
(2, 29, 9, '31', 1, 'pending', NULL, NULL, '2025-04-27 20:48:16'),
(3, 30, 9, '31', 1, 'pending', NULL, NULL, '2025-04-28 03:13:45'),
(4, 31, 9, '10', 1, 'pending', NULL, NULL, '2025-04-28 03:23:46'),
(5, 32, 9, '1', 1, 'pending', NULL, NULL, '2025-04-28 05:51:27'),
(6, 33, 9, '31', 1, 'pending', NULL, NULL, '2025-04-30 01:25:33'),
(7, 34, 9, '31', 1, 'pending', NULL, NULL, '2025-04-30 02:04:59'),
(8, 35, 9, '31', 1, 'pending', NULL, NULL, '2025-04-30 02:22:32'),
(9, 36, 9, '31', 1, 'pending', NULL, NULL, '2025-04-30 02:39:01'),
(10, 37, 9, '31', 1, 'pending', NULL, NULL, '2025-04-30 02:45:30'),
(11, 38, 9, '31', 1, 'pending', NULL, NULL, '2025-05-01 18:20:17'),
(12, 39, 9, '31', 1, 'pending', NULL, NULL, '2025-05-01 18:22:14'),
(13, 40, 9, '10', 1, 'pending', NULL, NULL, '2025-05-01 18:25:41');

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
(8, 'Jervene Solatorio', 8, '09655743164', 'jervenesolatorio@g.msuiit.edu.ph', '123'),
(10, 'Ismael B. Alango', 3, '12345678911', 'ismael.alango@g.msuiit.edu.ph', '123'),
(12, 'Jasmin Labrado', 8, '12345678911', 'jasmin.dugumo@g.msuiit.edu.ph', '123'),
(17, 'Ann Gaid', 11, '09111111111', 'annabelle.gaid@g.msuiit.edu.ph', '123'),
(18, 'Jimay Bigcas', 11, '09089919696', 'jimayma.bigcas@g.msuiit.edu.ph', '1234567'),
(19, 'JUDITH DUCAY', 11, '09269020235', 'judith.ducay@g.msuiit.edu.ph', 'November2017');

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
(6, 8, 10, 'staff', 'teest', 'test', '2025-04-16', 24, 2, '8_b7d234_69b90c8aea904ccc9c7bc0e153eb6008~mv2.jpg'),
(7, 6, 31, 'Office Head', 'test', 'test', '2015-03-12', 12, 1, '6_Test Profile.jpg'),
(8, 11, 11, 'Staff', 'Dummy', 'Dummy', '2025-05-09', 12, 1, '11_Test Profile.jpg'),
(9, 12, 11, 'Staff', 'test', 'test', '2025-05-02', 12, 1, '12_Test Profile.jpg'),
(10, 13, 10, 'Dean', 'Test', 'test', '2025-04-27', 1, 12, '13_b7d234_69b90c8aea904ccc9c7bc0e153eb6008~mv2.jpg'),
(11, 5, 1, 'Chancellor', 'Test', 'Regular', '2025-05-07', 12, 1, '5_b7d234_69b90c8aea904ccc9c7bc0e153eb6008~mv2.jpg'),
(12, 10, 31, 'L&amp;D', 'test', 'test', '2025-04-22', 1, 12, '10_b7d234_69b90c8aea904ccc9c7bc0e153eb6008~mv2.jpg'),
(13, 14, 10, 'Staff', 'financial assistant', 'Regular', '2023-12-12', 15, 1, '14_Test Profile.jpg'),
(14, 17, 31, 'Staff', 'test', 'Regular', '2002-02-05', 12, 2, '17_Test Profile.jpg'),
(15, 18, 31, 'Staff', 'Leave in-charge', 'Contract of Service', '1996-12-07', 15, 1, '18_Test Profile.jpg'),
(16, 19, 31, 'Administrative Assistant IV', 'Assisting in staff recruitment and payroll processing', 'Contract of Service', '2024-01-22', 14, 1, '19_Test Profile.jpg');

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
-- Indexes for table `training_approvals`
--
ALTER TABLE `training_approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `training_id` (`training_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `idp_competencies`
--
ALTER TABLE `idp_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `learningapplication`
--
ALTER TABLE `learningapplication`
  MODIFY `learningapp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `terminalid` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `trainingid` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `training_approvals`
--
ALTER TABLE `training_approvals`
  MODIFY `approval_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `UserDetailsID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `idp`
--
ALTER TABLE `idp`
  ADD CONSTRAINT `user_idp` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
