<?php
//Logs the user out if they were logged in, always redirects back to login
require_once(__DIR__ . '/../scripts/lib/functions.php');
reset_session();
die(header("Location: loginForm.php"));