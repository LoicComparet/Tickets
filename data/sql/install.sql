-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Ven 14 Avril 2017 à 18:29
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `gac`
--
CREATE DATABASE IF NOT EXISTS `gac`
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci;
USE `gac`;

-- --------------------------------------------------------

--
-- Structure de la table `conso`
--

DROP TABLE IF EXISTS `conso`;
CREATE TABLE IF NOT EXISTS `conso` (
  `ACCOUNT`    INT(11)      NOT NULL,
  `INVOICE`    INT(11)      NOT NULL,
  `CLIENT`     INT(11)      NOT NULL,
  `DATE_CONSO` DATETIME     NOT NULL,
  `REAL_DATA`  VARCHAR(255) NOT NULL,
  `BILL_DATA`  VARCHAR(255) NOT NULL,
  `TYPE_DATA`  VARCHAR(255) NOT NULL,
  KEY `date_conso_idx` (`DATE_CONSO`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;
