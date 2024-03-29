#!/usr/bin/php
<?php
require_once(__DIR__ . "/db.php");
require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');
function doLogin($username, $password, $session_id)
{
	$db = getDB();
	$stmt = $db->prepare("SELECT id, username, password from Users where username = :username");
	try {
		$r = $stmt->execute([":username" => $username]);
		if ($r) {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($user) {
				$hash = $user["password"];
				unset($user["password"]);
				if (password_verify($password, $hash)) {
					echo($user . " logged in successfully");
					create_session($session_id, $user['id']);
					return "success";
				} else {
					echo($user . " failed login attempt");
					return "denied";
				}
			} else {
				echo($user . "does not exist");
				return "denied but username";
				//IMPORTANT: Don't display to the client whether username or password was incorrect, has to be the same message.
				//We can log that here though.
			}
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}
function doRegistration($username, $password)
{
	//Sanitization should be done on front-end
	
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Users (password, username) VALUES(:password, :username)");
	try {
		$stmt->execute([":password" => $password, ":username" => $username]);
	echo $username . " registered successfully";
	return "success";
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "failure";

}
function create_session($session_id, $user_id) {
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO  Sessions (session_id, user_id) VALUES (:session_id, :user_id)");
	try {
		$stmt->execute([":session_id" => $session_id, ":user_id" => $user_id]);
		echo "Started session with session_id " . $session_id . " for user id " . $user_id . PHP_EOL;
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}

function doValidate($sessionID)
{
	$db = getDB();
	$stmt = $db->prepare("Select * from Sessions where session_id = :sessionID");
	try {
		$stmt->execute([":sessionID" => $sessionID]);
		if($stmt->rowCount() > 0) {
			echo "Valid session";
			return "valid";
		} else {
			echo "Invalid session requested";
			return "invalid";
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
		echo "Something crazy happened";
		return false;
	}
}

function getSessionUsername($sessionID) {
	$db = getDB();
	$stmt = $db->prepare("Select user_id from Sessions where session_id = :sessionID");
	try {
		$r = $stmt->execute([":sessionID" => $sessionID]);
		if($r) {
			$session = $stmt->fetch(PDO::FETCH_ASSOC);
			$user_id = $session['user_id'];
			$stmt = $db->prepare("Select username from Users where id = :userID");
			try{
				$r = $stmt->execute([":userID" => $user_id]);
				if($r) {
					$user = $stmt->fetch(PDO::FETCH_ASSOC);
					$username = $user['username'];
					return $username;
				}
			} catch (Exception $e) {
				echo "Error: " . $e->getMessage();
			}
		} else {

		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "error";
}

function delete_session($session_id) {
	$db = getDB();
	$stmt = $db->prepare("Delete from Sessions where session_id = :session_id");
	try {
		$stmt->execute([":session_id" => $session_id]);
		echo "Deleted session " . $session_id . PHP_EOL;
		return "deleted";
	} catch (Exception $e) {
		echo "Error deleting session " . $session_id . ": " . $e . PHP_EOL; 
		return "did not exist";
	}
}

function get_session_user_id($session_id) {
	$db = getDB();
	$stmt = $db->prepare("Select user_id from Sessions where session_id = :sessionID");
	try {
		$r = $stmt->execute([":sessionID" => $session_id]);
		if($r) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			return $result['user_id'];
		} else {
			return "session doesn't exist";
		}
			
	} catch (Exception $e) {
		echo "Error: " . $e;
		return "error";
	}
}

//Gets drink info
function get_drink($drink_id) {
	$response = array();
	$db = getDB();
	$stmt = $db->prepare("Select * from Drinks where drink_id = :drink_id");
	try{
		$r = $stmt->execute([":drink_id" => $drink_id]);
		if($r) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$response['drink_info'] = $result;
			$response['get_drink_info_status'] = "valid";
			return $response;

		} else {
			$response['get_drink_info_status'] = "invalid";
			return $response;
		}
	} catch (Exception $e) {
		echo "Error: " . $e;
		$response = array();
		$response['get_drink_info_status'] = "invalid";
		return $response;
	}
}

//Processes blog post
function send_blog_post($session_id, $blog_post, $blog_title) {
	$user_id = get_session_user_id($session_id);
	if(!is_int($user_id)) {
		return "user id error";
	}
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Blogs (user_id, blog_post, blog_title) VALUES(:user_id, :blog_post, :blog_title)");
	try {
		$stmt->execute([":user_id" => $user_id, ":blog_post" => $blog_post, ":blog_title" => $blog_title]);
		echo  "Blog posted successfully";
		return "valid";
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "failure";
}

//Get blog posts for a specific user
function get_blog_posts_user($user_id) {
	$db = getDB();
	$response = array();
	$stmt = $db->prepare("Select Blogs.blog_post, Blogs.blog_title, Users.username from Blogs inner join Users On Blogs.user_id = Users.id where Blogs.user_id = :user_id order by Blogs.blog_id desc;");
	try{
		$r = $stmt->execute([":user_id" => $user_id]);
		if($r) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$response['get_blog_posts_user_status'] = "valid";
			$response['blog_posts'] = $result;
			return $response;

		} else {
			$response['get_blog_posts_user_status'] = "invalid";
			return $response;
		}
	} catch (Exception $e) {
		echo "Error: " . $e;
		$response['get_blog_posts_user_status'] = "invalid";
		return $response;
	}
}

//Get blog posts for all users
function get_blog_posts_all() {
	$db = getDB();
	$response = array();
	$stmt = $db->prepare("Select Blogs.blog_post, Blogs.blog_title, Users.username from Blogs inner join Users On Blogs.user_id = Users.id order by Blogs.blog_id desc;");
	try{
		$r = $stmt->execute();
		if($r) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$response['get_blog_posts_all_status'] = "valid";
			$response['blog_posts'] = $result;
			return $response;

		} else {
			$response['get_blog_posts_all_status'] = "invalid";
			return $response;
		}
	} catch (Exception $e) {
		echo "Error: " . $e;
		$response['get_blog_posts_all_status'] = "invalid";
		return $response;
	}
}


//TODO: Delete redundant function from frontend
function get_drink_reviews($drink_id) {
	$db = getDB();
	$stmt = $db->prepare("Select rating, comment, id from Ratings where drink_id = :drink_id");
	try{
		$r = $stmt->execute([":drink_id" => $drink_id]);
		if($r) {
			$response = array();
			$response['get_drink_reviews_status'] = "valid";
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$response['reviews'] = $result;
			return $response;

		} else {
			$response = array();
			$response['get_drink_reviews_status'] = "invalid";
			return $response;
		}
	} catch (Exception $e) {
		echo "Error: " . $e;
		$response = array();
		$response['get_drink_reviews_status'] = "invalid";
		return $response;
	}
}

function send_drink_review($drink_id, $session_id, $rating, $comment) {
	$user_id = get_session_user_id($session_id);
	if(!is_int($user_id)) {
		echo "funky error" . PHP_EOL;
		return "user id error";
	}
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Ratings (user_id, drink_id, rating, comment) VALUES(:user_id, :drink_id, :rating, :comment)");
	try {
		$stmt->execute([":user_id" => $user_id, ":drink_id" => $drink_id, ":rating" => $rating, ":comment" => $comment]);
		echo  "Review posted successfully";
		return "valid";
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "failure";
}

function send_favorite($session_id, $drink_id) {
	$user_id = get_session_user_id($session_id);
	if(!is_int($user_id)) {
		return "user id error";
	}
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Favorites (user_id, drink_id) VALUES(:user_id, :drink_id)");
	try {
		$stmt->execute([":user_id" => $user_id, ":drink_id" => $drink_id]);
		echo  "Drink favorited successfully";
		return "valid";
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return "failure";
}

function get_favorite_drinks($user_id) {
	$db = getDB();
	$stmt = $db->prepare("Select drink_id from Favorites where user_id = :user_id");
	try {
		$r = $stmt->execute([":user_id" => $user_id]);
		if($r) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$response['get_favorite_drinks_status'] = "valid";

			$response['drink_ids'] = $result;
			return $response;
		}
	} catch (Exception $e){
		echo "Error: " . $e;
		$response['get_favorite_drinks_status'] = "invalid";
		return $response;
	}
}

function get_recommendations($session_id, $amount = 4) {
	//Find favorites
	$db = getDB();
	$response = array();
	$user_id = get_session_user_id($session_id);
	$favorites = get_favorite_drinks($user_id);
	$favorite_ids = $favorites['drink_ids'];
	if(count($favorite_ids) == 0) {
		$response['get_recommendations_status'] = 'valid';
		$response['recommendations'] = [];
		return $response;
	}
	$recommendations = array();
	$liked_ingredients = array();
	//Compile an array of liked ingredients
	foreach($favorite_ids as $favorite_drink) {
		$stmt = $db->prepare("Select ingredient_id from Drink_Ingredients where drink_id = :drink_id");
		try {
			$r = $stmt->execute(['drink_id' => $favorite_drink['drink_id']]);
			if($r) {
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$liked_ingredients_rows = $result;
				foreach($liked_ingredients_rows as $ingredient) {
					array_push($liked_ingredients, $ingredient['ingredient_id']);
				}
			}
		} catch (Exception $e) {
			echo("Error: " . $e);
			$response['get_recommendations_status'] = "invalid";
			return $response;
		}
	}
	//Order by most liked
	$count_instances = array_count_values($liked_ingredients);
	arsort($count_instances);
	$weighted_ingredients = array_keys($count_instances);
	//Find top $amount drinks, first come first serve based on ingredient preference, then randomize drinks that are weighted the same
	$recommendation_ids = array();
	foreach($weighted_ingredients as $ingredient_id) {
		$stmt = $db->prepare("Select distinct drink_id from Drink_Ingredients where ingredient_id = :ingredient_id");
		try {
			$r = $stmt->execute(['ingredient_id' => $ingredient_id]);
			if($r) {
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach($result as $recommendation) {
					array_push($recommendation_ids, $recommendation['drink_id']);
				}
				shuffle($recommendation_ids);
			}
			if(count($recommendation_ids) >= $amount) {
				break;
			} 
		} catch (Exception $e) {
			echo("Error: " . $e);
			$response['get_recommendations_status'] = "invalid";
			return $response;
		}
	}
	//Double check that we are only recommending $amount of drinks, array is already randomized so it will remove randomly
	while(count($recommendation_ids) >= $amount) {
		array_shift($recommendation_ids);
	}
	//Get drink info for recommendations
	foreach($recommendation_ids as $drink_id) {
		$drink_info = get_drink($drink_id);
		array_push($recommendations, $drink_info['drink_info']);
	}
	$response['get_recommendations_status'] = 'valid';
	$response['recommendations'] = $recommendations;
	return $response;
}

function search_drinks($search_string) {
	$db = getDB();
	$response = array();
	$search_string_fixed = "%" . $search_string . "%";
	$response['search_drinks_status'] = "invalid";
    $response['search_results'] = array();
	$stmt = $db->prepare("Select * from Drinks where drink_id like :search_string or drink_name like :search_string or drink_tags like :search_string or ingredients like :search_string");
	try {
		$r = $stmt->execute([':search_string' => $search_string_fixed]);
		if($r) {
			$search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($search_results)) {
                $response['search_results'] = $search_results;
            }
			$response['search_drinks_status'] = "valid";
		}
	} catch (Exception $e) {
		echo("Error: " . $e);
	}
	$stmt = $db->prepare("Select * from UserDrinks where is_public = 1 and (drink_id like :search_string or drink_name like :search_string or drink_tags like :search_string or ingredients like :search_string)");
	try {
		$r = $stmt->execute([':search_string' => $search_string_fixed]);
		if($r) {
			$search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($search_results)) {
                if(empty($response['search_results'])) {
                    $response['search_results'] = $search_results;
                } else {
                    $response['search_results'] = array_merge($response['search_results'], $search_results);
                }
            }
            $response['search_drinks_status'] = "valid";
		}
	} catch (Exception $e) {
		echo("Error: " . $e);
	}

	return $response;
}
function check_user_exists($user_id) {
	$db = getDB();
	$response = array();
	$response['check_user_exists_status'] = 'invalid';
	$stmt = $db->prepare("Select id from Users where id = :user_id");
	try {
		$r = $stmt->execute([':user_id' => $user_id]);
		if($r) {
			$response['check_user_exists_status'] = 'valid';
		}
	} catch (Exception $e) {
		echo("Error: " . $e);
	}
	return $response;
}

function add_user_drink($session_id, $drinkName, $drinkTags, $isPublic, $alcoholic, $ingredients, $measurements, $instructions) {
    // Get the user ID from the session
    $user_id = get_session_user_id($session_id);

    // Check if user_id is valid (not an error)
    if (!is_int($user_id)) {
        echo "Invalid user ID";
        return "failure";
    }
	$public = 0;
	if ($isPublic === 'Y')
    {
        $public = 1;
    }
	$is_alcoholic = 0;
    if ($alcoholic === 'Y')
    {
        $is_alcoholic = 1;
    }
    // Get the database connection
    $db = getDB();
    // Prepare the SQL query
    $stmt = $db->prepare("INSERT INTO UserDrinks (drink_name, drink_tags, is_public, alcoholic, ingredients, measurements, instructions, user_id) 
                          VALUES (:drink_name, :drink_tags, :is_public, :alcoholic, :ingredients, :measurements, :instructions, :user_id)");

    // Bind parameters
    $stmt->bindParam(":drink_name", $drinkName);
    $stmt->bindParam(":drink_tags", $drinkTags);
    $stmt->bindParam(":is_public", $public);
    $stmt->bindParam(":alcoholic", $is_alcoholic);
    $stmt->bindParam(":ingredients", $ingredients);
    $stmt->bindParam(":measurements", $measurements);
    $stmt->bindParam(":instructions", $instructions);
    $stmt->bindParam(":user_id", $user_id);

    try {
        // Execute the query
        if ($stmt->execute()) {
            echo "Drink added successfully";
            return "valid";
        } else {
            echo "Error executing the query";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    return "failure";
}

function get_username_user_id($user_id) {
	$db = getDB();
	$response = array();
	$response['get_username_user_id_status'] = 'invalid';
	$stmt = $db->prepare("Select username from Users where id = :userID");
	try{
		$r = $stmt->execute([":userID" => $user_id]);
		if($r) {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			$username = $user['username'];
			$response['username'] = $username;
			$response['get_username_user_id_status'] = 'valid';
		} else {
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	return $response;
}

function get_user_drinks($user_id, $get_private) {
	$db = getDB();
	$response = array();
	$response['get_user_drinks_status'] = 'invalid';
	if($get_private) {
		$stmt = $db->prepare("Select * from UserDrinks where user_id = :user_id");
	} else {
		$stmt = $db->prepare("Select * from UserDrinks where user_id = :user_id AND is_public = 1");
	}
	try {
		$r = $stmt->execute(['user_id' => $user_id]);
		if($r) {
			$drink_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$response['drink_info'] = $drink_info;
			$response['get_user_drinks_status'] = 'valid';
		}
	} catch (Exception $e) {
		echo("Error: $e");
	}
	return $response;
}

function get_profile($user_id, $is_user, $session_id) {
	//TODO: Make all RabbitMQ stuff happen in 1 request
	//get_session_user_id, get_username, get_favorite_drinks, get_drink_info, get_user_drinks, get_recommendations
	//compile into get_profile function
	//TODO: This still needs work
	$response = array();
	$response['username'] = get_username_user_id($user_id);
	$response['favorites'] = get_favorite_drinks($user_id);
	get_user_drinks($user_id, $is_user);
	get_recommendations($session_id);
}

//Get top rated public drinks
function get_top_drinks() {
    $response = array();
    $response['get_top_drinks_status'] = 'invalid';
    $db = getDB();
    $stmt = $db->prepare("SELECT D.*, R.avg_rating FROM Drinks D JOIN (SELECT drink_id, AVG(rating) AS avg_rating FROM Ratings GROUP BY drink_id ORDER BY avg_rating DESC LIMIT 10) R ON D.drink_id = R.drink_id");
    try {
        $r = $stmt->execute();
        if($r) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['get_top_drinks_status'] = 'valid';
            echo("Got top drinks from Ratings");
            $response['top_drinks'] = $results;
        }
    } catch (Exception $e) {
        echo("Error: " . $e);
    }
    return $response;
}
function get_user_activity() {
	$response = array();
	$response['get_user_activity_status'] = 'invalid';
	$db = getDB();
	$stmt = $db->prepare(
		"(
			SELECT user_id, created, blog_title, NULL AS drink_id, NULL AS rating, NULL AS comment, NULL AS drink_name
			FROM Blogs
			ORDER BY created DESC
			LIMIT 10
		)
		UNION
		(
			SELECT user_id, created, NULL AS blog_title, drink_id, rating, comment, NULL AS drink_name
			FROM Ratings
			ORDER BY created DESC
			LIMIT 10
		)
		UNION
		(
			SELECT user_id, created, NULL AS blog_title, drink_id, NULL AS rating, NULL AS comment, NULL AS drink_name
			FROM Favorites
			ORDER BY created DESC
			LIMIT 10
		)
		UNION
		(
			SELECT user_id, created, NULL AS blog_title, NULL AS drink_id, NULL AS rating, NULL AS comment, drink_name
			FROM UserDrinks
			WHERE is_public = true
			ORDER BY created DESC
			LIMIT 10
		)
		ORDER BY created DESC
		LIMIT 10;");
    try {
        $r = $stmt->execute();
        if($r) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['get_user_activity_status'] = 'valid';
            echo("sending user activity");
            $response['user_activity'] = $results;
        }
    } catch (Exception $e) {
        echo("Error: " . $e);
    }
    return $response;
}
function get_user_follow_activity($session_id) {
	$response = array();
	$response['get_user_follow_activity_status'] = 'invalid';
	$db = getDB();
	$stmt = $db->prepare();
    try {
        $r = $stmt->execute();
        if($r) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['get_user_follow_activity_status'] = 'valid';
            echo("getting user follow activity");
            $response['user_follow_activity'] = $results;
        }
    } catch (Exception $e) {
        echo("Error: " . $e);
    }
    return $response;
}
?>