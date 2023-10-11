<?php 
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");


$request = array();
$request['type'] = "login";
$request['username'] = $_POST['username'];
$request['password'] = $_POST['password'];

$response = $client->send_request($request);

?>

<!DOCTYPE html>
<html>
    <head>
	<meta charset="UTF-8">

    </head>

    <body>
	<form method="POST" onsubmit="sendFunction()">
	    <label for="username"> Username: </label><br>
	    <input type="text" id="username" name="username"><br>

	    <label for="password"> Password: </label><br>
	    <input type="text" id="password" name="password"><br>>

	    <button type="submit"> Login </button>

	</form>

    </body>

</html>
