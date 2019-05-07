-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `admins` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(30) COLLATE utf8_czech_ci NOT NULL,
	`surname` varchar(30) COLLATE utf8_czech_ci NOT NULL,
	`mail` varchar(50) COLLATE utf8_czech_ci NOT NULL,
	`password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	`date_update` datetime NOT NULL,
	`liane` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `groups` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(30) COLLATE utf8_czech_ci NOT NULL,
	`password` varchar(60) COLLATE utf8_czech_ci NOT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `groups_x_persons` (
	`group_id` int(10) unsigned NOT NULL,
	`person_id` int(10) unsigned NOT NULL,
	PRIMARY KEY (`group_id`,`person_id`),
	KEY `person_id` (`person_id`),
	CONSTRAINT `groups_x_persons_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
	CONSTRAINT `groups_x_persons_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `persons` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(30) COLLATE utf8_czech_ci NOT NULL,
	`surname` varchar(30) COLLATE utf8_czech_ci NOT NULL,
	`rc` varchar(11) COLLATE utf8_czech_ci DEFAULT NULL,
	`mail` varchar(50) COLLATE utf8_czech_ci NOT NULL,
	`phone` varchar(9) COLLATE utf8_czech_ci DEFAULT NULL,
	`address` text COLLATE utf8_czech_ci DEFAULT NULL,
	`password` char(60) COLLATE utf8_czech_ci NOT NULL,
	`note` text COLLATE utf8_czech_ci DEFAULT NULL,
	`date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	PRIMARY KEY (`id`),
	UNIQUE KEY `mail` (`mail`),
	UNIQUE KEY `rc` (`rc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `visits` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_start` datetime NOT NULL,
	`date_end` datetime NOT NULL,
	`date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`date_limit` datetime DEFAULT NULL,
	`open` tinyint(1) unsigned NOT NULL DEFAULT 1,
	`group_id` int(10) unsigned DEFAULT NULL,
	`person_id` int(10) unsigned DEFAULT NULL,
	`type` tinyint(3) unsigned NOT NULL DEFAULT 1,
	`note` text COLLATE utf8_czech_ci DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `person_id` (`person_id`),
	KEY `group_id` (`group_id`),
	CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `visits_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `visit_requests` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`person_id` int(10) unsigned NOT NULL,
	`date_start` date NOT NULL,
	`date_end` date NOT NULL,
	`date_add` datetime NOT NULL,
	`active` tinyint(1) NOT NULL DEFAULT 1,
	`days` tinyint(3) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `person_id` (`person_id`),
	CONSTRAINT `visit_requests_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
