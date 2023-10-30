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

// Fetch all drink IDs from the `Drinks` table
$drink_ids = [];
$result = $conn->query("SELECT drink_id FROM Drinks");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $drink_ids[] = $row["drink_id"];
    }
}

// Fetch all ingredient IDs from the `Ingredients` table
$ingredient_ids = [];
$result = $conn->query("SELECT ingredient_id, ingredient_name FROM Ingredients");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ingredient_ids[$row["ingredient_name"]] = $row["ingredient_id"];
    }
}

// Loop through each drink and associate ingredients with drink IDs
foreach ($drink_ids as $drink_id) {
    // Simulate a random number of ingredients (between 2 and 8) for each drink
    $num_ingredients = rand(2, 8);

    // Select random ingredients from the ingredient list
    $selected_ingredients = array_rand($ingredient_ids, $num_ingredients);

    // Insert rows into `Drink_Ingredients` table
    foreach ($selected_ingredients as $ingredient) {
        $ingredient_id = $ingredient_ids[$ingredient];
        $stmt = $conn->prepare("INSERT INTO Drink_Ingredients (drink_id, ingredient_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $drink_id, $ingredient_id);
        $stmt->execute();
        $stmt->close();
    }
    echo "Associated ingredients for drink ID: " . $drink_id . "<br>";
}

// Close the database connection
$conn->close();

?>
