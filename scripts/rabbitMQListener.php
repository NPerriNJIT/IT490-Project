#!/usr/bin/php
<?php
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
require_once(__DIR__ . '/lib/functions.php');

function requestProcessor($request)
{
	echo "received request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type']))
	{
		return "ERROR: unsupported message type";
	}
	switch ($request['type'])
	{
	case "login":
		echo("running login function" . PHP_EOL); //Debugging output
		
		$response = array();
		$response['type'] = "login_response";
		$response['login_status'] = doLogin($request['username'],$request['password']);
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	case "registration":
		echo("running registration function");
		$response = array();
		$response['type'] = "registration_response";
		$response['registration_status'] = doRegistration($request['username'],$request['password']);
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	case "get_session_username":
		//TODO: add validate session
		$response = array();
		$response['type'] = "session_response";
		if(doValidate($request['session_id'])) {
			$response['username'] = getSessionUsername($request['session_id']);
			$response['session_status'] = "valid";
		} else {
			$response['session_status'] = "invalid";
		}
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	case "delete_session_data":
		echo "running delete session" . PHP_EOL;
		$response = array();
		$response['delete_session_status'] = delete_session($request['session_id']);
		echo "Sending response: " . PHP_EOL . var_dump($response);
		return $response;
	case "validate_session":
		echo "running session validation" . PHP_EOL;
		$response = array();
		$response['session_status'] = doValidate($request['session_id']);
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	}
	return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer(__DIR__ . "/testRabbitMQ.ini","testServer");
echo("Now listening for client messages...");
$server->process_requests('requestProcessor');
echo("Debug line". PHP_EOL);
//TODO: Make listener be always running until closed by an administrator
exit();
?>

