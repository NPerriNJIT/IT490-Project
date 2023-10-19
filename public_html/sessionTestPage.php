<?php
//Basic profile page that will tell us whether the user is logged in with proper session data or not
require_once(__DIR__ . "/../scripts/lib/functions.php");
?>

<html>
    <head>
	<meta charset="UTF-8">

    </head>
    <body>
        <p>
            Logged in: 
            <?php if(is_logged_in()) : ?>
                True 
                Username: <?php echo(get_session_username()) ?>
            <?php else : ?> 
                False 
            <?php endif ?>
        </p>
    </body>
</html>
<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>