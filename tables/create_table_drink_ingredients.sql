CREATE TABLE IF NOT EXISTS `Drink_Ingredients` (
        `drink_id` INT NOT NULL,
        `ingredient_id` INT NOT NULL,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY(drink_id) REFERENCES Drinks(drink_id)
)
;