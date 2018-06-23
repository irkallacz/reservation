ALTER TABLE `persons` DROP `group_id`;
ALTER TABLE `visits` CHANGE `group_id` `group_id` int(10) unsigned NULL AFTER `open`;
ALTER TABLE `visits` ADD INDEX `group_id` (`group_id`);
ALTER TABLE `visits` ADD FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

