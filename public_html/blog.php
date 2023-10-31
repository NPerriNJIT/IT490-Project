<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
error_reporting(E_ALL);
if(!is_logged_in()) {
    die(header("Location: loginForm.php"));
}
$blog_posts = get_blog_posts_all();
?>
<html>
    <head>
        <meta charset="UTF-8">

    </head>
    <body>
        <?php foreach($blog_posts as $blog_post) {
            echo "<h2>Title: " . $blog_post['title'] . "</h2>";
            echo "<h4>User: " . $blog_post['username'] . "</h4>";
            echo "<p>" . $blog_post['blog_post'] . "</p>";
        }
        ?>
    </body>
</html>
<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>