<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
$user_id = $_GET['id'];
error_log("user id $user_id");
if ($user_id < 1 || check_user_id_exists()) {
    flash("Invalid user", "danger");
    die(header("Location: sessionTestPage.php"));
}
//TODO: Add get_user_id() to functions
//TODO: Add check_user_id_exists() to functions
//TODO: Add functions to get profile info for user
//TODO: Add profile changes if user is on their profile, not required for midterm assignments ;)
if($user_id == get_user_id()) {
    $is_user = true;
} else {
    $is_user = false;
}
?>
<!- ADD HTML HERE ->
<?php
    //TODO: Add link to drink from drink names
    $favorite_drinks = get_favorite_drinks($user_id);
    foreach($favorite_drinks as $drink) : ?>
        <tr>
            <td><?php $drink['drink_name'] ?></td>
        </tr>
    <?php endforeach; ?>