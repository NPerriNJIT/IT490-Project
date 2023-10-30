<?php

// Define the database connection
$servername = "localhost";
$username = "jheans";
$password = "12345";
$dbname = "testdb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Parse the API data
$api_url = "https://www.thecocktaildb.com/api/json/v1/1/list.php?i=list";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

// Check if the API request was successful
if (isset($data['drinks'])) {
    foreach ($data['drinks'] as $ingredient) {
        $ingredient_name = $ingredient['strIngredient1'];
        
        // Check if the ingredient is already in the database
        $query = "SELECT COUNT(*) FROM Ingredients WHERE ingredient_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $ingredient_name);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // If the ingredient is not already in the database, insert it
        if ($count == 0) {
            $query = "INSERT INTO Ingredients (ingredient_name) VALUES (?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $ingredient_name);
            $stmt->execute();
            $stmt->close();
            echo "Inserted ingredient: " . $ingredient_name . "<br>";
        } else {
            echo "Ingredient already exists: " . $ingredient_name . "<br>";
        }
    }
} else {
    echo "API request failed. Unable to fetch ingredients.";
}

// Close the database connection
$conn->close();

?>
