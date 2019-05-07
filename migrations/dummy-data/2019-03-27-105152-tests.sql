-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

TRUNCATE `visits`;
TRUNCATE `groups_x_persons`;
TRUNCATE `persons`;
TRUNCATE `groups`;
TRUNCATE `admins`;

INSERT INTO `admins` (`id`, `name`, `surname`, `mail`, `password`, `date_update`, `liane`) VALUES
	(1,	'Tomáš',	'Admin',	'tomas.admin@gmail.cz',	'$2y$10$Qe6X1k4XO4qaRJ96xD7Oo.LFgFe41XkxQYIu6E9TxucqurWKRWNFy',	NOW(),	0);

INSERT INTO `groups` (`id`, `title`, `password`, `active`) VALUES
	(1,	'Skupina 1',	'$2y$10$sgnsc/1cyT7A0GJ3ZnbHR.J/q/SRmUAW1K//p8uz6bGSHchd67TXS',	1),
	(2,	'Skupina 2',	'$2y$10$3HCRorL.AVRB8ZArQNnJ5.x6/d9TJXew5ildGIOn61fdRl62ucLZK',	1),
	(3,	'Skupina 3',	'$2y$10$sc4d8v3JY5Viy/v2LzDJSujXoYGtdI1vxk8r508xn9g/uR6y09UyS',	0);

INSERT INTO `persons` (`id`, `name`, `surname`, `rc`, `mail`, `phone`, `address`, `password`, `note`, `date_update`) VALUES
	(1,	'Tomáš',	'Blbý',		'120416/0000',	'tomas.blby@centrum.cz',	'160911277',	'Horni dolní',	'$2y$10$K8jeErjP2ES0dcYH2YB/KOREemsTVpQddykU0VqqaY7/o6KXQWuQa',	NULL,	NOW()),
	(2,	'Adam',		'Sádlo',	'120517/0000',	'adam.sadlo@seznam.cz',		'160911278',	'Praha',				'$2y$10$1E..hE0mW4InwyarmGYNg..8ha.H93WB.bv5EjoR4Z7SHVrTmLyfS',	NULL,	NOW()),
	(3,	'Petr',		'Tvrdý',	'120618/0000',	'petr.tvrdy@email.cz',		'160911279',	'Liberec',			'$2y$10$Lld8GoJP18lqFAzIAtIJ2ewS7p1CZeJXq2EcaUhCf/wwYOg0MdzBm',	NULL,	NOW()),
	(4,	'Karel',	'Varel',	'120719/0000',	'karel.varel@atlas.cz',		'160911280',	'Benešov',			'$2y$10$ztdVbwt60th6XCuouHgN6.pFf6i8wIDBgjymOVaqjfYuTmgSFnjMW',	NULL,	NOW());

INSERT INTO `visits` (`id`, `date_start`, `date_end`, `date_update`, `date_limit`, `open`, `group_id`, `person_id`, `type`, `note`) VALUES
	(1,		'2100-02-01 07:00:00',	'2100-02-01 07:45:00',	NOW(),	'2100-01-20 08:45:00',	1,	1,		NULL,	1,	NULL),
	(2,		'2100-02-01 07:45:00',	'2100-02-01 08:30:00',	NOW(),	'2100-01-20 08:45:00',	0,	1,		1,		2,	NULL),
	(3,		'2100-02-01 08:30:00',	'2100-02-01 09:15:00',	NOW(),	'2100-01-20 08:45:00',	0,	1,		NULL,	1,	NULL),
	(4,		'2100-02-02 07:00:00',	'2100-02-02 07:45:00',	NOW(),	'2100-01-21 08:45:00',	1,	2,		NULL,	1,	NULL),
	(5,		'2100-02-02 07:45:00',	'2100-02-02 08:30:00',	NOW(),	'2100-01-21 08:45:00',	0,	2,		2,		2,	NULL),
	(6,		'2100-02-02 08:30:00',	'2100-02-02 09:15:00',	NOW(),	'2100-01-21 08:45:00',	1,	3,		NULL,	1,	NULL),
	(7,		'2100-02-03 07:00:00',	'2100-03-02 07:45:00',	NOW(),	'2100-01-22 08:45:00',	0,	3,		3,		1,	NULL),
	(8,		'2100-02-03 07:45:00',	'2100-03-02 08:30:00',	NOW(),	'2100-01-22 08:45:00',	1,	NULL,	NULL,	1,	NULL),
	(9,		'2100-02-03 08:30:00',	'2100-03-02 09:15:00',	NOW(),	'2100-01-22 08:45:00',	0,	NULL,	4,		1,	NULL),
	(11,	'2000-02-01 07:00:00',	'2000-02-01 07:45:00',	NOW(),	'2000-01-20 08:45:00',	1,	1,		NULL,	1,	NULL),
	(12,	'2000-02-01 07:45:00',	'2000-02-01 08:30:00',	NOW(),	'2000-01-20 08:45:00',	0,	1,		1,		2,	NULL),
	(13,	'2000-02-01 08:30:00',	'2000-02-01 09:15:00',	NOW(),	'2000-01-20 08:45:00',	0,	1,		NULL,	1,	NULL),
	(14,	'2000-02-02 07:00:00',	'2000-02-02 07:45:00',	NOW(),	'2000-01-21 08:45:00',	1,	2,		NULL,	1,	NULL),
	(15,	'2000-02-02 07:45:00',	'2000-02-02 08:30:00',	NOW(),	'2000-01-21 08:45:00',	0,	2,		2,		2,	NULL),
	(16,	'2000-02-02 08:30:00',	'2000-02-02 09:15:00',	NOW(),	'2000-01-21 08:45:00',	1,	3,		NULL,	1,	NULL),
	(17,	'2000-02-03 07:00:00',	'2000-03-02 07:45:00',	NOW(),	'2000-01-22 08:45:00',	0,	3,		3,		1,	NULL),
	(18,	'2000-02-03 07:45:00',	'2000-03-02 08:30:00',	NOW(),	'2000-01-22 08:45:00',	1,	NULL,	NULL,	1,	NULL),
	(19,	'2000-02-03 08:30:00',	'2000-03-02 09:15:00',	NOW(),	'2000-01-22 08:45:00',	0,	NULL,	4,		1,	NULL);

INSERT INTO `groups_x_persons` (`group_id`, `person_id`) VALUES
	(1, 1),
	(2, 2),
	(3, 3),
	(1, 4);