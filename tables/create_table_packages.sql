CREATE TABLE IF NOT EXISTS `Packages` (
	`id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT UNIQUE,
	`machine` VARCHAR(255) NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` VARCHAR(100) NOT NULL DEFAULT `qa`,
    `destination_path` TEXT NOT NULL,
	`local_path` TEXT NOT NULL,
    `version` VARCHAR(255) NOT NULL UNIQUE
)
;