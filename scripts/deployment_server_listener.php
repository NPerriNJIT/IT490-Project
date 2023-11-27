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
    $response = array();
    switch($request['type']) {
        case "check_update":
            //TODO: Add check_update function
            //What should be set in the request: branch, cluster, current version
            $response = check_update($request);
        case "set_version_status":
            //What should be set in the request: version, status
            $response = set_version_status();
        case "rollback":
            //TODO: add rollback function
            //What should be set: machine
            $response = rollback();
        case "add_version":
            //TODO: Add add_version function
            //What should be set in the request: version, branch, status, 
    }
    return $response;
}
$server = new rabbitMQServer(__DIR__ . "/testRabbitMQ.ini","testServer");
echo("Now listening for client messages...");
$server->process_requests('requestProcessor');
echo("Debug line". PHP_EOL);
//TODO: Make listener be always running until closed by an administrator
exit();
?>