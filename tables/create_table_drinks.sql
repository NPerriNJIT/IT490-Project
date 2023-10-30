CREATE TABLE IF NOT EXISTS `DRINKS` (
        `drink_id` INT PRIMARY KEY NOT NULL UNIQUE,
        `drink_name` VARCHAR(60) NOT NULL,
        `drink_tags` VARCHAR(255),
        `alcoholic` BOOLEAN,
        `ingredients` TEXT,
        `measurements` TEXT,
        `instructions` TEXT,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);CREATE TABLE IF NOT EXISTS `DRINKS` (
        `drink_id` INT PRIMARY KEY NOT NULL UNIQUE,
        `drink_name` VARCHAR(60) NOT NULL,
        `drink_tags` VARCHAR(255) NOT NULL,
        `alcoholic` BOOLEAN NOT NULL,
        `ingredients` TEXT NOT NULL,
        `measurements` TEXT NOT NULL,
        `instructions` TEXT NOT NULL,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
;

