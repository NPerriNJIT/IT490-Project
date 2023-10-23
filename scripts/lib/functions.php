<?php
require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');

//se() makes sure that variables that will be outputted via html
//are properly outputted
function se($v, $k = null, $default = "", $isEcho = true)
{
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}

function is_logged_in($redirect = false, $destination = "loginForm.php")
{
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "validate_session";
	$request['session_id'] = session_id();
	//send session ID to see if logged in

	$response = $client->send_request($request);


	if(isset($response['session_status']) && $response['session_status'] === "valid") {
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

function get_session_username() {
	if(is_logged_in()) {
		$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
		$request = array();
		$request['type'] = "get_session_username";
		$request['session_id'] = session_id();
		$response = $client->send_request($request);
		//Waits for a response from the server

		if(isset($response['type']) && $response['type'] == "session_response") {
			if(isset($response['session_status']) && $response['session_status'] == "valid") {
				return $response['username'];
			}
		} else {
			flash("ERROR IN FUNCTIONS SEE LINE 77", "danger");
		}
	}
}
//Used for outputting messages onto the webpage with different colors for different types of messages
//"success" = green, "warning" = yellow, "danger" = red
function flash($msg = "", $color = "info")
{
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}
//Helper function for flash()
function getMessages()
{
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}
//Used for hard resetting sessions, will delete it from database if it exists
function reset_session()
{
	if(session_status() == PHP_SESSION_ACTIVE) {
		delete_session_data(session_id());

    	session_unset();
    	session_destroy();
	}
    session_start();
}
//On a successful login, send_session() will send session information to the server
function send_session($session_id, $username)
{
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$message = array();
	$message['type'] = "start_session";
	$message['session_id'] = $session_id;
	$message['username'] = $username;
	$client->publish($message);
	is_logged_in();
}
//Sends message to delete this session from database if it exists
function delete_session_data($session_id) {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
	$message = array();
	$message['type'] = "delete_session_data";
	$message['session_id'] = $session_id;
	$client->publish($message);
}
//Gets the url of a page, used for nav
function get_url($dest)
{
    global $BASE_PATH;
    if (str_starts_with($dest, "/")) {
        //handle absolute path
        return $dest;
    }
    //handle relative path
    return $BASE_PATH . $dest;
}
