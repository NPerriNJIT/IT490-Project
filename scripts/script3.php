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

// Fetch the API data for each drink and associate ingredients with drink IDs
$api_url = "https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=";
foreach ($drink_ids as $drink_id) {
    $response = file_get_contents($api_url . $drink_id);
    $data = json_decode($response, true);

    if (isset($data['drinks'])) {
        $drink = $data['drinks'][0];
        foreach ($ingredient_ids as $ingredient_name => $ingredient_id) {
            if (!empty($drink[$ingredient_name])) {
                // Insert a row into `Drink_Ingredients`
                $stmt = $conn->prepare("INSERT INTO Drink_Ingredients (drink_id, ingredient_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $drink_id, $ingredient_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        echo "Associated ingredients for drink ID: " . $drink_id . "<br>";
    } else {
        echo "Unable to fetch data for drink ID: " . $drink_id . "<br>";
    }
}

// Close the database connection
$conn->close();

?>
