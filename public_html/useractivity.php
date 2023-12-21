<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
error_reporting(E_ALL);
if(!is_logged_in()) {
    die(header("Location: loginForm.php"));
}
//need more code to edit display on webpage
?>

v<!DOCTYPE html>
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

    <ul>
        <?php
        $userActivity = get_user_activity();

        foreach ($userActivity as $activity) {
            echo '<li>';

            if (!is_null($activity['blog_title'])) {
                echo 'User ID ' . $activity['user_id'] . ' just made a blog post about "' . $activity['blog_title'] . '"!<br>';
            } elseif (!is_null($activity['drink_id']) && !is_null($activity['rating']) && !is_null($activity['comment'])) {
                echo 'User ID ' . $activity['user_id'] . ' just left a ' . $activity['rating'] . ' star rating on drink ID ' . $activity['drink_id'] . ', saying "' . $activity['comment'] . '"!<br>';
            } elseif (!is_null($activity['drink_id'])) {
                echo 'User ID ' . $activity['user_id'] . ' just favorited drink ID ' . $activity['drink_id'] . '!<br>';
            } elseif (!is_null($activity['drink_name'])) {
                echo 'User ID ' . $activity['user_id'] . ' created their own drink: the "' . $activity['drink_name'] . '"! Go check it out!<br>';
            } else {
                echo 'Unknown Activity<br>';
            }

            echo '</li>';
        }
        ?>
    </ul>
</body>
</html>