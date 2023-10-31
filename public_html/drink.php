<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
if(!isset($_GET['id'])) {
    flash("Drink ID not set");
    die(header("Location: profile.php"));
}
//TODO: Make this page
$drink_id = $_GET['id'];
$drink = get_drink_info($drink_id);
$get_reviews = get_drink_reviews($drink_id);
$unrated = false;
if($get_reviews === 'No reviews') {
    $unrated = true;
} else {
    $comments = array();
    $avg_rating = $get_reviews['average_rating'];
    $comments['text'] = $get_reviews['comments'];
    $comments['review_id'] = $get_reviews['review_id'];
}
if (isset($_POST["submit"])) {
    if (!isset($_POST["rating_number"]) || !isset($_POST["comment"])) {
        flash("Please fill all fields", "danger");
    } else {
        send_drink_review($drink_id, $_POST["rating_number"], $_POST["comment"]);
    }
}

?>
<h1>Add Rating</h1>
<?php echo(display_drink_info($drink)) ?>
<?php echo(var_dump($drink)) ?>
<p><?php echo($get_reviews)?></p>
<form method="POST">
    <h3>Rating</h3>
	<label for="1">☆</label>
  	<input type="radio" id="1" name="rating_number" value="1">
      <br>
	<label for="2">☆☆</label>
 	<input type="radio" id="2" name="rating_number" value="2">
    <br>
  	<label for="3">☆☆☆</label>
  	<input type="radio" id="3" name="rating_number" value="3">
      <br>
  	<label for="4">☆☆☆☆</label>
  	<input type="radio" id="4" name="rating_number" value="4">
      <br>
    <label for="5">☆☆☆☆☆</label>
  	<input type="radio" id="5" name="rating_number" value="5">
      <br>
<br>
    <h4>Comments</h4>
        <label for="comment"></label><br>
        <input type="text" id="comment" name="comment"><br>
        <input type="submit" value="Submit" name="submit">
</form>
</div>

<!-- Assuming you have your form and other content above this section -->

<?php
// Check if there are previous ratings and comments
if (!empty($comments)) {
    echo "<h2>Previous Ratings and Comments:</h2>";
    if(!$unrated);
    foreach ($comments as $review) {
        
        echo "<div>";
        echo "<p>Comment: " . $review['text'] . "</p>";
        echo "</div>";
    }
}
require(__DIR__ . "/../scripts/partials/flash.php");
?>