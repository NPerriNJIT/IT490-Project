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
        echo(display_drink_info($drink));
    }
?>

<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>