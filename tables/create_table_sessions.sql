CREATE TABLE IF NOT EXISTS `Sessions` (
        `id` VARCHAR(255) PRIMARY KEY NOT NULL UNIQUE,
        `data` TEXT,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`accessed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
;

