<?php 
    
    session_start();

    $username = $_POST['username'];
    $password = $_POST['password'];

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">

    </head>

    <body>
        <form method="POST">
            <label for="username"> Username: </label><br>
            <input type="text" id="username" name="username"><br>
            
            <label for="password"> Password: </label><br>
            <input type="text" id="password" name="password"><br>>

            <button type="submit"> Login </button>

        </form>

    </body>

</html>