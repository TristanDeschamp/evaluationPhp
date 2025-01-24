CREATE DATABASE IF NOT EXISTS `examphp`;

USE `examphp`;


DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(120) NOT NULL,
  `password` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
);
DROP TABLE IF EXISTS `fichiers`;
CREATE TABLE IF NOT EXISTS `fichiers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `fichier` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY(`id_utilisateur`) REFERENCES utilisateurs(`id`)
);