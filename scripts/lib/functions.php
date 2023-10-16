<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

function is_logged_in($redirect = false, $destination = "login.php")
{
	$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");
	$response = array();
	$response['type'] = "validate_session";
	$response['sessionId'] = session_id();
	$client->publish($response);
	//send session ID to see if logged in

	$server = new rabbitMQServer("../testRabbitMQ2.ini","testServer");
	$request = $server->process_requests();
	$var_dump($request);
	if(isset($request['sessionStatus']) && $request['sessionStatus'] == true) {
		$isLoggedIn = true;
	} else {
		$isLoggedIn = false;
	}
	//receives whether or not 
	if ($redirect && !$isLoggedIn) {
		flash("You must be logged in to view this page", "warning");
		die(header("Location: $destination"));
	}
	return $isLoggedIn;
}


>
