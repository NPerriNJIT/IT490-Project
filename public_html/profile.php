<?php
//THIS PAGE ISN'T USABLE YET
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: loginForm.php"));
}
//TODO: sanitize id if necessary
$user_id = $_GET['id'];
error_log("user id $user_id");
if ($user_id < 1 || !check_user_exists($user_id)) {
    flash("Invalid user", "danger");
    die(header("Location: profile?id=$user_id"));
}

//TODO: Add functions to get profile info for user
//TODO: Add profile changes if user is on their profile, not required for midterm assignments ;)
if($user_id == get_session_user_id()) {
    $is_user = true;
    $profile = get_profile_me($user_id);
} else {
    $is_user = false;
    $profile = get_profile_not_me($user_id);
}
?>
<!- ADD HTML HERE ->

<html>
    <head>
    <meta charset="UTF-8">
    </head>

    <body>
        <p>
            <?php echo(get_session_username()) ?>
        </p>
    </body>
</html>

<?php
    //Add other profile info above (username, etc)
    $favorite_drinks = get_favorite_drinks($user_id);
    foreach($favorite_drinks as $drink) : ?>
        <?php echo(display_drink_info(get_drink_info($drink))); ?>
    <?php endforeach; ?>


<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>    