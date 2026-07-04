-- Migration : agrandir la colonne contenu pour stocker les messages WhatsApp complets
-- Erreur corrigée : SQLSTATE[22001] Data too long for column 'contenu'

ALTER TABLE `presence_verifications`
  MODIFY COLUMN `contenu` TEXT COLLATE utf8mb4_general_ci DEFAULT NULL;
