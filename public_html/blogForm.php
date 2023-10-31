<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
error_reporting(E_ALL);
if(!is_logged_in()) {
    die(header("Location: loginForm.php"));
}
?>

<html>
    <head>
        <meta charset="UTF-8">

    </head>
    <body>
        <form method="POST" action="">
            <label for="blogTitle">Title:</label><br>
            <input type="text" id="blogTitle" name="blogTitle"><br><br>

            <label for="blogContent">Content:</label><br>
            <textarea id="blogContent" name="blogContent" rows="4" cols="50"></textarea><br><br>

            <button type="submit">Submit</button>
        </form>
    </body>
</html>
<?php 
if(isset($_POST['blogTitle']) && isset($_POST['blogContent'])) {
    flash("test", "success");
    print_r(isset($_POST['blogTitle']) && isset($_POST['blogContent']));
    echo("Posting");
    //send_blog_post($_POST['blogTitle'], $_POST['blogContent']);
}
?>
<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>