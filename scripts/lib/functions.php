<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

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
	$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");
	$response = array();
	$response['type'] = "validate_session";
	$response['sessionId'] = session_id();
	$client->publish($response);
	//send session ID to see if logged in

	$server = new rabbitMQServer("../testRabbitMQ2.ini","testServer");
	$request = $server->process_requests();

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

function get_session_data(array $data) {
	if(is_logged_in()) {
		$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");
		$message = array();
		$message['type'] = "validate_session";
		$message['session_id'] = session_id();
		$message['data'] = $data;
		$client->publish($message);
		//Sends session validation request with requested session data if the user is logged in
		
		$server = new rabbitMQServer("../testRabbitMQ2.ini","testServer");
		$request = $server->process_requests();
		//Waits for a response from the server

		if(isset($request['type']) && $request['type'] == "session_valid") {
			//return data
		} else {
			flash("ERROR REQUEST TYPE WASN'T SET ON THE RETURNING MESSAGE");
		}
	}
}

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
function getMessages()
{
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

