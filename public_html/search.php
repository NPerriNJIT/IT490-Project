<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: loginForm.php"));
}

$redirect_id = get_session_user_id();
error_log($redirect_id);
if(isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    die(header("Location: profile.php?id=" . $redirect_id));
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
            <input type="text" id="drinkNameInput" name="drinkName"><br>
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

        $drinkResults = search_drinks($drinkResults);

        foreach($drinkResults as $drink){
            echo(display_drink_info(get_drink_info($drink['drink_name'])));
        }
    }
?>

<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>