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
		echo("running login function"); //Debugging output
		$client = new rabbitMQClient(__DIR__ . "/testRabbitMQ2.ini","testServer");
		$response = array();
		$response['type'] = "login_response";
		$response['login_status'] = doLogin($request['username'],$request['password']);
		$client->publish($response);
		echo "Sent response: ".PHP_EOL . var_dump($response);
	case "registration":
		echo("running registration function");
		$client = new rabbitMQClient(__DIR__ . "/testRabbitMQ2.ini","testServer");
		$response = array();
		$response['type'] = "registration_response";
		$response['registration_status'] = doRegistration($request['username'],$request['password']);
		$client->publish($response);
	case "validate_session":
		//TODO: add validate session
		$client = new rabbitMQClient(__DIR__ . "/testRabbitMQ2.ini","testServer");
		$response = array();
		$response['type'] = "session_response";
		$response['session_status'] = doValidate($request['session_id']);
		$client->publish($response);
	}
	return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer(__DIR__ . "/testRabbitMQ.ini","testServer");
echo("Now listening for client messages...");
$server->process_requests('requestProcessor');
//TODO: Make listener be always running until closed by an administrator
exit();
?>

