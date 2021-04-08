CREATE TABLE IF NOT EXISTS `orders` (
	`id` bigint (20) PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`email` varchar(256) DEFAULT NULL,
	`name` varchar(256) DEFAULT NULL,
	`address_1` longtext,
	`address_2` longtext,
	`country` longtext DEFAULT NULL,
	`phone_number` text
);