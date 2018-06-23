CREATE TABLE `admins` (
	`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` varchar(30) NOT NULL,
	`surname` varchar(30) NOT NULL,
	`mail` varchar(50) NOT NULL,
	`password` varchar(255) NOT NULL,
	`date_update` datetime NOT NULL,
	`liane` tinyint(1) unsigned NOT NULL
) ENGINE='InnoDB';