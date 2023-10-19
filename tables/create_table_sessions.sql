CREATE TABLE IF NOT EXISTS `Sessions` (
        `session_id` VARCHAR(255) PRIMARY KEY NOT NULL UNIQUE,
        `user_id` INT FOREIGN KEY REFERENCES Users(`id`) NOT NULL,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`accessed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
;
