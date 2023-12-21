<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
error_reporting(E_ALL);
if(!is_logged_in()) {
    die(header("Location: loginForm.php"));
}
//need more code to edit display on webpage
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Activity</title>
</head>
<body>

<table border="1">
    <tr>
        <th>User ID</th>
        <th>Created</th>
        <th>Action</th>
        <th>Details</th>
    </tr>
    <?php
    $userActivity = get_user_activity();

    foreach ($userActivity as $activity) {
        echo '<tr>';
        echo '<td>' . $activity['user_id'] . '</td>';
        echo '<td>' . $activity['created'] . '</td>';

        if (!is_null($activity['blog_title'])) {
            echo '<td>Blog Post</td>';
            echo '<td>Title: ' . $activity['blog_title'] . '</td>';
        } elseif (!is_null($activity['drink_id']) && !is_null($activity['rating']) && !is_null($activity['comment'])) {
            echo '<td>Rating</td>';
            echo '<td>Drink ID: ' . $activity['drink_id'] . '<br>';
            echo 'Rating: ' . $activity['rating'] . '<br>';
            echo 'Comment: ' . $activity['comment'] . '</td>';
        } elseif (!is_null($activity['drink_id'])) {
            echo '<td>Favorite</td>';
            echo '<td>Drink ID: ' . $activity['drink_id'] . '</td>';
        } elseif (!is_null($activity['drink_name'])) {
            echo '<td>User Drink</td>';
            echo '<td>Drink Name: ' . $activity['drink_name'] . '</td>';
        } else {
            echo '<td>Unknown Activity</td>';
            echo '<td></td>';
        }

        echo '</tr>';
    }
    ?>
</table>

</body>
</html>