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

// Define a function to call the API and populate the database
function populateDatabase($drink_id, $conn) {
    $api_url = "https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=" . $drink_id;
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    if ($data['drinks'] != null) {
        $drink = $data['drinks'][0];

        // Prepare SQL statement to insert data
        $stmt = $conn->prepare("INSERT INTO Drinks (drink_id, drink_name, drink_tags, alcoholic, ingredients, measurements, instructions, avgrating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssd", $drink_id, $drink['strDrink'], $drink['strTags'], ($drink['strAlcoholic'] == 'Alcoholic' ? 1 : 0), json_encode(array_filter($drink, function ($key) { return strpos($key, 'strIngredient') === 0; }, ARRAY_FILTER_USE_KEY)), json_encode(array_filter($drink, function ($key) { return strpos($key, 'strMeasure') === 0; }, ARRAY_FILTER_USE_KEY)), $drink['strInstructions'], null);

        // Execute the prepared statement
        if ($stmt->execute()) {
            echo "Inserted drink with ID: " . $drink_id . "<br>";
        } else {
            echo "Error inserting drink with ID: " . $drink_id . "<br>";
        }

        // Close the statement
        $stmt->close();
    }
}

// Start populating the database from a specified drink_id
$drink_id = 1;
while (true) {
    populateDatabase($drink_id, $conn);
    $drink_id++;
}

// Close the database connection
$conn->close();

?>
