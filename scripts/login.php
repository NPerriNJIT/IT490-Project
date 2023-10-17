<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if both username and password are set
        if (isset($_POST['username']) && isset($_POST['password'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];

                require_once('../scripts/path.inc');
                require_once('../scripts/get_host_info.inc');
                require_once('../scripts/rabbitMQLib.inc');
                $client = new rabbitMQClient("../scripts/testRabbitMQ.ini", "testServer");

                $request = array();
                $request['type'] = "login";
                $request['username'] = $_POST['username'];
                $request['password'] = $_POST['password'];
                print_r($request);

                $response = $client->send_request($request);

                echo "client received response: ".PHP_EOL;
                print_r($response);
                echo "\n\n";

        } else {
                echo "Both username and password are required.";
        }
} else {
        echo "Invalid request method.";
}
?>