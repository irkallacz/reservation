ALTER TABLE `persons`
CHANGE `rc` `rc` varchar(11) COLLATE 'utf8_czech_ci' NULL AFTER `surname`,
CHANGE `address` `address` text COLLATE 'utf8_czech_ci' NULL AFTER `phone`;