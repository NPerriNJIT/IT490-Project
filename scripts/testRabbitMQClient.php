#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function requestProcessor($request){
	var_dump($request);
	return true;
}


$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "login";
$request['username'] = "steve";
$request['password'] = "password";
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";
$server = new rabbitMQServer("testRabbitMQ2.ini","testServer");
echo("Now listening for client messages...");
$server->process_requests('requestProcessor');
//TODO: Make listener be always running until closed by an administrator
exit();

echo $argv[0]." END".PHP_EOL;

