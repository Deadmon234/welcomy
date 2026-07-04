-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.4.3 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage des données de la table welcomy.evenements : 2 rows
/*!40000 ALTER TABLE `evenements` DISABLE KEYS */;
INSERT INTO `evenements` (`id_even`, `id_utilisateur`, `nom`, `lieu`, `date_`, `description`) VALUES
	(1, 2, 'Lancement de Asso+ 2.0', 'ALVI HOTEL à DEIDO', '2026-07-15 17:00:00', NULL);
/*!40000 ALTER TABLE `evenements` ENABLE KEYS */;

-- Listage des données de la table welcomy.invites : 7 rows
/*!40000 ALTER TABLE `invites` DISABLE KEYS */;
-- INSERT INTO `invites` (`id_invite`, `email`, `nom`, `telephone`, `statut`) VALUES
-- 	(15, '', 'bonbo', '677070782', 'present'),
-- 	(16, '', 'espada', '655789809', 'present'),
-- 	(14, '', 'simple', '652435667', 'present'),
-- 	(12, '', 'Gaby', '687949911', 'present'),
-- 	(13, '', 'George', '677070782', 'present'),
-- 	(11, '', 'brice', '652236142', 'present'),
-- 	(8, '', 'Bonou Boris', '655720442', 'present'),
-- 	(10, '', 'jojo', '653342345', 'present'),
-- 	(9, '', 'Fotso Christian', '652236142', 'present'),
-- 	(17, '', 'varifle', '655709878', 'present'),
-- 	(18, '', 'jean', '699895448', 'present');
/*!40000 ALTER TABLE `invites` ENABLE KEYS */;

-- Listage des données de la table welcomy.liste_invites : 11 rows
/*!40000 ALTER TABLE `liste_invites` DISABLE KEYS */;
-- INSERT INTO `liste_invites` (`id_liste_invite`, `id_utilisateur`, `id_invite`, `id_even`, `est_present`, `a_installe_asso`, `enregistrer_par`, `date_validation`) VALUES
-- 	(1, 2, 4, 2, 0, 0, 'Fotso Christian', '2026-07-03 16:07:51'),
-- 	(2, 2, 5, 2, 0, 0, 'Fotso Christian', '2026-07-03 15:41:10'),
-- 	(3, 2, 6, 2, 0, 0, 'Fotso Christian', '2026-07-03 16:13:29'),
-- 	(5, 3, 8, 1, 1, 0, 'Fotso', '2026-07-03 17:32:36'),
-- 	(6, 2, 9, 1, 1, 0, 'Fotso Christian', '2026-07-04 13:12:34'),
-- 	(7, 2, 10, 1, 1, 0, 'Fotso Christian', '2026-07-03 17:42:44'),
-- 	(8, 3, 11, 1, 1, 0, 'Fotso', '2026-07-03 17:04:09'),
-- 	(9, 3, 12, 1, 1, 0, 'Fotso', '2026-07-03 17:05:13'),
-- 	(10, 3, 13, 1, 1, 0, 'Fotso', '2026-07-03 17:11:42'),
-- 	(11, 3, 14, 1, 1, 0, 'Fotso', '2026-07-03 17:16:29'),
-- 	(12, 2, 15, 1, 1, 0, 'Fotso Christian', '2026-07-04 13:16:55'),
-- 	(13, 2, 16, 1, 1, 0, 'Fotso Christian', '2026-07-04 13:26:18'),
-- 	(14, 2, 17, 1, 1, 0, 'Fotso Christian', '2026-07-04 13:37:05'),
-- 	(15, 3, 18, 1, 1, 0, 'Fotso', '2026-07-04 13:51:03');
/*!40000 ALTER TABLE `liste_invites` ENABLE KEYS */;

-- Listage des données de la table welcomy.presence_verifications : 0 rows
/*!40000 ALTER TABLE `presence_verifications` DISABLE KEYS */;
-- INSERT INTO `presence_verifications` (`id_verification`, `id_liste_invite`, `id_invite`, `id_even`, `statut`, `type`, `contenu`, `envoyer_par`, `date_envoi`, `etat`, `remerciement_envoye`, `remerciement_par`, `contenu_remerciement`, `date_remerciement`) VALUES
-- 	(1, 7, 10, 1, 'validated', 'whatsapp', 'Bonjour jojo,\n\nVotre présence à l\'événement "Lancement de Asso+ 2.0" est confirmée.\nDate: 2026-07-15 17:00:00\nLieu: ALVI HOTEL à DEIDO\n\nMerci de votre participation.\n\nPour tout problème lié à l\'application Asso+, veuillez nous contacter au +237 654143860.', 'Fotso Christian', '2026-07-03 17:42:44', 'envoye', 0, NULL, NULL, NULL),
-- 	(2, 15, 18, 1, 'validated', 'whatsapp', 'Bonjour jean,\n\nVotre présence à l\'événement "Lancement de Asso+ 2.0" est confirmée.\nDate: 2026-07-15 17:00:00\nLieu: ALVI HOTEL à DEIDO\n\nMerci de votre participation.\n\nPour tout problème lié à l\'application Asso+, veuillez nous contacter au +237 654143860.', 'Fotso', '2026-07-04 13:51:03', 'envoye', 1, 'Fotso Christian', 'Bonjour jean,\n\nNous tenons à vous remercier chaleureusement pour votre participation à l\'événement « Lancement de Asso+ 2.0 », qui s\'est tenu le 2026-07-15 17:00:00 à ALVI HOTEL à DEIDO.\n\nVotre présence a contribué à faire de cet événement un moment réussi. Nous espérons vous revoir très bientôt !\n\nPour toute question ou assistance concernant l\'application Asso+, notre service client Asso+ est disponible au +237 654143860.\n\nEncore merci,\nL\'équipe Asso+', '2026-07-04 14:11:10'),
-- 	(3, 8, 11, 1, 'validated', 'whatsapp', NULL, 'Fotso Christian', '2026-07-04 14:10:09', 'envoye', 1, 'Fotso Christian', 'Bonjour brice,\n\nNous tenons à vous remercier chaleureusement pour votre participation à l\'événement « Lancement de Asso+ 2.0 », qui s\'est tenu le 2026-07-15 17:00:00 à ALVI HOTEL à DEIDO.\n\nVotre présence a contribué à faire de cet événement un moment réussi. Nous espérons vous revoir très bientôt !\n\nPour toute question ou assistance concernant l\'application Asso+, notre service client Asso+ est disponible au +237 654143860.\n\nEncore merci,\nL\'équipe Asso+', '2026-07-04 14:10:09');
/*!40000 ALTER TABLE `presence_verifications` ENABLE KEYS */;

-- Listage des données de la table welcomy.users : 2 rows
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id_utilisateur`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
	(2, 'Fotso Christian', 'sylvestrechristianf@gmail.com', '$2y$10$s8W6qW/OAGTxXhx5QpD1WOxJmntRpwcIhxOhDc7SmlP3ccP5FRiW2', 'admin', '2026-07-03 12:29:49'),
	(3, 'Fotso', 'sfotsowomgne@gmail.com', '$2y$10$DswIiKie.EYQKt7TLNPk6ePHomvd49OIO9dy1yAt04q5lNAtjfS.e', 'hotesse', '2026-07-03 13:07:56');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
