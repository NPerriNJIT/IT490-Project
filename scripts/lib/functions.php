#!/usr/bin/php
<?php
require_once("db.php");
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
function doLogin($username, $password)
{
	echo "1";
	$db = getDB();
	$stmt = $db->prepare("SELECT id, username, password from Users where username = :username");
	try {
		$r = $stmt->execute([":username" => $username]);
		if ($r) {
			echo "2";
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($user) {
				$client = new rabbitMQClient("testRabbitMQ2.ini", "testServer");
				$login_response = array();
				$login_response['type'] = "login_response";
				$hash = $user["password"];
				unset($user["password"]);
				if (password_verify($password, $hash)) {
					echo($user + " logged in successfully");
					$login_response['status'] = "success";
					//TODO: Create a session client-side with ID matching the session here
					//TODO: Create a session here with username, other useful information
					//we will have to pass things linked to users here, such as a team ID if we are sticking with fantasy football
				} else {
					echo($user + " failed login attempt");
					$login_response['status'] = "denied";
				}
			} else {
				echo "3";
				$login_response['status'] = "denied but username";
				//IMPORTANT: Don't display to the client whether username or password was incorrect, has to be the same message.
				//We can log that here though.
			}
			echo($login_response['status']);
			$client->publish($login_response);
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}
function doRegistration($username, $password)
{
	//Sanitization should be done on front-end
	$hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (password, username) VALUES(:password, :username)");
        try {
            $stmt->execute([":password" => $hash, ":username" => $username]);
	    echo $username . " registered successfully";
	    return $username . " registered successfully";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
	}
	return "Failed to register, maybe the username is already used?";

}
function doValidate($sessionID)
{
	$db = getDB();
	$stmt = $db->prepare("Select data from Sessions where id = :sessionID");
	try {
		$r = $stmt->execute([":sessionID" => $sessionID]);
		if($r) {
			$session = $stmt->fetch(PDO::FETCH_ASSOC);
			return $session['data'];
		} else {
			echo "Invalid session requested";
			return null;
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}

?>
