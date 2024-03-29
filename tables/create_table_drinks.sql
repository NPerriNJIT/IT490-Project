CREATE TABLE IF NOT EXISTS `Drinks` (
        `drink_id` INT PRIMARY KEY NOT NULL UNIQUE,
        `drink_name` VARCHAR(60) NOT NULL,
        `drink_tags` VARCHAR(255),
        `alcoholic` BOOLEAN NOT NULL,
        `ingredients` TEXT NOT NULL,
        `measurements` TEXT NOT NULL,
        `instructions` TEXT NOT NULL,
        `avgrating` DOUBLE,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
);

