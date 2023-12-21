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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
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
