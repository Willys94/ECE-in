-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 01 juin 2025 à 18:22
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecein`
--

-- --------------------------------------------------------

--
-- Structure de la table `album`
--

DROP TABLE IF EXISTS `album`;
CREATE TABLE IF NOT EXISTS `album` (
  `id_album` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_album`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `album`
--

INSERT INTO `album` (`id_album`, `id_utilisateur`, `nom`, `description`, `date_creation`) VALUES
(1, 3, 'f', 'f', '2025-05-30 18:31:17'),
(2, 1, 'sh', '', '2025-06-01 12:30:45');

-- --------------------------------------------------------

--
-- Structure de la table `album_photo`
--

DROP TABLE IF EXISTS `album_photo`;
CREATE TABLE IF NOT EXISTS `album_photo` (
  `id_album` int NOT NULL,
  `id_publication` int NOT NULL,
  PRIMARY KEY (`id_album`,`id_publication`),
  KEY `id_publication` (`id_publication`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `album_photo`
--

INSERT INTO `album_photo` (`id_album`, `id_publication`) VALUES
(1, 2),
(2, 0),
(2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

DROP TABLE IF EXISTS `candidature`;
CREATE TABLE IF NOT EXISTS `candidature` (
  `id_candidature` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_emploi` int NOT NULL,
  `date_candidature` datetime NOT NULL,
  `statut` enum('en_attente','accepte','refuse') DEFAULT 'en_attente',
  PRIMARY KEY (`id_candidature`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_emploi` (`id_emploi`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `candidature`
--

INSERT INTO `candidature` (`id_candidature`, `id_utilisateur`, `id_emploi`, `date_candidature`, `statut`) VALUES
(1, 3, 1, '2025-05-30 18:09:26', 'en_attente');

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE IF NOT EXISTS `commentaire` (
  `id_commentaire` int NOT NULL AUTO_INCREMENT,
  `id_publication` int DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `texte` text,
  `date_commentaire` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_commentaire`),
  KEY `id_publication` (`id_publication`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `commentaire`
--

INSERT INTO `commentaire` (`id_commentaire`, `id_publication`, `id_utilisateur`, `texte`, `date_commentaire`) VALUES
(1, 8, 5, 'nul', '2025-05-30 17:42:16');

-- --------------------------------------------------------

--
-- Structure de la table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
CREATE TABLE IF NOT EXISTS `connexion` (
  `id_utilisateur_source` int NOT NULL,
  `id_utilisateur_cible` int NOT NULL,
  `statut` enum('en_attente','accepte','refuse') DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur_source`,`id_utilisateur_cible`),
  KEY `id_utilisateur_cible` (`id_utilisateur_cible`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `connexion`
--

INSERT INTO `connexion` (`id_utilisateur_source`, `id_utilisateur_cible`, `statut`) VALUES
(3, 1, 'accepte'),
(1, 3, 'accepte'),
(3, 6, 'en_attente'),
(5, 3, 'accepte'),
(1, 5, 'accepte');

-- --------------------------------------------------------

--
-- Structure de la table `emploi`
--

DROP TABLE IF EXISTS `emploi`;
CREATE TABLE IF NOT EXISTS `emploi` (
  `id_emploi` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `description` text,
  `entreprise` varchar(255) DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `date_publication` date DEFAULT NULL,
  `publie_par` int DEFAULT NULL,
  PRIMARY KEY (`id_emploi`),
  KEY `publie_par` (`publie_par`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `emploi`
--

INSERT INTO `emploi` (`id_emploi`, `titre`, `description`, `entreprise`, `lieu`, `date_publication`, `publie_par`) VALUES
(1, 'Enseignant permanent', 'Enseignement des matières informatiques à l\'ECE Paris. CDI, temps plein, expérience requise.', 'ECE Paris', 'Paris', '2025-05-30', 4),
(2, 'Vacataire en mathématiques', 'Cours de soutien en mathématiques pour les étudiants de première année. Contrat vacataire, horaires flexibles.', 'Omnes Education', 'Paris', '2025-05-30', 4),
(3, 'Stage développement web', 'Stage de 6 mois en développement web chez Capgemini. Technologies : PHP, JavaScript, MySQL.', 'Capgemini', 'Paris', '2025-05-30', 4),
(4, 'Apprentissage Data Analyst', 'Contrat d\'apprentissage en analyse de données. Formation assurée, alternance école/entreprise.', 'BNP Paribas', 'Paris', '2025-05-30', 4),
(5, 'Stage Erasmus', 'Stage de 3 mois dans une entreprise partenaire en Allemagne. Projet international, anglais courant requis.', 'Entreprise partenaire', 'Berlin', '2025-05-30', 4),
(6, 'CDD Assistant administratif', 'CDD de 12 mois pour assister le service administratif d\'Omnes Education.', 'Omnes Education', 'Paris', '2025-05-30', 4),
(7, 'CDI Responsable pédagogique', 'Responsable pédagogique pour la filière ingénierie. CDI, management d\'équipe.', 'ECE Paris', 'Paris', '2025-05-30', 4),
(8, 'Stage marketing digital', 'Stage de 4 mois en marketing digital, gestion des réseaux sociaux et campagnes publicitaires.', 'Start-up Innov', 'Lyon', '2025-05-30', 4),
(9, 'Alternance ingénieur systèmes', 'Alternance de 2 ans en ingénierie systèmes, missions DevOps.', 'Société Générale', 'Paris', '2025-05-30', 4),
(10, 'Vacataire informatique', 'Cours d\'initiation à la programmation pour étudiants de première année. Contrat vacataire.', 'Omnes Education', 'Lille', '2025-05-30', 4);

-- --------------------------------------------------------

--
-- Structure de la table `groupe`
--

DROP TABLE IF EXISTS `groupe`;
CREATE TABLE IF NOT EXISTS `groupe` (
  `id_groupe` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_groupe`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `groupe`
--

INSERT INTO `groupe` (`id_groupe`, `nom`, `date_creation`) VALUES
(1, 's', '2025-05-30 18:54:16');

-- --------------------------------------------------------

--
-- Structure de la table `groupe_membre`
--

DROP TABLE IF EXISTS `groupe_membre`;
CREATE TABLE IF NOT EXISTS `groupe_membre` (
  `id_groupe` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_groupe`,`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `groupe_membre`
--

INSERT INTO `groupe_membre` (`id_groupe`, `id_utilisateur`) VALUES
(1, 1),
(1, 3),
(1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `expediteur_id` int DEFAULT NULL,
  `destinataire_id` int DEFAULT NULL,
  `contenu` text,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  KEY `expediteur_id` (`expediteur_id`),
  KEY `destinataire_id` (`destinataire_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id_message`, `expediteur_id`, `destinataire_id`, `contenu`, `date_envoi`) VALUES
(1, 1, 3, '4\r\n', '2025-05-27 21:13:37'),
(2, 1, 3, '4\r\n', '2025-05-27 21:16:11'),
(3, 3, 1, 'hey\r\n', '2025-05-27 21:16:52'),
(4, 3, 3, '4\r\n', '2025-05-27 21:17:18'),
(5, 3, 1, 'salut test\r\n', '2025-05-28 17:12:56'),
(6, 1, 3, 'sa', '2025-06-01 13:23:50'),
(7, 1, 3, 'con', '2025-06-01 13:23:55'),
(8, 3, 1, 'toi', '2025-06-01 13:24:11'),
(9, 3, 1, '[Partage de publication] slt\r\n', '2025-06-01 19:04:00'),
(10, 3, 1, '[Partage de publication] je suis choco\n(Média : uploads/1748369983_photo moi.jpg)', '2025-06-01 19:04:11'),
(11, 1, 3, '[Partage de publication] slt\r\n', '2025-06-01 19:06:14'),
(12, 1, 3, '[Partage de publication] slt\r\n', '2025-06-01 19:31:47'),
(13, 1, 3, '[Partage de publication] salut', '2025-06-01 19:36:28');

-- --------------------------------------------------------

--
-- Structure de la table `message_groupe`
--

DROP TABLE IF EXISTS `message_groupe`;
CREATE TABLE IF NOT EXISTS `message_groupe` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `id_groupe` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `contenu` text,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `message_groupe`
--

INSERT INTO `message_groupe` (`id_message`, `id_groupe`, `id_utilisateur`, `contenu`, `date_envoi`) VALUES
(1, 1, 3, 'hey', '2025-05-30 18:58:21');

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id_notification` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `message` text,
  `date_notif` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notification`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `notification_officielle`
--

DROP TABLE IF EXISTS `notification_officielle`;
CREATE TABLE IF NOT EXISTS `notification_officielle` (
  `id_notification` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `date_evenement` datetime DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_notification`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `notification_officielle`
--

INSERT INTO `notification_officielle` (`id_notification`, `titre`, `description`, `date_evenement`, `source`) VALUES
(1, 'Conférence IA', 'Conférence sur l\'intelligence artificielle organisée par l\'ECE.', '2025-06-15 18:00:00', 'ECE'),
(2, 'Forum Entreprises', 'Rencontre avec les entreprises partenaires d\'Omnes Education.', '2025-06-20 14:00:00', 'Omnes Education');

-- --------------------------------------------------------

--
-- Structure de la table `partage`
--

DROP TABLE IF EXISTS `partage`;
CREATE TABLE IF NOT EXISTS `partage` (
  `id_partage` int NOT NULL AUTO_INCREMENT,
  `id_publication` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `date_partage` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_partage`),
  KEY `id_publication` (`id_publication`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `partage`
--

INSERT INTO `partage` (`id_partage`, `id_publication`, `id_utilisateur`, `date_partage`) VALUES
(1, 8, 5, '2025-05-30 15:37:07'),
(2, 8, 5, '2025-05-30 15:37:11'),
(3, 8, 5, '2025-05-30 15:41:58'),
(4, 2, 1, '2025-06-01 10:17:44');

-- --------------------------------------------------------

--
-- Structure de la table `publication`
--

DROP TABLE IF EXISTS `publication`;
CREATE TABLE IF NOT EXISTS `publication` (
  `id_publication` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `texte` text NOT NULL,
  `media` varchar(255) DEFAULT NULL,
  `date_pub` datetime DEFAULT CURRENT_TIMESTAMP,
  `visibilite` enum('public','amis') NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id_publication`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `publication`
--

INSERT INTO `publication` (`id_publication`, `id_utilisateur`, `texte`, `media`, `date_pub`, `visibilite`) VALUES
(1, 1, 'slt\r\n', '', '2025-05-27 20:16:39', 'public'),
(2, 1, 'je suis choco', 'uploads/1748369983_photo moi.jpg', '2025-05-27 20:19:43', 'public'),
(3, 3, 'hey\r\n', '', '2025-05-27 20:35:36', 'public'),
(4, 3, 'salut', '', '2025-05-28 17:10:56', 'public'),
(10, 3, 'slt\r\n', '', '2025-05-30 18:34:06', 'public');

-- --------------------------------------------------------

--
-- Structure de la table `reaction`
--

DROP TABLE IF EXISTS `reaction`;
CREATE TABLE IF NOT EXISTS `reaction` (
  `id_reaction` int NOT NULL AUTO_INCREMENT,
  `id_publication` int DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `type_reaction` enum('like','love','applaudir') DEFAULT NULL,
  `date_reaction` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reaction`),
  KEY `id_publication` (`id_publication`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `reaction`
--

INSERT INTO `reaction` (`id_reaction`, `id_publication`, `id_utilisateur`, `type_reaction`, `date_reaction`) VALUES
(2, 8, 5, 'like', '2025-05-30 17:42:07');

-- --------------------------------------------------------

--
-- Structure de la table `reseau`
--

DROP TABLE IF EXISTS `reseau`;
CREATE TABLE IF NOT EXISTS `reseau` (
  `id_utilisateur` int NOT NULL,
  `id_ami` int NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_ami`),
  KEY `id_ami` (`id_ami`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `image_fond` varchar(255) DEFAULT NULL,
  `role` enum('auteur','admin') DEFAULT 'auteur',
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `photo_profil`, `image_fond`, `role`, `date_inscription`) VALUES
(1, 'Test', 'Utilisateur', 'test@ece.fr', '$2y$10$iJ73Q7icIoloz2RJ/0h45esSBexfc7xh3mRD2z/6mpshcrhCWutFS', NULL, 'uploads/fond_1_photo moi.jpg', 'auteur', '2025-06-01 12:49:10'),
(2, 'cho', 'co', 'cho@ece.fr', '$2y$10$r2Mn6C0RoCNLitwKXWzfR.4zwW2.0..kXRATHQjQVoZKixdFgq7Gm', NULL, NULL, 'auteur', '2025-06-01 12:49:10'),
(3, 'chau', 'choco', 'choco@ece.fr', '$2y$10$OUJTLcbWP3xdtHNwvqaQdev3pmoNDblh/Vuc2XLGO8U7HR8Km2dZm', 'uploads/photo_3_WhatsApp Image 2025-05-27 à 19.51.16_29968c4d.jpg', NULL, 'auteur', '2025-06-01 12:49:10'),
(4, 'Admin', 'ECE', 'admin@ece.fr', '$2y$10$9D7C1r6hNvMPAH/BUgsvo.l50vHhss2y6Urz8nSGGhPojTNuG4LgC', NULL, NULL, 'admin', '2025-06-01 12:49:10'),
(5, 'Cornichon', 'milouf', 'm@ece.fr', '$2y$10$hjtnnY63ByY42IgzYEXj8eLs57vJlN0Mc9gJ/pN/2JnlJVuA91exC', NULL, NULL, 'auteur', '2025-06-01 12:49:10'),
(6, 'tik', 'banane', 'banane@ece.fr', '$2y$10$pDv0eSBood4LgwEGSi8w6uGebv/oCZ5ErXZwBlCYO9zxrO50FUe16', NULL, NULL, 'auteur', '2025-06-01 12:49:10');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
