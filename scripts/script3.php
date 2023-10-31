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

// Loop through each drink and fetch the number of ingredients from the API
foreach ($drink_ids as $drink_id) {
    // Check if the drink ID already exists in Drink_Ingredients
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM Drink_Ingredients WHERE drink_id = ?");
    $check_stmt->bind_param("i", $drink_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();
    
    // If the drink ID already exists, skip to the next one
    if ($count > 0) {
        echo "Drink ID " . $drink_id . " already exists in Drink_Ingredients | ";
        continue;
    }

    $api_url = "https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=" . $drink_id;
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    if ($data['drinks'] != null) {
        $drink = $data['drinks'][0];
        $ingredients = [];

        // Collect non-null ingredients
        for ($i = 1; $i <= 15; $i++) {
            $ingredient = $drink["strIngredient$i"];
            if ($ingredient) {
                $ingredients[] = $ingredient;
            }
        }

        // Loop through ingredients and associate them with their unique IDs
        foreach ($ingredients as $ingredient_name) {
            $stmt = $conn->prepare("SELECT ingredient_id FROM Ingredients WHERE ingredient_name = ?");
            $stmt->bind_param("s", $ingredient_name);
            $stmt->execute();
            $stmt->bind_result($ingredient_id);
            
            if ($stmt->fetch()) {
                // Insert rows into `Drink_Ingredients` table
                $stmt->close(); // Close the previous statement
                $stmt2 = $conn->prepare("INSERT INTO Drink_Ingredients (drink_id, ingredient_id) VALUES (?, ?)");
                $stmt2->bind_param("ii", $drink_id, $ingredient_id);
                $stmt2->execute();
                $stmt2->close();
            }
            else {
                $stmt->close();
            }
        }

        echo "Associated ingredients for drink ID: " . $drink_id . " | ";
    }
}

// Close the database connection
$conn->close();

?>
