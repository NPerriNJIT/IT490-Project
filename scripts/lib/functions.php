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
			    return true;
			    //we will have to pass things linked to users here, such as a team ID if we are sticking with fantasy football
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            //TODO: Add error logging
        }
}

?>
