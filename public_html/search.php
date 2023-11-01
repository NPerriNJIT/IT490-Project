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

        <div>
            <label for="input">Enter Drink</label>
            <input type="text" id="drinkNameInput" name="drinkNameInput"><br>
            <input type="button" id="searchButton" value="Search"><br>
        </div>

        <div id="drinkResults">
            
        </div>

        <footer>
            <p>&copy; 2023 AlcoholSocialPage IT490-Pros</p>
        </footer>

        <script>
            /*

            javascript draft


            const drinkSearchResults = search_drinks(drinkNameInput.value)

            if(drinkSearchResults.length > 0){
                drinkSearchResults.forEach(drink) {
                    //run display_drink_info function
                    //display on html
                }
            }
            else(drinkResults.textContent="No matching drinks")

            */
        </script>
    </body>
</html>

<?php 
    if(isset($_POST['drinkNameInput'])){
        $drinkInput = $_POST['drinkNameInput'];

        $drinkResults = search_drinks($drinkInput);

        foreach($drinkResults as $drink){
            echo(display_drink_info(get_drink_info($drink['drink_name'])));
        }
    }
    else{
        flash("Enter Drink");
    }
?>

<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>