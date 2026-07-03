-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2026 at 08:25 AM
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
-- Database: `portal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `channel`
--

CREATE TABLE `channel` (
  `channel_id` int(11) NOT NULL,
  `workspace_id` int(11) NOT NULL,
  `channel_name` varchar(100) NOT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `channel`
--

INSERT INTO `channel` (`channel_id`, `workspace_id`, `channel_name`, `is_private`, `created_at`, `updated_at`) VALUES
(1, 1, 'general', 0, '2026-06-07 11:11:01', '2026-06-07 11:11:01'),
(2, 1, 'dev-chat', 0, '2026-06-07 11:11:01', '2026-06-07 11:11:01'),
(3, 2, 'designs', 1, '2026-06-07 11:11:01', '2026-06-07 11:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `channel_message`
--

CREATE TABLE `channel_message` (
  `ch_message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `channel_message`
--

INSERT INTO `channel_message` (`ch_message_id`) VALUES
(1),
(2);

-- --------------------------------------------------------

--
-- Table structure for table `channel_user`
--

CREATE TABLE `channel_user` (
  `channel_user_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `channel_user`
--

INSERT INTO `channel_user` (`channel_user_id`, `channel_id`, `user_id`, `joined_at`) VALUES
(1, 1, 1, '2026-06-07 11:12:22'),
(2, 1, 2, '2026-06-07 11:12:22'),
(3, 2, 1, '2026-06-07 11:12:22'),
(4, 3, 3, '2026-06-07 11:12:22');

-- --------------------------------------------------------

--
-- Table structure for table `direct_message`
--

CREATE TABLE `direct_message` (
  `dm_message_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `direct_message`
--

INSERT INTO `direct_message` (`dm_message_id`, `conversation_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `dm_conversation`
--

CREATE TABLE `dm_conversation` (
  `conversation_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dm_conversation`
--

INSERT INTO `dm_conversation` (`conversation_id`, `created_at`, `updated_at`) VALUES
(1, '2026-06-07 11:20:00', '2026-06-07 11:20:00'),
(2, '2026-06-07 11:20:00', '2026-06-07 11:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `dm_participant`
--

CREATE TABLE `dm_participant` (
  `dm_participant_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dm_participant`
--

INSERT INTO `dm_participant` (`dm_participant_id`, `conversation_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-06-07 11:21:45', '2026-06-07 11:21:45'),
(2, 1, 2, '2026-06-07 11:21:45', '2026-06-07 11:21:45'),
(3, 2, 2, '2026-06-07 11:21:45', '2026-06-07 11:21:45'),
(4, 2, 3, '2026-06-07 11:21:45', '2026-06-07 11:21:45');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `file_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`file_id`, `message_id`, `file_name`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 3, 'build_log.txt', 2048, '2026-06-07 11:16:47', '2026-06-07 11:16:47');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `msg_body` text DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `msg_type` varchar(50) DEFAULT 'text',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `channel_id`, `sender_id`, `parent_id`, `msg_body`, `sent_at`, `msg_type`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'Hello everyone!', '2026-06-07 11:13:38', 'text', '2026-06-07 11:13:38', '2026-06-07 11:13:38'),
(2, 1, 2, NULL, 'Hi Ali!', '2026-06-07 11:13:38', 'text', '2026-06-07 11:13:38', '2026-06-07 11:13:38'),
(3, 2, 1, NULL, 'Any updates on the build?', '2026-06-07 11:13:38', 'text', '2026-06-07 11:13:38', '2026-06-07 11:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `pinned_message`
--

CREATE TABLE `pinned_message` (
  `pin_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pinned_message`
--

INSERT INTO `pinned_message` (`pin_id`, `message_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-06-07 11:15:31', '2026-06-07 11:15:31');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `channel_id`, `created_by`, `assigned_to`, `title`, `description`, `status`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 2, 'Fix login bug', NULL, 'pending', '2025-07-01', '2026-06-07 11:18:18', '2026-06-07 11:18:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `avatar_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `is_active`, `avatar_url`, `created_at`, `updated_at`) VALUES
(1, 'ali_raza', 'ali@example.com', 'hash123', 1, 'http://img.com/ali.jpg', '2026-06-07 11:07:23', '2026-06-07 11:07:23'),
(2, 'sara_k', 'sara@example.com', 'hash456', 1, 'http://img.com/sara.jpg', '2026-06-07 11:07:23', '2026-06-07 11:07:23'),
(3, 'john_d', 'john@example.com', 'hash789', 1, NULL, '2026-06-07 11:07:23', '2026-06-07 11:07:23');

-- --------------------------------------------------------

--
-- Table structure for table `workspace`
--

CREATE TABLE `workspace` (
  `workspace_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workspace`
--

INSERT INTO `workspace` (`workspace_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Tech Team', 'Main workspace for tech', '2026-06-07 11:08:46', '2026-06-07 11:08:46'),
(2, 'Design Hub', 'UI/UX workspace', '2026-06-07 11:08:46', '2026-06-07 11:08:46');

-- --------------------------------------------------------

--
-- Table structure for table `workspace_members`
--

CREATE TABLE `workspace_members` (
  `member_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `workspace_id` int(11) NOT NULL,
  `role` varchar(50) DEFAULT 'member',
  `joined_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workspace_members`
--

INSERT INTO `workspace_members` (`member_id`, `user_id`, `workspace_id`, `role`, `joined_at`) VALUES
(1, 1, 1, 'admin', '2026-06-07 11:09:43'),
(2, 2, 1, 'member', '2026-06-07 11:09:43'),
(3, 3, 2, 'member', '2026-06-07 11:09:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `channel`
--
ALTER TABLE `channel`
  ADD PRIMARY KEY (`channel_id`),
  ADD KEY `workspace_id` (`workspace_id`);

--
-- Indexes for table `channel_message`
--
ALTER TABLE `channel_message`
  ADD PRIMARY KEY (`ch_message_id`);

--
-- Indexes for table `channel_user`
--
ALTER TABLE `channel_user`
  ADD PRIMARY KEY (`channel_user_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `direct_message`
--
ALTER TABLE `direct_message`
  ADD PRIMARY KEY (`dm_message_id`),
  ADD KEY `conversation_id` (`conversation_id`);

--
-- Indexes for table `dm_conversation`
--
ALTER TABLE `dm_conversation`
  ADD PRIMARY KEY (`conversation_id`);

--
-- Indexes for table `dm_participant`
--
ALTER TABLE `dm_participant`
  ADD PRIMARY KEY (`dm_participant_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `pinned_message`
--
ALTER TABLE `pinned_message`
  ADD PRIMARY KEY (`pin_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `workspace`
--
ALTER TABLE `workspace`
  ADD PRIMARY KEY (`workspace_id`);

--
-- Indexes for table `workspace_members`
--
ALTER TABLE `workspace_members`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `workspace_id` (`workspace_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `channel`
--
ALTER TABLE `channel`
  MODIFY `channel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `channel_message`
--
ALTER TABLE `channel_message`
  MODIFY `ch_message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `channel_user`
--
ALTER TABLE `channel_user`
  MODIFY `channel_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `direct_message`
--
ALTER TABLE `direct_message`
  MODIFY `dm_message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dm_conversation`
--
ALTER TABLE `dm_conversation`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dm_participant`
--
ALTER TABLE `dm_participant`
  MODIFY `dm_participant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pinned_message`
--
ALTER TABLE `pinned_message`
  MODIFY `pin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workspace`
--
ALTER TABLE `workspace`
  MODIFY `workspace_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `workspace_members`
--
ALTER TABLE `workspace_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `channel`
--
ALTER TABLE `channel`
  ADD CONSTRAINT `channel_ibfk_1` FOREIGN KEY (`workspace_id`) REFERENCES `workspace` (`workspace_id`);

--
-- Constraints for table `channel_user`
--
ALTER TABLE `channel_user`
  ADD CONSTRAINT `channel_user_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `channel` (`channel_id`),
  ADD CONSTRAINT `channel_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `direct_message`
--
ALTER TABLE `direct_message`
  ADD CONSTRAINT `direct_message_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `dm_conversation` (`conversation_id`);

--
-- Constraints for table `dm_participant`
--
ALTER TABLE `dm_participant`
  ADD CONSTRAINT `dm_participant_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `dm_conversation` (`conversation_id`),
  ADD CONSTRAINT `dm_participant_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`message_id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `channel` (`channel_id`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `message_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `message` (`message_id`);

--
-- Constraints for table `pinned_message`
--
ALTER TABLE `pinned_message`
  ADD CONSTRAINT `pinned_message_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`message_id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `channel` (`channel_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `workspace_members`
--
ALTER TABLE `workspace_members`
  ADD CONSTRAINT `workspace_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `workspace_members_ibfk_2` FOREIGN KEY (`workspace_id`) REFERENCES `workspace` (`workspace_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
