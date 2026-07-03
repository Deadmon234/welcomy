DROP TABLE IF EXISTS `evenements`;
CREATE TABLE IF NOT EXISTS `evenements` (
  `id_even` bigint NOT NULL AUTO_INCREMENT,
  `id_utilisateur` bigint NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_` datetime NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_even`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `invites`;
CREATE TABLE IF NOT EXISTS `invites` (
  `id_invite` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `statut` enum('absent','present') COLLATE utf8mb4_general_ci DEFAULT 'absent',
  PRIMARY KEY (`id_invite`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `invites` (`id_invite`, `email`, `nom`, `telephone`, `statut`) VALUES
(1, 'sylvestrechristianfotsowomgne@gmail.com', 'fogue arthur', '+237 652236142', 'present'),
(2, 'sylvestre@gmail.com', 'veronne copin', '237 652236143', 'present'),
(3, 'sfotsowomgne@gmail.com', 'Sylvestre Christian FOTSO', '+237 652236142', 'absent');

DROP TABLE IF EXISTS `liste_invites`;
CREATE TABLE IF NOT EXISTS `liste_invites` (
  `id_liste_invite` bigint NOT NULL AUTO_INCREMENT,
  `id_invite` bigint NOT NULL,
  `id_even` bigint NOT NULL,
  `est_present` tinyint(1) DEFAULT '0',
  `a_installe_asso` tinyint(1) DEFAULT '0',
  `enregistrer_par` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_validation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_liste_invite`),
  KEY `id_invite` (`id_invite`),
  KEY `id_even` (`id_even`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `presence_verifications`;
CREATE TABLE IF NOT EXISTS `presence_verifications` (
  `id_verification` bigint NOT NULL AUTO_INCREMENT,
  `id_liste_invite` bigint NOT NULL,
  `id_invite` bigint NOT NULL,
  `id_even` bigint NOT NULL,
  `statut` enum('pending','validated','failed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `type` enum('whatsapp','sms','email') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'whatsapp',
  `contenu` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `envoyer_par` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_envoi` datetime NOT NULL,
  `etat` enum('en attente','envoye','erreur') COLLATE utf8mb4_general_ci DEFAULT 'en attente',
  PRIMARY KEY (`id_verification`),
  KEY `id_liste_invite` (`id_liste_invite`),
  KEY `id_invite` (`id_invite`),
  KEY `id_even` (`id_even`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_utilisateur` bigint NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('hotesse','admin') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id_utilisateur`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'bobo eliore', 'boboeliore@gmail.com', '$2y$10$.2dmX8.X6nqEOdpULadM4.HDunNCrxesGFeKeYVzaUB406bbbrEui', 'hotesse', '2025-04-17 14:20:50');
COMMIT;

