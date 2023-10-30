#!/usr/bin/php
<?php
require_once(__DIR__ . "/db.php");
require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');
function doLogin($username, $password, $session_id)
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
				$hash = $user["password"];
				unset($user["password"]);
				if (password_verify($password, $hash)) {
					echo($user . " logged in successfully");
					create_session($session_id, $user['id']);
					return "success";
					//TODO: Create a session client-side with ID matching the session here
					//TODO: Create a session here with username, other useful information
					//we will have to pass things linked to users here, such as a team ID if we are sticking with fantasy football
				} else {
					echo($user . " failed login attempt");
					return "denied";
				}
			} else {
				echo "3";
				echo($user . "does not exist");
				return "denied but username";
				//IMPORTANT: Don't display to the client whether username or password was incorrect, has to be the same message.
				//We can log that here though.
			}
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}
function doRegistration($username, $password)
{
	//Sanitization should be done on front-end
	
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Users (password, username) VALUES(:password, :username)");
	try {
		$stmt->execute([":password" => $password, ":username" => $username]);
	echo $username . " registered successfully";
	return "success";
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "failure";

}
function create_session($session_id, $user_id) {
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO  Sessions (session_id, user_id) VALUES (:session_id, :user_id)");
	try {
		$stmt->execute([":session_id" => $session_id, ":user_id" => $user_id]);
		echo "Started session with session_id " . $session_id . " for user id " . $user_id . PHP_EOL;
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}

function doValidate($sessionID)
{
	$db = getDB();
	$stmt = $db->prepare("Select * from Sessions where session_id = :sessionID");
	try {
		$stmt->execute([":sessionID" => $sessionID]);
		if($stmt->rowCount() > 0) {
			echo "Valid session";
			return "valid";
		} else {
			echo "Invalid session requested";
			return "invalid";
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
		echo "Something crazy happened";
		return false;
	}
}

function getSessionUsername($sessionID) {
	$db = getDB();
	$stmt = $db->prepare("Select user_id from Sessions where session_id = :sessionID");
	try {
		$r = $stmt->execute([":sessionID" => $sessionID]);
		if($r) {
			$session = $stmt->fetch(PDO::FETCH_ASSOC);
			$user_id = $session['user_id'];
			$stmt = $db->prepare("Select username from Users where id = :userID");
			try{
				$r = $stmt->execute([":userID" => $user_id]);
				if($r) {
					$user = $stmt->fetch(PDO::FETCH_ASSOC);
					$username = $user['username'];
					return $username;
				}
			} catch (Exception $e) {
				echo "Error: " . $e->getMessage();
			}
		} else {

		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "error";
}

function delete_session($session_id) {
	$db = getDB();
	$stmt = $db->prepare("Delete from Sessions where session_id = :session_id");
	try {
		$stmt->execute([":session_id" => $session_id]);
		echo "Deleted session " . $session_id . PHP_EOL;
		return "deleted";
	} catch (Exception $e) {
		echo "Error deleting session " . $session_id . ": " . $e . PHP_EOL; 
		return "did not exist";
	}
}

function get_session_user_id($session_id) {
	$db = getDB();
	$stmt = $db->prepare("Select user_id from Sessions where session_id = :sessionID");
	try {
		$r = $stmt->execute([":sessionID" => $session_id]);
		if($r) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			return $result['user_id'];
		} else {
			return "session doesn't exist";
		}
			
	} catch (Exception $e) {
		echo "Error: " . $e;
		return "error";
	}
}

//Gets drink info
function get_drink($drink_id) {
	$db = getDB();
	$stmt = $db->prepare("Select drink_name, drink_tags, alcoholic, ingredients, measurements, instructions from Drinks where drink_id = :drink_id");
	try{
		$r = $stmt->execute([":drink_id" => $drink_id]);
		if($r) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$result['get_drink_info_status'] = "valid";
			return $result;

		} else {
			$result = array();
			$result['get_drink_info_status'] = "invalid";
			return $result;
		}
	} catch (Exception $e) {
		echo "Error: " . $e;
		$result = array();
		$result['get_drink_info_status'] = "invalid";
		return $result;
	}
}

//Processes blog post
function send_blog_post($session_id, $blog_post) {
	$user_id = get_session_user_id($session_id);
	if(!is_int($user_id)) {
		return "user id error";
	}
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Blogs (user_id, blog_post) VALUES(:user_id, :blog_post)");
	try {
		$stmt->execute([":user_id" => $user_id, ":blog_post" => $blog_post]);
		echo  "Blog posted successfully";
		return "valid";
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "failure";
}


?>