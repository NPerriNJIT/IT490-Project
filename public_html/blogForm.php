<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
?>

<html>
    <head>
        <meta charset="UTF-8">

    </head>
    <body>
        <form onsubmit="return send_blog_post(document.getElementById('blogTitle').value, document.getElementById('blogPost').value)" method="POST">
            <label for="blogTitle">Title:</label><br>
            <input type="text" id="blogTitle" name="blogTitle"><br><br>

            <label for="blogContent">Content:</label><br>
            <textarea id="blogContent" rows="4" cols="50"></textarea><br><br>

            <button type="submit">
        </form>
    </body>
</html>
<?php 
if(isset($_POST['blogTitle']) && isset($_POST['blogContent'])) {
    send_blog_post($_POST['blogTitle'], $_POST['blogContent']);
    die(header("Location: blogForm.php"));
}
?>
<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>