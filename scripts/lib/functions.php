<?php
require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');

//se() makes sure that variables that will be outputted via html
//are properly outputted
function se($v, $k = null, $default = "", $isEcho = true)
{
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}

function is_logged_in($redirect = false, $destination = "loginForm.php")
{
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "validate_session";
	$request['session_id'] = session_id();
	//send session ID to see if logged in

	$response = $client->send_request($request);


	if(isset($response['session_status']) && $response['session_status'] === "valid") {
		$isLoggedIn = true;
	} else {
		$isLoggedIn = false;
	}
	//receives whether or not 
	if ($redirect && !$isLoggedIn) {
		flash("You must be logged in to view this page", "warning");
		die(header("Location: $destination"));
	}
	return $isLoggedIn;
}

function get_session_username() {
	if(is_logged_in()) {
		$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
		$request = array();
		$request['type'] = "get_session_username";
		$request['session_id'] = session_id();
		$response = $client->send_request($request);
		//Waits for a response from the server

		if(isset($response['type']) && $response['type'] == "session_response") {
			if(isset($response['session_status']) && $response['session_status'] == "valid") {
				return $response['username'];
			}
		} else {
			flash("ERROR IN FUNCTIONS SEE LINE 77", "danger");
		}
	}
}
//Used for outputting messages onto the webpage with different colors for different types of messages
//"success" = green, "warning" = yellow, "danger" = red
function flash($msg = "", $color = "info")
{
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}
//Helper function for flash()
function getMessages()
{
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}
//Used for hard resetting sessions, will delete it from database if it exists
function reset_session()
{
	if(session_status() == PHP_SESSION_ACTIVE) {
		delete_session_data(session_id());

    	session_unset();
    	session_destroy();
	}
    session_start();
}
//Sends message to delete this session from database if it exists
function delete_session_data($session_id) {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
	$message = array();
	$message['type'] = "delete_session_data";
	$message['session_id'] = $session_id;
	$response = $client->send_request($message);
    flash("Successfully logged out", "success");
	//TODO: log response
}
//Gets the url of a page, used for nav
function get_url($dest)
{
    global $BASE_PATH;
    if (str_starts_with($dest, "/")) {
        //handle absolute path
        return $dest;
    }
    //handle relative path
    return $BASE_PATH . $dest;
}
//THESE FUNCTIONS ARE FOR PERSONAL DELIVERABLES, NOT DONE YET
//Sends blog post to DB
function send_blog_post($blog_title, $blog_post)
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'send_blog_post';
    $request['blog_title'] = $blog_title; 
    $request['blog_post'] = $blog_post;
    $request['session_id'] = session_id();
    $response = $client->send_request($request);
    if(isset($response['send_blog_post_status']) && $response['send_blog_post_status'] === 'valid') {
        flash("Blog post succesfully sent", "success");
		die(header("Location: sessionTestPage.php"));
    } else {
        flash("Blog post failed to send", "warning");
    }
}

//Send drink review to DB
function send_drink_review($drink_id, $rating, $comment)
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'send_drink_review';
    $request['drink_id'] = $drink_id;
    $request['rating'] = $rating;
	$request['comment'] = $comment;
    $request['session_id'] = session_id();
    $response = $client->send_request($request);
    if(isset($response['drink_review_status']) && $response['drink_review_status'] === "valid") {
		print_r($response);
        flash("Drink successfully reviewed", "success");
    } else {
        flash("Drink review failed", "warning");
    }
}

//Get blog posts from user
function get_blog_posts_user($user_id) 
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'get_blog_posts_user';
    $request['user_id'] = $user_id;
    $response = $client->send_request($request);
    if(isset($response['get_blog_posts_user_status']) && $response['get_blog_posts_user_status'] === 'valid') {
        return $response['blog_posts'];
    } else {
        flash("Failed to get blog posts", "warning");
        return [];
    }
}

//Get all blog posts
function get_blog_posts_all() 
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'get_blog_posts_all';
    $response = $client->send_request($request);
    if(isset($response['get_blog_posts_all_status']) && $response['get_blog_posts_all_status'] === 'valid') {
        return $response['blog_posts'];
    } else {
        flash("Failed to get blog posts", "warning");
        return [];
    }
}

//Get reviews for a specific drink
function get_drink_reviews($drink_id)
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'get_drink_reviews';
    $request['drink_id'] = $drink_id;
    $response = $client->send_request($request);
    if(isset($response['get_drink_reviews_status']) && $response['get_drink_reviews_status'] === 'valid') {
		$reviews = $response['reviews'];
		return $reviews;
    } else {
        flash("Failed to get drink reviews", "warning");
		return [];
    }
}

//Add drink as favorite
function send_favorite($drink_id) 
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'send_favorite';
    $request['drink_id'] = $drink_id;
    $request['session_id'] = session_id();
    $response = $client->send_request($request);
    if(isset($response['send_favorite_status']) && $response['send_favorite_status'] === 'valid') {
        flash("Successfully favorited", "success");
    } else {
        flash("Failed to favorite drink", "danger");
    }
}

//Get favorite drinks
function get_favorite_drinks($user_id)
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'get_favorite_drinks';
    $request['user_id'] = $user_id;
    $response = $client->send_request($request);
    if(isset($response['get_favorite_drinks_status']) && $response['get_favorite_drinks_status'] === 'valid') {
        if($response['drink_ids'] > 0) {
            return $response['drink_ids'];
        }
        return "No favorites";
    } else {
        flash("Failed to get favorite drinks", "warning");
    }
}

//Get drink info
function get_drink_info($drink_id) {
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'get_drink_info';
    $request['drink_id'] = $drink_id;
    $response = $client->send_request($request);
    if(isset($response['get_drink_info_status']) && $response['get_drink_info_status'] === 'valid') {
        $drink_info = array();
        $drink_info = $response['drink_info'];
		unset($drink_info['get_drink_info_status']);
        return $drink_info;
    } else {
        flash("Error retrieving drink info", "danger");
        die(header("Location: profile.php"));
        return false;
    }
}

//Get user id from session
function get_session_user_id() {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "get_session_user_id";
	$request['session_id'] = session_id();
	$response = $client->send_request($request);
	//Waits for a response from the server

		
	if(isset($response['get_session_user_id_status']) && $response['get_session_user_id_status'] == "valid") {
		return $response['user_id'];
	}
}

function check_user_exists($user_id) {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "check_user_exists";
	$request['user_id'] = $user_id;
	$response = $client->send_request($request);
	//Waits for a response from the server

	
	if(isset($response['check_user_exists_status'])) {
		return $response['check_user_exists_status'] === 'valid';
	}
	return false;
}

function get_username($user_id) {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "get_username_user_id";
	$request['user_id'] = $user_id;
	$response = $client->send_request($request);
	//Waits for a response from the server

		
	if(isset($response['get_username_user_id_status']) && $response['get_username_user_id_status'] == "valid") {
		return $response['username'];
	}
}

function display_drink_info($drink) {
	//TODO: Add hyperlink to drink on the drink name
	$drink_info = "<li>Drink Name: " . $drink['drink_name'] . "</li>" . PHP_EOL;
	$drink_info = $drink_info . "<li>Drink ID: " . $drink['drink_id'] . "</li>" . PHP_EOL;
	$drink_info = $drink_info . "<li>Drink Category: " . $drink['tags'] . "</li>" . PHP_EOL;
	$drink_info = $drink_info . "<li>Is alcoholic?: " . $drink['alcoholic'] . "</li>" . PHP_EOL;
	$drink_info = $drink_info . "<li>Ingredients: " . $drink['ingredients'] . "</li>" . PHP_EOL;
	$drink_info = $drink_info . "<li>Measurements: " . $drink['measurements'] . "</li>" . PHP_EOL;
	$drink_info = $drink_info . "<li>Instructions: " . $drink['instructions'] . "</li>" . PHP_EOL . "<hr>";
	return $drink_info;
}

function get_recommendations() {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "get_recommendations";
	$request['user_id'] = session_id();
	$response = $client->send_request($request);
	if(isset($response['get_recommendations_status']) && $response['get_recommendations_status'] === 'valid') {
		return $response['drinks'];
	} else {
		echo flash("Failed to get recommendations", "warning");
		return "error";
	}
}

function search_drinks($search_string) {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "search_drinks";
	$request['search_string'] = $search_string;
	$response = $client->send_request($request);
	if(isset($response['search_drinks_status']) && $response['search_drinks_status'] === 'valid') {
		return $response['search_results'];
	} else {
		flash("Error searching", "warning");
	}
}

function add_user_drink($drinkName, $drinkTags, $alcoholic, $isPublic, $ingredients, $measurements, $instructions)
{
    $client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'send_add_user_drink';
    $request['drinkName'] = $drinkName;
    $request['drinkTags'] = $drinkTags;
	$request['isPublic'] = $isPublic;
	$request['alcoholic'] = $alcoholic;
	$request['ingredients'] = $ingredients;
	$request['measurements'] = $measurements;
	$request['instructions'] = $instructions;
    $request['session_id'] = session_id();
    $response = $client->send_request($request);
    if(isset($response['send_add_user_drink_status']) && $response['send_add_user_drink_status'] === 'valid') {
        flash("User drink information sent", "success");
		die(header("Location: sessionTestPage.php"));
    } else {
        flash("User drink information failed to send", "warning");
    }
}
function get_user_drinks($user_id, $get_private = false) {
	$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testServer");
    $request = array();
    $request['type'] = 'get_user_drinks';
	$request['user_id'] = $user_id;
	$request['get_private'] = $get_private;
	$response = $client->send_request($request);
	if(isset($response['get_user_drinks_status']) && $response['get_user_drinks_status'] === 'valid') {
		return $response['drink_info'];
	}
}