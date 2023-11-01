<?php
//THIS PAGE ISN'T USABLE YET
require_once(__DIR__ . "/../scripts/partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: loginForm.php"));
}
//TODO: sanitize id if necessary
$redirect_id = get_session_user_id();
error_log($redirect_id);
if(isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    die(header("Location: profile.php?id=" . $redirect_id));
}
error_log("user id $user_id");
error_log(print_r($user_id < 1));
error_log(print_r(check_user_exists($user_id)));
if ($user_id < 1 || !check_user_exists($user_id)) {
    flash("Invalid user", "danger");
    die(header("Location: profile.php?id=" . $redirect_id));
}

//TODO: Add functions to get profile info for user
//TODO: Add profile changes if user is on their profile, not required for midterm assignments ;)
$is_user = false;
if($user_id == get_session_user_id()) {
    $is_user = true;
}
?>
<!- ADD HTML HERE ->

<html>
    <head>
    <meta charset="UTF-8">
    </head>

    <body>
        <p> Username: <?php echo(get_username($user_id)) ?></p>
    </body>
</html>

<?php
    //Add other profile info above (username, etc)
    echo("<p> Favorites:</p>");
    $favorite_drinks = get_favorite_drinks($user_id);
    foreach($favorite_drinks as $drink) : ?>
        <?php echo(display_drink_info(get_drink_info($drink['drink_id']))); ?>
    <?php endforeach; ?>
    <?php 
    echo("<br><p>Personal drinks:</p>");
    $personal_drinks = get_user_drinks($user_id, $is_user);
    foreach($personal_drinks as $drink) : ?>
        <?php echo(display_drink_info($drink)); ?>
    <?php endforeach; ?>
    <?php 
    if($is_user) {
        echo("<br><p>Recommended drinks:</p>");
        $recommendations = get_recommendations();
        foreach($recommendations as $drink) {
            echo(display_drink_info($drink));

        }
    }
?>
<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>    