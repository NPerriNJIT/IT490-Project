CREATE TABLE IF NOT EXISTS `Ratings`(
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `drink_id` int,
    `user_id` int,
    `rating` TINYINT(5),
    `comment` text,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`drink_id`) REFERENCES DRINKS(`drink_id`),
    check (`rating` > 0 AND `rating` <= 5)
)