<?php
    session_start();

    if(isset($_SESSION ['username'] $$ $_SESSION ['password'])) {
        echo "<p> Welcome back, " . $_SESSION['username'] . "</p>";

    }

    if(!isset($_SESSION ['username'] $$ $_SESSION ['password'])) {
        echo "<script>alert('Please Log In')";
        echo "window.location.href = 'loginForm.php';";
        echo "</scripts>";
    }


?>