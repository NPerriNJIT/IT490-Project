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

        // Extract ingredients and measurements
        $ingredients = [];
        $measurements = [];
        for ($i = 1; $i <= 15; $i++) {
            $ingredient = $drink["strIngredient$i"];
            $measurement = $drink["strMeasure$i"];
            if ($ingredient && $measurement) {
                $ingredients[] = $ingredient;
                $measurements[] = $measurement;
            }
        }

        // Combine ingredients and measurements into comma-separated text
        $ingredients_param = implode(", ", $ingredients);
        $measurements_param = implode(", ", $measurements);
        $alcoholic_param = ($drink['strAlcoholic'] == 'Alcoholic') ? 1 : 0;
        $avgrating_param = 0;

        // Prepare SQL statement to insert data
        $stmt = $conn->prepare("INSERT INTO Drinks (drink_id, drink_name, drink_tags, alcoholic, ingredients, measurements, instructions, avgrating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if the prepare statement was successful
        if ($stmt === false) {
            echo "Error preparing statement: " . $conn->error . "<br>";
            return;
        }

        // Bind the parameters
        $stmt->bind_param("issssssd", $drink_id, $drink['strDrink'], $drink['strTags'], $alcoholic_param, $ingredients_param, $measurements_param, $drink['strInstructions'], $avgrating_param);

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

$drink_id = 11000;
$waitTime = 10; // Wait time in seconds

while (true) {
    if ($drink_id !== null) {
        $response = file_get_contents("https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=" . $drink_id);

        if ($response === false) {
            $http_response_header_string = implode(",", $http_response_header);

            // Check if the response contains a "429 Too Many Requests" error
            if (strpos($http_response_header_string, "429 Too Many Requests") !== false) {
                echo "Rate limit reached. Waiting for $waitTime seconds...\n";
                sleep($waitTime);
                continue; // Retry after waiting
            }
        }

        // Parse the response
        $data = json_decode($response, true);

        if ($data['drinks'] != null) {
            if (!isDuplicateDrink($drink_id, $conn)) {
                populateDatabase($drink_id, $conn);
            } else {
                echo "Drink with ID $drink_id already exists. Skipping.\n";
            }
        }
    }
    $drink_id++;
}


function isDuplicateDrink($drink_id, $conn) {
    $query = "SELECT COUNT(*) FROM Drinks WHERE drink_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $drink_id);
    $stmt->execute();
    $stmt->bind_result($count);
    
    $stmt->fetch();
    $stmt->close();
    
    return $count > 0;
}


// Close the database connection
$conn->close();

?>
