<html>
    <head>
	<meta charset="UTF-8">

    </head>

    <body>
	<form method="POST" onsubmit="loginForm.php">
	    <label for="username"> Username: </label><br>
	    <input type="text" id="username" name="username"><br>

	    <label for="password"> Password: </label><br>
	    <input type="text" id="password" name="password"><br>>

	    <button type="submit"> Login </button>

	</form>

    </body>
</html>

<?php
require_once('scripts/path.inc');
require_once('scripts/get_host_info.inc');
require_once('scripts/rabbitMQLib.inc');
$client = new rabbitMQClient("lib/testRabbitMQ.ini", "testServer");


$request = array();
$request['type'] = "login";
$request['username'] = $_POST['username'];
$request['password'] = $_POST['password'];
$request['message'] = "test Message";

print_r($request);

$response = $client->send_request($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

?>


