<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: loginForm.php"));
}
?>

<html lang = "en">
    <head>
        <meta charset="UTF-8">
        <title>Drink App</title>
    </head>
    <body>
        <header>
            <h1>Drink App</h1>
        </header>

        <div id="drinkResults">
            
        </div>

        <footer>
            <p>&copy; 2023 AlcoholSocialPage IT490-Pros</p>
        </footer>
        <div>
            <h3>Top Drinks:</h3>
        </div>
        
    </body>
</html>


<?php 
    $top_drinks = get_top_drinks();
    foreach($top_drinks as $drink){
        $sum_ratings = 0;
        $reviews = get_drink_reviews($drink['id']);
        foreach($reviews as $review) {
            $sum_ratings+=$review['rating'];
        }
        $avg_rating = $sum_ratings/count($reviews);
        $drink_info = "<li>Drink Name: " . $drink['drink_name'] . "</li>" . PHP_EOL;
        
        $drink_info = $drink_info . '<li>Drink ID: <a href="drink.php?id=' . $drink['drink_id'] . '">' . $drink['drink_id'] . '</a></li>' . PHP_EOL;
        $drink_info = $drink_info . "<li>Drink Category: " . $drink['drink_tags'] . "</li>" . PHP_EOL;
        $drink_info = $drink_info . "<li>Is alcoholic?: " . $drink['alcoholic'] . "</li>" . PHP_EOL;
        $drink_info = $drink_info . "<li>Ingredients: " . $drink['ingredients'] . "</li>" . PHP_EOL;
        $drink_info = $drink_info . "<li>Average rating: " . $avg_rating . "</li>" . PHP_EOL;
        echo($drink_info);

    //    echo(display_drink_info($drink));
    }
?>

<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>