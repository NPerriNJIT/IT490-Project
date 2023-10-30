CREATE TABLE IF NOT EXISTS `Reviews` (
        `review_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT UNIQUE,
        `user_id` INT NOT NULL,
        `drink_id` INT NOT NULL,
        `review` TEXT,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY(user_id) REFERENCES Users(id),
    FOREIGN KEY(drink_id) REFERENCES Drinks(drink_id)
)
;