<?php
//Note: this is to resolve cookie issues with port numbers
//Written by Matt Toegel
//https://github.com/MattToegel
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
$localWorks = true; //some people have issues with localhost for the cookie params
//if you're one of those people make this false

//this is an extra condition added to "resolve" the localhost issue for the session cookie
if (($localWorks && $domain == "localhost") || $domain != "localhost") {
    session_set_cookie_params([
        "lifetime" => 60 * 60,
        "path" => "/",
        //"domain" => $_SERVER["HTTP_HOST"] || "localhost",
        "domain" => $domain,
        //Change secure to true once we have HTTPS
        "secure" => false,
        "httponly" => true,
        "samesite" => "lax"
    ]);
}
require_once(__DIR__ . "/../lib/functions.php");
session_start();
$isLoggedIn = is_logged_in();

?>
<!-- include css and js files -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
<script src="<?php echo get_url('helpers.js'); ?>"></script>

<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">AlchoholApp</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if ($isLoggedIn) : ?>
                    <li><a href="<?php echo get_url('profile.php'); ?>"> Profile </a></li>
                <?php endif; ?>
                <?php if (!$isLoggedIn) : ?>
                    <li><a href="<?php echo get_url('loginForm.php'); ?>">Login</a></li>
                    <li><a href="<?php echo get_url('register.php'); ?>">Register</a></li>
                <?php endif; ?>
                <?php if ($isLoggedIn) : ?>
                    <li><a href="<?php echo get_url('blog.php'); ?>">Blog</a></li>
                    <li><a href="<?php echo get_url('blogForm.php'); ?>">Blog Post</a></li>
                    <li><a href="<?php echo get_url('search.php'); ?>"> Search Drinks</a></li>
                    <li><a href="<?php echo get_url('adddrink.php'); ?>">Create A Drink</a></li>
                    <li><a href="<?php echo get_url('logout.php'); ?>"> Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</div>
