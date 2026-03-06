-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 06 mars 2026 à 15:42
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `coinqsy_games`
--

-- --------------------------------------------------------

--
-- Structure de la table `banque`
--

CREATE TABLE `banque` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `game_slug` varchar(255) NOT NULL,
  `skin_slug` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `purchased_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `banque`
--

INSERT INTO `banque` (`id`, `user_id`, `username`, `game_slug`, `skin_slug`, `price`, `purchased_at`) VALUES
(1, 1, 'kingvan', '', 'cyan', 500, '2026-03-05 18:58:24'),
(2, 1, 'kingvan', 'snake', 'ghost', 300, '2026-03-06 04:14:43');

-- --------------------------------------------------------

--
-- Structure de la table `classement_global`
--

CREATE TABLE `classement_global` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `total_points` bigint(20) DEFAULT 0,
  `total_victoires` int(11) DEFAULT 0,
  `total_participations` int(11) DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `conversion_pack_payments`
--

CREATE TABLE `conversion_pack_payments` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `proof_image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversion_pack_payments`
--

INSERT INTO `conversion_pack_payments` (`id`, `user_id`, `proof_image`, `status`, `submitted_at`, `processed_at`, `admin_notes`) VALUES
(1, 2, 'proof_2_1772806047.png', 'approved', '2026-03-06 15:07:27', '2026-03-06 15:17:25', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `conversion_requests`
--

CREATE TABLE `conversion_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `requested_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `amount` int(11) DEFAULT 0,
  `withdrawal_number` varchar(50) DEFAULT NULL,
  `withdrawal_sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conversion_requests`
--

INSERT INTO `conversion_requests` (`id`, `user_id`, `requested_at`, `status`, `admin_notes`, `processed_at`, `amount`, `withdrawal_number`, `withdrawal_sent_at`) VALUES
(7, 1, '2026-03-06 14:01:32', 'approved', '', '2026-03-06 14:58:14', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `conversion_send`
--

CREATE TABLE `conversion_send` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coins_amount` bigint(20) NOT NULL,
  `fcfa_amount` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','paid') DEFAULT 'pending',
  `requested_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(100) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `type` enum('zhi','addplayer','other') NOT NULL DEFAULT 'zhi',
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `used_at` datetime DEFAULT NULL,
  `used_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `nombrefois` int(11) NOT NULL,
  `status` enum('active','used','expired') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `coupons`
--

INSERT INTO `coupons` (`id`, `user_id`, `code`, `pseudo`, `type`, `generated_at`, `expires_at`, `used_at`, `used_by_user_id`, `nombrefois`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'coingames_kingvan_zhi_addplayer_20260306112240', 'kingvan', 'addplayer', '2026-03-06 11:22:40', NULL, '2026-03-06 11:30:33', 3, 10, 'used', '2026-03-06 10:22:40', '2026-03-06 10:59:45');

-- --------------------------------------------------------

--
-- Structure de la table `game_scores`
--

CREATE TABLE `game_scores` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(60) NOT NULL,
  `game_slug` varchar(80) NOT NULL,
  `score` bigint(20) NOT NULL,
  `level_reached` tinyint(4) DEFAULT 1,
  `extras` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extras`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `game_scores`
--

INSERT INTO `game_scores` (`id`, `user_id`, `username`, `game_slug`, `score`, `level_reached`, `extras`, `created_at`) VALUES
(133, 1, 'kingvan', 'coinqsy-rush', 117300, 15, NULL, '2026-03-06 09:47:04'),
(138, 2, 'KRISAIDER', 'coinqsy-rush', 7950, 1, NULL, '2026-03-05 20:11:35'),
(150, 1, 'kingvan', 'snake', 190, 1, NULL, '2026-03-06 03:38:33');

-- --------------------------------------------------------

--
-- Structure de la table `game_sessions`
--

CREATE TABLE `game_sessions` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_name` varchar(100) NOT NULL,
  `score` int(11) DEFAULT 0,
  `coins_earned` int(11) DEFAULT 0,
  `played_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paris`
--

CREATE TABLE `paris` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournoi_id` int(11) NOT NULL,
  `joueur_parie_sur` int(11) NOT NULL,
  `montant` int(11) NOT NULL,
  `cote` decimal(4,2) DEFAULT 1.00,
  `statut` enum('ouvert','gagne','perdu') DEFAULT 'ouvert',
  `cree_le` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paris`
--

INSERT INTO `paris` (`id`, `user_id`, `tournoi_id`, `joueur_parie_sur`, `montant`, `cote`, `statut`, `cree_le`) VALUES
(1, 1, 1, 1, 100, 1.00, 'ouvert', '2026-03-05 18:53:26');

-- --------------------------------------------------------

--
-- Structure de la table `teaser_scores`
--

CREATE TABLE `teaser_scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `score` int(11) NOT NULL,
  `level_reached` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `teaser_scores`
--

INSERT INTO `teaser_scores` (`id`, `user_id`, `username`, `score`, `level_reached`, `created_at`) VALUES
(1, NULL, 'Anonyme', 1260, 3, '2026-03-05 14:33:11'),
(2, NULL, 'Anonyme', 510, 3, '2026-03-05 14:34:39'),
(3, 1, 'kingvan', 20, 1, '2026-03-05 20:25:03'),
(4, 1, 'kingvan', 30, 1, '2026-03-05 20:25:23');

-- --------------------------------------------------------

--
-- Structure de la table `tournament_participations`
--

CREATE TABLE `tournament_participations` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `rank` int(11) DEFAULT NULL,
  `coins_prize` int(11) DEFAULT 0,
  `joined_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tournois`
--

CREATE TABLE `tournois` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `jeu_slug` varchar(80) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `type` enum('solo','duo','equipe') DEFAULT 'solo',
  `places_max` int(11) DEFAULT 100,
  `places_restantes` int(11) DEFAULT 100,
  `frais_inscription` int(11) DEFAULT 0,
  `prix_pool` bigint(20) DEFAULT 0,
  `statut` enum('ouvert','en_cours','termine','annule') DEFAULT 'ouvert',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tournois`
--

INSERT INTO `tournois` (`id`, `nom`, `description`, `jeu_slug`, `date_debut`, `date_fin`, `type`, `places_max`, `places_restantes`, `frais_inscription`, `prix_pool`, `statut`, `created_at`) VALUES
(1, 'Coinqsy Rush Tournoi edit-1', 'Que le meilleur gagne', 'coinqsy-rush', '2026-03-05 20:08:00', '2026-03-05 20:11:00', 'solo', 2, 0, 10, 1000, 'en_cours', '2026-03-05 16:08:18');

-- --------------------------------------------------------

--
-- Structure de la table `tournoi_participations`
--

CREATE TABLE `tournoi_participations` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournoi_id` int(11) NOT NULL,
  `score` bigint(20) DEFAULT 0,
  `rank` int(11) DEFAULT NULL,
  `coins_gagnes` bigint(20) DEFAULT 0,
  `inscrit_le` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tournoi_participations`
--

INSERT INTO `tournoi_participations` (`id`, `user_id`, `tournoi_id`, `score`, `rank`, `coins_gagnes`, `inscrit_le`) VALUES
(1, 1, 1, 13500, NULL, 108, '2026-03-05 17:05:24'),
(2, 2, 1, 5400, NULL, 0, '2026-03-05 19:06:34');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `coins_balance` bigint(20) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `eligible_conversion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `coins_balance`, `created_at`, `last_login`, `eligible_conversion`) VALUES
(1, 'kingvan', 'aguehwafiqichola@gmail.com', '$2y$10$uqWjIfmBRTCZE4uoWPEo1ulMDBJkJIeuSZJbgmJwPd.ctITN8A2C.', 1000000, '2026-02-27 04:42:26', NULL, 1),
(2, 'KRISAIDER', 'aguehwafiq@outlook.com', '$2y$10$p2Nhm0tAHUmzXWSHgf/ISeHKkO1NmrXcLlhghujh3SKhToH6N8Iye', 600000, '2026-03-05 19:05:04', NULL, 0),
(3, 'bilback', 'wafiqagueh2008@gmail.com', '$2y$10$NP2SCBvMDceuXbmvgy1.suzMCaxhpf17Y/4Wkcv/HSi5/3tuaFvme', 100, '2026-03-06 11:30:33', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `user_skins`
--

CREATE TABLE `user_skins` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_slug` varchar(50) NOT NULL,
  `skin_slug` varchar(50) NOT NULL,
  `purchased_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_skins`
--

INSERT INTO `user_skins` (`id`, `user_id`, `game_slug`, `skin_slug`, `purchased_at`) VALUES
(1, 1, 'snake', 'ghost', '2026-03-06 03:34:52');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `banque`
--
ALTER TABLE `banque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_skin` (`skin_slug`);

--
-- Index pour la table `classement_global`
--
ALTER TABLE `classement_global`
  ADD PRIMARY KEY (`user_id`);

--
-- Index pour la table `conversion_pack_payments`
--
ALTER TABLE `conversion_pack_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `conversion_requests`
--
ALTER TABLE `conversion_requests`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `conversion_send`
--
ALTER TABLE `conversion_send`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `game_scores`
--
ALTER TABLE `game_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_game` (`user_id`,`game_slug`),
  ADD KEY `idx_game_score` (`game_slug`,`score`),
  ADD KEY `idx_user_game` (`user_id`,`game_slug`);

--
-- Index pour la table `game_sessions`
--
ALTER TABLE `game_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Index pour la table `paris`
--
ALTER TABLE `paris`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tournoi_id` (`tournoi_id`),
  ADD KEY `joueur_parie_sur` (`joueur_parie_sur`);

--
-- Index pour la table `teaser_scores`
--
ALTER TABLE `teaser_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_score` (`score`),
  ADD KEY `idx_created` (`created_at`);

--
-- Index pour la table `tournament_participations`
--
ALTER TABLE `tournament_participations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_rank` (`rank`);

--
-- Index pour la table `tournois`
--
ALTER TABLE `tournois`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jeu` (`jeu_slug`),
  ADD KEY `idx_date` (`date_debut`);

--
-- Index pour la table `tournoi_participations`
--
ALTER TABLE `tournoi_participations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_participation` (`user_id`,`tournoi_id`),
  ADD KEY `tournoi_id` (`tournoi_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_skins`
--
ALTER TABLE `user_skins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_skin_per_game` (`user_id`,`game_slug`,`skin_slug`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `banque`
--
ALTER TABLE `banque`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `conversion_pack_payments`
--
ALTER TABLE `conversion_pack_payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `conversion_requests`
--
ALTER TABLE `conversion_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `conversion_send`
--
ALTER TABLE `conversion_send`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `game_scores`
--
ALTER TABLE `game_scores`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT pour la table `game_sessions`
--
ALTER TABLE `game_sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paris`
--
ALTER TABLE `paris`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `teaser_scores`
--
ALTER TABLE `teaser_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tournament_participations`
--
ALTER TABLE `tournament_participations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tournois`
--
ALTER TABLE `tournois`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `tournoi_participations`
--
ALTER TABLE `tournoi_participations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user_skins`
--
ALTER TABLE `user_skins`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `classement_global`
--
ALTER TABLE `classement_global`
  ADD CONSTRAINT `classement_global_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `conversion_pack_payments`
--
ALTER TABLE `conversion_pack_payments`
  ADD CONSTRAINT `conversion_pack_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `conversion_send`
--
ALTER TABLE `conversion_send`
  ADD CONSTRAINT `conversion_send_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paris`
--
ALTER TABLE `paris`
  ADD CONSTRAINT `paris_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `paris_ibfk_2` FOREIGN KEY (`tournoi_id`) REFERENCES `tournois` (`id`),
  ADD CONSTRAINT `paris_ibfk_3` FOREIGN KEY (`joueur_parie_sur`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `tournoi_participations`
--
ALTER TABLE `tournoi_participations`
  ADD CONSTRAINT `tournoi_participations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tournoi_participations_ibfk_2` FOREIGN KEY (`tournoi_id`) REFERENCES `tournois` (`id`);

--
-- Contraintes pour la table `user_skins`
--
ALTER TABLE `user_skins`
  ADD CONSTRAINT `user_skins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
