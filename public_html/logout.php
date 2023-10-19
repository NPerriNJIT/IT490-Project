<?php
if(is_logged_in()) {
    reset_session();
    flash("Successfully logged out", "success");
} else {
    flash("You never logged in...", "warning");
}
die(header("Location: loginForm.php"));