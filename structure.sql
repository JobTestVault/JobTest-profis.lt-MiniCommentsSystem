
CREATE DATABASE app;

USE app;

CREATE TABLE `user` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(30) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`password` CHAR(40) NOT NULL,
	`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP	
);

CREATE INDEX user_login ON `user` (`email`, `password`);

CREATE TABLE `comment` (
	`id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(30) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`comment` TEXT,
	`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP	
);

INSERT INTO `user` (`name`, `email`, `password`) VALUE ('Admin', 'admin@admin.org', 'd033e22ae348aeb5660fc2140aec35850c4da997');