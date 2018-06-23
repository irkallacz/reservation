CREATE TABLE `groups_x_persons` (
	`group_id` int(10) unsigned NOT NULL,
	`person_id` int(10) unsigned NOT NULL,
	FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
	FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB';

ALTER TABLE `groups_x_persons`
ADD PRIMARY KEY `group_id_person_id` (`group_id`, `person_id`);