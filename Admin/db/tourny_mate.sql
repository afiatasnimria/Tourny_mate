-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2025 at 09:21 PM
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
-- Database: `tourny_mate`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `pass_key` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `pass_key`) VALUES
(0, 'selim', '12345678'),
(0, '', ''),
(0, 'selim', '12345678');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `full_name`, `email`, `message`, `created_at`) VALUES
(1, 'John Doe', 'john.doe@example.com', 'I am interested in participating in the upcoming tournament. Can you provide more details about registration?', '2025-01-24 10:37:33'),
(2, 'Emily Smith', 'emily.smith@gmail.com', 'When is the next football tournament scheduled? I would like to register my team.', '2025-01-24 10:37:33'),
(3, 'Michael Johnson', 'michael.j@hotmail.com', 'I have a question about the scoring system in the recent cricket match. Could you clarify the rules?', '2025-01-24 10:37:33'),
(4, 'Sarah Williams', 'sarah.w@yahoo.com', 'Is there a way to view individual player statistics from past tournaments?', '2025-01-24 10:37:33'),
(5, 'David Brown', 'david.brown@outlook.com', 'I noticed an issue with the team registration process. Can someone help me?', '2025-01-24 10:37:33'),
(6, 'Lisa Taylor', 'lisa.taylor@gmail.com', 'What are the eligibility criteria for the upcoming sports tournament?', '2025-01-24 10:37:33'),
(7, 'Robert Wilson', 'robert.wilson@example.com', 'How can I become a tournament official or referee?', '2025-01-24 10:37:33'),
(8, 'Jennifer Lee', 'jennifer.lee@hotmail.com', 'Are there any youth sports programs available?', '2025-01-24 10:37:33'),
(9, 'Christopher Martin', 'chris.martin@yahoo.com', 'I would like to sponsor an upcoming tournament. Who should I contact?', '2025-01-24 10:37:33'),
(10, 'Amanda Garcia', 'amanda.garcia@outlook.com', 'Can you provide information about past tournament winners?', '2025-01-23 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `highlights`
--

CREATE TABLE `highlights` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` longtext DEFAULT NULL,
  `video_file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `highlights`
--

INSERT INTO `highlights` (`id`, `user_id`, `title`, `details`, `video_file`, `created_at`) VALUES
(1, 1, 'Ratione obcaecati en', 'Beatae voluptas illu', '1737830794_3978429-hd_1920_1080_24fps.mp4', '2025-01-25 18:46:34'),
(2, 1, 'Nam ducimus tempore', 'Commodo modi ex erro', '1737830884_4446327-hd_1920_1080_30fps.mp4', '2025-01-25 18:48:04'),
(4, 1, 'Labore laudantium s', 'Accusamus unde optio', '1737831259_2932301-uhd_4096_2160_24fps.mp4', '2025-01-25 18:54:19'),
(5, 1, 'Distinctio Qui expl', 'Exercitation consect', '1737831615_3978429-hd_1920_1080_24fps.mp4', '2025-01-25 19:00:15'),
(6, 1, 'Dolorem ut aliquam d', 'Ipsam aut nesciunt ', '1737831622_4446327-hd_1920_1080_30fps.mp4', '2025-01-25 19:00:22'),
(7, 1, 'Itaque voluptas cupi', 'In ullam aute incidi', '1737831630_2932301-uhd_4096_2160_24fps.mp4', '2025-01-25 19:00:30'),
(8, 1, 'Aut ad est quasi aut', 'Delectus autem temp', '1737831640_2932301-uhd_4096_2160_24fps.mp4', '2025-01-25 19:00:40'),
(9, 1, 'Nihil in sit est qui', 'Similique esse est', '1737831647_4446327-hd_1920_1080_30fps.mp4', '2025-01-25 19:00:47'),
(10, 1, 'kuddus', 'Why do we use it?\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\n\r\nWhere does it come from?\r\nContrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.\r\n\r\nThe standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.\r\n\r\nWhere can I get some?\r\nThere are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.\r\n\r\n5\r\n	paragraphs\r\n	words\r\n	bytes\r\n	lists\r\n	Start with \'Lorem\r\nipsum dolor sit amet...\'\r\n\r\nDonate: If you use this site regularly and would like to help keep the site on the Internet, please consider donating a small sum to help pay for the hosting and bandwidth bill. There is no minimum donation, any sum is appreciated - click here to donate using PayPal. Thank you for your support. Donate bitcoin: 16UQLq1HZ3CNwhvgrarV6pMoA2CDjb4tyF\r\nTranslations: Can you help translate this site into a foreign language ? Please email us with details if you can help.\r\nThere is a set of mock banners available here in three colours and in a range of standard banner sizes:\r\nBannersBannersBanners\r\nNodeJS Python Interface GTK Lipsum Rails .NET\r\nThe standard Lorem Ipsum passage, used since the 1500s\r\n\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\"\r\n\r\nSection 1.10.32 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC\r\n\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius mo', '1737832336_2932301-uhd_4096_2160_24fps.mp4', '2025-01-25 19:12:16');

-- --------------------------------------------------------

--
-- Table structure for table `individual_score`
--

CREATE TABLE `individual_score` (
  `id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `no_of_matches` int(11) DEFAULT NULL,
  `runs` int(11) DEFAULT NULL,
  `short_pitch_runs` int(11) DEFAULT NULL,
  `total_six` int(11) DEFAULT NULL,
  `total_fours` int(11) DEFAULT NULL,
  `total_three` int(11) DEFAULT 0,
  `total_two` int(11) DEFAULT 0,
  `total_one` int(11) DEFAULT 0,
  `total_wickets` int(11) DEFAULT NULL,
  `total_over` float DEFAULT NULL,
  `total_dots` int(11) DEFAULT NULL,
  `total_goals` int(11) DEFAULT 0,
  `total_saves` int(11) DEFAULT NULL,
  `total_assists` int(11) DEFAULT NULL,
  `total_wins_football` int(11) DEFAULT NULL,
  `total_wins_cricket` int(11) DEFAULT NULL,
  `total_yellow` int(11) DEFAULT NULL,
  `total_red` int(11) DEFAULT NULL,
  `positions` varchar(10) DEFAULT NULL,
  `is_out` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `individual_score`
--

INSERT INTO `individual_score` (`id`, `match_id`, `tournament_id`, `user_id`, `team_id`, `no_of_matches`, `runs`, `short_pitch_runs`, `total_six`, `total_fours`, `total_three`, `total_two`, `total_one`, `total_wickets`, `total_over`, `total_dots`, `total_goals`, `total_saves`, `total_assists`, `total_wins_football`, `total_wins_cricket`, `total_yellow`, `total_red`, `positions`, `is_out`) VALUES
(52, 2, 9, 1, 5, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(53, 2, 9, 2, 5, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(54, 2, 9, 2, 6, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_played`
--

CREATE TABLE `match_played` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team_1_id` int(11) DEFAULT NULL,
  `team_2_id` int(11) DEFAULT NULL,
  `match_day` datetime NOT NULL,
  `official_1_id` int(11) DEFAULT NULL,
  `official_2_id` int(11) DEFAULT NULL,
  `official_3_id` int(11) DEFAULT NULL,
  `match_type` varchar(10) NOT NULL,
  `match_end` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `match_played`
--

INSERT INTO `match_played` (`id`, `tournament_id`, `team_1_id`, `team_2_id`, `match_day`, `official_1_id`, `official_2_id`, `official_3_id`, `match_type`, `match_end`) VALUES
(2, 9, 5, 6, '2025-01-23 22:00:00', 2, 1, NULL, 'ODI', NULL),
(3, 9, 5, 6, '2025-01-25 19:59:00', 2, NULL, NULL, 'ODI', NULL),
(4, 13, 5, 6, '2025-01-28 13:29:00', NULL, NULL, NULL, 'T20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `image_1` varchar(255) DEFAULT NULL,
  `image_2` varchar(255) DEFAULT NULL,
  `image_3` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `user_id`, `title`, `subtitle`, `description`, `main_image`, `image_1`, `image_2`, `image_3`, `created_at`) VALUES
(5, 1, 'Non labore hic id re', 'Quo provident labor', 'ARUSHA, Tanzania (AP) — Tanzania’s president said Monday that one sample from a remote part of northern Tanzania tested positive for Marburg disease, a highly infectious virus which can be fatal in up to 88% of cases without treatment.\r\n\r\nPresident Samia Suluhu Hassan spoke in Dodoma, the capital, alongside World Health Organization Director-General Tedros Adhanom Ghebreyesus.\r\n\r\nWHO was the first to report on Jan. 14 a suspected outbreak of Marburg that had killed eight people in Tanzania’s Kagera region. Tanzanian health officials disputed the report hours later, saying tests on samples had returned negative results.\r\n\r\nHassan said Monday that further tests had confirmed a case of Marburg. Twenty-five other samples were negative, she said.\r\n\r\nLike Ebola, the Marburg virus originates in fruit bats and spreads between people through close contact with the bodily fluids of infected individuals or with surfaces, such as contaminated bedsheets.\r\n\r\nSymptoms include fever, muscle pains, diarrhea, vomiting and in some cases death from extreme blood loss. There is no authorized vaccine or treatment for Marburg.\r\n\r\nThis is the second outbreak of Marburg in Kagera since 2023. It comes exactly a month after Rwanda, which shares with a border with Kagera, declared its own outbreak of the disease was over.\r\n\r\nRwandan officials reported a total of 15 deaths and 66 cases in the outbreak first declared on Sept. 27, with the majority of those affected health care workers who handled the first patients.\r\n\r\n___\r\n\r\nThis story has been corrected to show that Tanzania’s president spoke in Dodoma, not Dar es Salaam.\r\n\r\n View comments (299)\r\nUp next\r\nRFI\r\nTanzania\'s president Hassan to run in October polls\r\nRFI\r\nMon, January 20, 2025 at 9:32 PM GMT+6·1 min read\r\n\r\n\r\nTanzania\'s President Samia Suluhu Hassan walks during a visit at the Bogor palace in Bogor, West Java on 25 January, 2024.\r\nTanzania\'s ruling party on Sunday nominated President Samia Suluhu Hassan as its candidate in general elections due in October in the east African country. Hassan took office in 2021 after the sudden death of her authoritarian predecessor John Magufuli.\r\n\r\nHer party, Chama Cha Mapinduzi (CCM), held a general assembly over the weekend at the end of which it said it had named her as its sole candidate for the October poll.\r\n\r\nAfter taking power, Hassan was initially feted for easing restrictions Magufuli had imposed on the opposition and the media in the country of around 67 million people.\r\n\r\nBut rights groups and Western governments have since criticised what they see as renewed repression.\r\n\r\nPoliticians belonging to the main opposition Chadema party have been arrested and several opposition figures have been abducted and murdered.\r\n\r\n\"We achieved many things in the past four years and I promise to deliver more in the coming term,\" Hassan said in her closing remarks.\r\n\r\n\"I urge all to maintain our unity as we go to the elections. The polls can seriously divide us but I believe we will remain united now that we have candidates,\" the president added.\r\n\r\nFreed Tanzanian opposition leaders \'beaten\' during mass arrests\r\n\r\nChallenge on the horizon\r\nThe Chadema party has not yet begun the process of selecting its candidate, but is expected to elect a new president on Tuesday.\r\n\r\n\r\nRead more on RFI English\r\n\r\nRead also:\r\nTanzania government lifts six-year ban on opposition political rallies\r\nTanzania\'s Maasai relocation scheme slammed in new HRW report\r\nTanzania approves controversial oil pipeline despite environmental concerns', 'download (3).jpeg', 'download (2).jpeg', 'download (1).jpeg', 'download.jpeg', '2025-01-20 16:22:12'),
(6, 1, 'Quia culpa sint in ', 'Autem neque reprehen', 'Odio eaque at neque ', 'download (1).jpeg', 'download (2).jpeg', 'download (3).jpeg', 'download.jpeg', '2025-01-20 18:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `id` int(11) NOT NULL,
  `sports_type` varchar(10) NOT NULL,
  `position` varchar(10) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score`
--

CREATE TABLE `score` (
  `score_id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `team_1_id` int(11) DEFAULT NULL,
  `team_2_id` int(11) DEFAULT NULL,
  `team_1_score` int(11) NOT NULL,
  `team_2_score` int(11) NOT NULL,
  `winner_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`id`, `name`, `manager_id`, `logo`) VALUES
(5, 'team robust', 1, 'download.jpeg'),
(6, 'Erasmus Fuentes', 2, 'download (3).jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `team_player`
--

CREATE TABLE `team_player` (
  `id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_player`
--

INSERT INTO `team_player` (`id`, `team_id`, `user_id`, `created_at`) VALUES
(5, 5, 1, '2025-01-20 18:25:16'),
(7, 5, 2, '2025-01-20 19:11:27'),
(8, 6, 2, '2025-01-20 20:20:12');

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

CREATE TABLE `tournament` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `venue` varchar(40) NOT NULL,
  `region` varchar(40) NOT NULL,
  `district` varchar(40) NOT NULL,
  `thana` varchar(40) NOT NULL,
  `area` varchar(40) NOT NULL,
  `tour_type` varchar(10) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`id`, `name`, `creator_id`, `venue`, `region`, `district`, `thana`, `area`, `tour_type`, `start_date`, `end_date`) VALUES
(7, 'Lucius Garrison', 1, 'Officia voluptate co', 'Qui architecto iste ', 'Minima non velit con', 'Labore sint corporis', 'Pariatur Et quo acc', 'cricket', '2026-12-12', '2027-06-22'),
(8, 'Sophia Mclaughlin', 1, 'Omnis deleniti et es', 'Quis deleniti exerci', 'Voluptates voluptate', 'Sit id animi volup', 'Iste et in adipisci ', 'cricket', '2020-03-18', '2002-05-10'),
(9, 'Breanna Sanchez', 2, 'Sit numquam sed dign', 'Ab laboris totam est', 'Illum eveniet alia', 'Quaerat doloremque a', 'Labore in qui veniam', 'football', '1978-11-13', '2007-02-03'),
(10, 'Stewart Macdonald', 1, 'Laudantium quis cil', 'Dolorem numquam cons', 'Laborum dolore dicta', 'Dolores tempora anim', 'Id dicta vitae esse', 'cricket', '2008-05-05', '1988-05-29'),
(11, 'Shelley Barry', 1, 'Voluptas lorem adipi', 'Sunt dolorem modi ip', 'Consequatur quia nih', 'Corporis aut qui ips', 'Expedita unde quis f', 'cricket', '2022-11-26', '2006-05-04'),
(12, 'Dawn Landry', 1, 'Atque cupidatat vel ', 'Facere aspernatur do', 'Dolore aut non neces', 'Corporis perspiciati', 'Velit libero est mol', 'cricket', '2006-05-10', '2018-08-17'),
(13, 'Test To', 1, 'Ipsam dolor ut obcae', 'Non dolores et eiusm', 'Iste aut id culpa re', 'Voluptatem quis nost', 'Qui voluptate nostru', 'football', '1991-09-06', '1995-12-21');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_officials`
--

CREATE TABLE `tournament_officials` (
  `id` int(11) NOT NULL,
  `official_id` int(11) DEFAULT NULL,
  `tournament_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_officials`
--

INSERT INTO `tournament_officials` (`id`, `official_id`, `tournament_id`) VALUES
(2, 1, 7),
(3, 2, 7),
(4, 1, 8),
(5, 1, 9),
(6, 2, 9),
(7, 1, 10),
(8, 2, 10),
(9, 8, 10),
(10, 2, 11),
(11, 9, 11),
(12, 8, 12),
(13, 9, 12),
(14, 8, 13),
(15, 9, 13);

-- --------------------------------------------------------

--
-- Table structure for table `tournament_request`
--

CREATE TABLE `tournament_request` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_request`
--

INSERT INTO `tournament_request` (`id`, `tournament_id`, `user_id`, `team_id`, `status`, `created_at`) VALUES
(5, 7, 1, 5, 'approved', '2025-01-20 18:25:39'),
(6, 8, 1, 5, 'approved', '2025-01-20 20:11:26'),
(7, 9, 1, 5, 'approved', '2025-01-20 20:20:30'),
(8, 9, 2, 6, 'approved', '2025-01-20 20:20:30'),
(9, 10, 1, 5, 'approved', '2025-01-21 01:29:11'),
(10, 10, 2, 6, 'pending', '2025-01-21 01:29:11'),
(11, 11, 2, 6, 'pending', '2025-01-25 12:26:00'),
(12, 12, 2, 6, 'pending', '2025-01-25 12:26:37'),
(13, 13, 1, 5, 'approved', '2025-01-25 18:25:09'),
(14, 13, 2, 6, 'approved', '2025-01-25 18:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_team`
--

CREATE TABLE `tournament_team` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_team`
--

INSERT INTO `tournament_team` (`id`, `tournament_id`, `team_id`) VALUES
(2, 7, 5),
(3, 8, 5),
(4, 9, 6),
(5, 9, 5),
(6, 10, 5),
(7, 13, 5),
(8, 13, 6);

-- --------------------------------------------------------

--
-- Table structure for table `tournament_team_score`
--

CREATE TABLE `tournament_team_score` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `wickets` int(11) DEFAULT 0,
  `goals` int(255) DEFAULT 0,
  `match_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_team_score`
--

INSERT INTO `tournament_team_score` (`id`, `tournament_id`, `team_id`, `score`, `wickets`, `goals`, `match_id`) VALUES
(37, 9, 5, 28, 0, 15, 2),
(43, 9, 6, 0, 0, 6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `userinfo`
--

CREATE TABLE `userinfo` (
  `id` int(11) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `fullName` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password_key` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT NULL,
  `phone` varchar(11) NOT NULL,
  `dp` varchar(100) DEFAULT NULL,
  `cover` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userinfo`
--

INSERT INTO `userinfo` (`id`, `username`, `fullName`, `email`, `password_key`, `role`, `phone`, `dp`, `cover`) VALUES
(1, 'xubixab', 'Mariko', 'bulevom@mailinator.com', '$2y$10$a6qpOl9hrZB5XTCZbzYZBu./4wPzFbn6/aplY/DKqNykQ5MaWLYnq', 'user', '+1 (917) 82', '../../../../uploads/user/678edcd126dad_download (3).jpeg', '../../../../uploads/user/678edcd126e8a_download (2).jpeg'),
(2, 'kutiluhyg', 'Reed Benton', 'topo@mailinator.com', '$2y$10$0AI37MlIggcPB3KlLGrPjeJrVjQNjhb.BagKRqtPbACUeRJs.Y4Z2', 'user', '+1 (221) 76', NULL, NULL),
(8, 'gelefo', 'Arsenio Boone', 'qipivibuz@mailinator.com', '$2y$10$QkR0mw0ehk.MJzlLds0SVOYEHgHeQVWrAD923plQDs7jdpLZ3ElU6', 'user', '+1 (887) 45', NULL, NULL),
(9, 'afr', 'Juliet Hogan', 'bulevom@mailinator.com', '$2y$10$HXBuTk3NvCMhBQFg7SM5h.89ES0al2zD1d0qNxRU7GVCvTvEN92j.', 'user', '1234567890', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `highlights`
--
ALTER TABLE `highlights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `individual_score`
--
ALTER TABLE `individual_score`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_individual_score_team` (`team_id`),
  ADD KEY `fk_individual_score_tournament` (`tournament_id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `match_played`
--
ALTER TABLE `match_played`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_1_id` (`team_1_id`),
  ADD KEY `team_2_id` (`team_2_id`),
  ADD KEY `official_1_id` (`official_1_id`),
  ADD KEY `official_2_id` (`official_2_id`),
  ADD KEY `official_3_id` (`official_3_id`),
  ADD KEY `fk_match_played_tournament` (`tournament_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_news_user` (`user_id`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `score`
--
ALTER TABLE `score`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `match_id` (`match_id`),
  ADD KEY `team_1_id` (`team_1_id`),
  ADD KEY `team_2_id` (`team_2_id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Indexes for table `team_player`
--
ALTER TABLE `team_player`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_team` (`team_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `tournament`
--
ALTER TABLE `tournament`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `tournament_officials`
--
ALTER TABLE `tournament_officials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `official_id` (`official_id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Indexes for table `tournament_request`
--
ALTER TABLE `tournament_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tournament_request_tournament` (`tournament_id`),
  ADD KEY `fk_tournament_request_user` (`user_id`),
  ADD KEY `fk_tournament_request_team` (`team_id`);

--
-- Indexes for table `tournament_team`
--
ALTER TABLE `tournament_team`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `tournament_team_score`
--
ALTER TABLE `tournament_team_score`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tournament_id` (`tournament_id`,`team_id`),
  ADD UNIQUE KEY `unique_tournament_team` (`tournament_id`,`team_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `userinfo`
--
ALTER TABLE `userinfo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `highlights`
--
ALTER TABLE `highlights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `individual_score`
--
ALTER TABLE `individual_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `match_played`
--
ALTER TABLE `match_played`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score`
--
ALTER TABLE `score`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `team_player`
--
ALTER TABLE `team_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tournament_officials`
--
ALTER TABLE `tournament_officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tournament_request`
--
ALTER TABLE `tournament_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tournament_team`
--
ALTER TABLE `tournament_team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tournament_team_score`
--
ALTER TABLE `tournament_team_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `userinfo`
--
ALTER TABLE `userinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `highlights`
--
ALTER TABLE `highlights`
  ADD CONSTRAINT `highlights_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `userinfo` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `individual_score`
--
ALTER TABLE `individual_score`
  ADD CONSTRAINT `fk_individual_score_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_individual_score_tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_match_id` FOREIGN KEY (`match_id`) REFERENCES `match_played` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `individual_score_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `match_played` (`id`),
  ADD CONSTRAINT `individual_score_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `userinfo` (`id`);

--
-- Constraints for table `manager`
--
ALTER TABLE `manager`
  ADD CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`);

--
-- Constraints for table `match_played`
--
ALTER TABLE `match_played`
  ADD CONSTRAINT `fk_match_played_tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `match_played_ibfk_1` FOREIGN KEY (`team_1_id`) REFERENCES `team` (`id`),
  ADD CONSTRAINT `match_played_ibfk_2` FOREIGN KEY (`team_2_id`) REFERENCES `team` (`id`),
  ADD CONSTRAINT `match_played_ibfk_3` FOREIGN KEY (`official_1_id`) REFERENCES `userinfo` (`id`),
  ADD CONSTRAINT `match_played_ibfk_4` FOREIGN KEY (`official_2_id`) REFERENCES `userinfo` (`id`),
  ADD CONSTRAINT `match_played_ibfk_5` FOREIGN KEY (`official_3_id`) REFERENCES `userinfo` (`id`);

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_user` FOREIGN KEY (`user_id`) REFERENCES `userinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `player_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`),
  ADD CONSTRAINT `player_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `userinfo` (`id`);

--
-- Constraints for table `score`
--
ALTER TABLE `score`
  ADD CONSTRAINT `score_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `match_played` (`id`),
  ADD CONSTRAINT `score_ibfk_2` FOREIGN KEY (`team_1_id`) REFERENCES `team` (`id`),
  ADD CONSTRAINT `score_ibfk_3` FOREIGN KEY (`team_2_id`) REFERENCES `team` (`id`);

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `userinfo` (`id`);

--
-- Constraints for table `team_player`
--
ALTER TABLE `team_player`
  ADD CONSTRAINT `fk_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `userinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tournament`
--
ALTER TABLE `tournament`
  ADD CONSTRAINT `tournament_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `userinfo` (`id`);

--
-- Constraints for table `tournament_officials`
--
ALTER TABLE `tournament_officials`
  ADD CONSTRAINT `tournament_officials_ibfk_1` FOREIGN KEY (`official_id`) REFERENCES `userinfo` (`id`),
  ADD CONSTRAINT `tournament_officials_ibfk_2` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`);

--
-- Constraints for table `tournament_request`
--
ALTER TABLE `tournament_request`
  ADD CONSTRAINT `fk_tournament_request_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tournament_request_tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tournament_request_user` FOREIGN KEY (`user_id`) REFERENCES `userinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tournament_team`
--
ALTER TABLE `tournament_team`
  ADD CONSTRAINT `tournament_team_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`),
  ADD CONSTRAINT `tournament_team_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`);

--
-- Constraints for table `tournament_team_score`
--
ALTER TABLE `tournament_team_score`
  ADD CONSTRAINT `tournament_team_score_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_team_score_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
