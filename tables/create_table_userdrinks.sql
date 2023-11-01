CREATE TABLE IF NOT EXISTS `UserDrinks` (
        `drink_id` INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
        `drink_name` VARCHAR(60) NOT NULL,
        `user_id` INT NOT NULL,
        `drink_tags` VARCHAR(255),
        `alcoholic` BOOLEAN NOT NULL,
        `is_public` BOOLEAN NOT NULL,
        `ingredients` TEXT NOT NULL,
        `measurements` TEXT NOT NULL,
        `instructions` TEXT NOT NULL,
        `avgrating` DOUBLE,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES Users(id)
        
);

