<?php
//Logs the user out if they were logged in, always redirects back to login
require_once(__DIR__ . '/../scripts/lib/functions.php');
if(is_logged_in()) {
    reset_session();
    flash("Successfully logged out", "success");
} else {
    flash("You never logged in...", "warning");
}
die(header("Location: loginForm.php"));