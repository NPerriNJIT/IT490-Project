#!/usr/bin/php
<?php
require_once("db.php");
function doLogin($username, $password)
{
	$db = getDB();
	$stmt = $db->prepare("SELECT id, username, password from Users where username = :username");
	try {
		$r = $stmt->execute([":username" => $username]);
		if ($r) {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($user) {
				$hash = $user["password"];
				unset($user["password"]);
				if (password_verify($password, $hash)) {
					echo($user + " logged in successfully");
					return true;
					//TODO: Create a session client-side with ID matching the session here
					//TODO: Create a session here with username, other useful information
					//we will have to pass things linked to users here, such as a team ID if we are sticking with fantasy football
				} else {
					echo($user + " failed login attempt");
					return false;
				}
			} else {
				return false;
			}
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
			echo "Invalid session requested"
			return null;
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}

?>
