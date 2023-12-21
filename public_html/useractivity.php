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
    <title>User Activity Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <h1>User Activity Page</h1>
    <h6>This page displays the 10 latest user interactions on our website!</h6>

    <ul>
        <?php
        $userActivity = get_user_activity();

        foreach ($userActivity as $activity) {
            echo '<li>';

            if (!is_null($activity['blog_title'])) {
                echo '<a href="profile.php?user_id=' . $activity['user_id'] . '">User ID ' . get_username($activity['user_id']) . '</a> just made a blog post about "' . $activity['blog_title'] . '"!<br>';
            } elseif (!is_null($activity['drink_id']) && !is_null($activity['rating']) && !is_null($activity['comment'])) {
                echo '<a href="profile.php?user_id=' . $activity['user_id'] . '">User ID ' . get_username($activity['user_id']) . '</a> just left a ' . $activity['rating'] . ' star rating on drink ID ' . $activity['drink_id'] . ', saying "' . $activity['comment'] . '"!<br>';
            } elseif (!is_null($activity['drink_id'])) {
                echo '<a href="profile.php?user_id=' . $activity['user_id'] . '">User ID ' . get_username($activity['user_id']) . '</a> just favorited drink ID ' . $activity['drink_id'] . '!<br>';
            } elseif (!is_null($activity['drink_name'])) {
                echo '<a href="user_profile.php?user_id=' . $activity['user_id'] . '">User ID ' . get_username($activity['user_id']) . '</a> created their own drink: the <a href="drink.php?drink_id=' . $activity['drink_id'] . '">' . $activity['drink_name'] . '</a>! Go check it out!<br>';
            } else {
                echo 'Unknown Activity<br>';
            }

            echo '</li>';
        }
        ?>
    </ul>
</body>
</html>