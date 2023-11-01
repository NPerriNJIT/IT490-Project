<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
error_reporting(E_ALL);
if(!is_logged_in()) {
    die(header("Location: loginForm.php"));
}

?>

<form method="POST" action="submit_form.php"> <!-- Replace with your form action -->
    <label for="drinkName">Drink Name:</label><br>
    <input type="text" id="drinkName" name="drinkName" required><br><br>

    <label for="drinkTags">Drink Tags:</label><br>
    <input type="text" id="drinkTags" name="drinkTags"><br><br>

    <label for="isAlcoholic">Is Public:</label><br>
    <input type="radio" id="isAlcoholicYes" name="isAlcoholic" value="Y">
    <label for="isAlcoholicYes">Yes</label><br>
    <input type="radio" id="isAlcoholicNo" name="isAlcoholic" value="N">
    <label for="isAlcoholicNo">No</label><br>

    <label for="isPublic">Is Public:</label><br>
    <input type="radio" id="isPublicYes" name="isPublic" value="Y">
    <label for="isPublicYes">Yes</label><br>
    <input type="radio" id="isPublicNo" name="isPublic" value="N">
    <label for="isPublicNo">No</label><br>

    <!-- Ingredients -->
    <label for="ingredients">Ingredients:</label><br>
    <textarea id="ingredients" name="ingredients" rows="4" cols="50"></textarea><br><br>

    <!-- Measurements -->
    <label for="measurements">Measurements:</label><br>
    <textarea id="measurements" name="measurements" rows="4" cols="50"></textarea><br><br>

    <!-- Instructions -->
    <label for="instructions">Instructions:</label><br>
    <textarea id="instructions" name="instructions" rows="4" cols="50"></textarea><br><br>

    <input type="submit" value="Submit">
</form>
<?php

if(isset($_POST['drinkName']) && isset($_POST['instructions']) && isset($_POST['measurements']) 
&& isset($_POST['ingredients']) && isset($_POST['isAlcoholic']) && isset($_POST['isPublic'])) {
    add_user_drink($_POST['blogTitle'], $_POST['blogContent']);
    //Add a redirect to show all blogs page
}

?>