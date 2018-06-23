CREATE TABLE `volunteers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `person_id` int(10) unsigned NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `days` int unsigned NOT NULL,
  FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB';