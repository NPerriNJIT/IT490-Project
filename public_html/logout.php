<?php
//Logs the user out if they were logged in, always redirects back to login
if(is_logged_in()) {
    reset_session();
    flash("Successfully logged out", "success");
} else {
    flash("You never logged in...", "warning");
}
die(header("Location: loginForm.php"));