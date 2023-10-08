CREATE TABLE IF NOT EXISTS `Users` (
	`id` INT NOT NULL AUTO_INCREMENT UNIQUE,
	`username` VARCHAR(100) NOT NULL UNIQUE,
	`password` VARCHAR(60) NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`PRIMARY KEY (`id`)
)
--This is the first table, it will be used to store basic user information.
--NOTE: I don't remember if this is going to be enough to securely store passwords, will look into it more.
