<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
error_reporting(E_ALL);
if(!is_logged_in()) {
    die(header("Location: loginForm.php"));
}

get_user_activity();
?>