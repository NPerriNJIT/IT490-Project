#!/usr/bin/php
<?php
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
require_once(__DIR__ . '/lib/functions.php');

function requestProcessor($request)
{
	echo "received request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type']))
	{
		return "ERROR: unsupported message type";
	}
	switch ($request['type'])
	{
	case "login":
		echo("running login function" . PHP_EOL); //Debugging output
		
		$response = array();
		$response['type'] = "login_response";
		$response['login_status'] = doLogin($request['username'],$request['password'],$request['session_id']);
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	case "registration":
		echo("running registration function");
		$response = array();
		$response['type'] = "registration_response";
		$response['registration_status'] = doRegistration($request['username'],$request['password']);
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	case "get_session_username":
		//TODO: add validate session
		$response = array();
		$response['type'] = "session_response";
		if(doValidate($request['session_id'])) {
			$response['username'] = getSessionUsername($request['session_id']);
			$response['session_status'] = "valid";
		} else {
			$response['session_status'] = "invalid";
		}
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	case "delete_session_data":
		echo "running delete session" . PHP_EOL;
		$response = array();
		$response['delete_session_status'] = delete_session($request['session_id']);
		echo "Sending response: " . PHP_EOL . var_dump($response);
		return $response;
	case "validate_session":
		echo "running session validation" . PHP_EOL;
		$response = array();
		$response['session_status'] = doValidate($request['session_id']);
		echo "Sending response: ".PHP_EOL . var_dump($response);
		return $response;
	case "get_drink_info":
		echo "running get drink info" . PHP_EOL;
		$response = array();
		$response = get_drink($request['drink_id']);
		echo "Sending response: " . PHP_EOL . var_dump($response);
		return $response;
	case "send_blog_post":
		echo "user sent blog post" . PHP_EOL;
		$response = array();
		$response['send_blog_post_status'] = send_blog_post($request['session_id'], $request['blog_post'], $request['blog_title']);
		return $response;
	case "get_blog_posts_user":
		echo "getting user blog posts" . PHP_EOL;
		$response = array();
		$response = get_blog_posts_user($request['user_id']);
		return $response;
	case "get_blog_posts_all":
		echo "getting all blog posts" . PHP_EOL;
		$response = array();
		$response = get_blog_posts_all();
		return $response;
	case "send_drink_review":
		echo "processing drink review" . PHP_EOL;
		$response = array();
		$response['send_drink_reviews_status'] = send_drink_review($request['drink_id'], $request['session_id'], $request['rating'], $request['comment']);
		return $response;
	case "get_drink_reviews":
		echo "getting drink reviews" . PHP_EOL;
		$response = array();
		$response = get_drink_reviews($request['drink_id']);
		return $response;
	case "send_favorite":
		echo "processing favorite";
		$response = array();
		$response = send_favorite($request['session_id'], $request['drink_id']);
		return $response;
	case "get_favorite_drinks":
		echo "getting favorites";
		$response = array();
		$response = get_favorite_drinks($request['user_id']);
		return $response;
	case "get_recommendations":
		echo "getting recommendations";
		$response = array();
		if(isset($request['amount'])) {
			$response = get_recommendations($request['user_id'], $request['amount']);
		} else {
			$response = get_recommendations($request['user_id']);
		}
		return $response;
	case "search_drinks":
		echo "search";
		$response = array();
		$response = search_drinks($request['search_string']);
		return $response;
	case "send_add_user_drink":
		echo "adding user drink";
		$response = array();
		$response = add_user_drink($request['drinkName'], $request['drinkTags'], $request['isPublic'], $request['alcholic'], 
		$request['ingredients'], $request['measurements'], $request['instructions'], $request['user_id']);
		return $response;
	}
	return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer(__DIR__ . "/testRabbitMQ.ini","testServer");
echo("Now listening for client messages...");
$server->process_requests('requestProcessor');
echo("Debug line". PHP_EOL);
//TODO: Make listener be always running until closed by an administrator
exit();
?>

