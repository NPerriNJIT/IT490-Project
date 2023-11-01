<?php
require_once(__DIR__ . "/../scripts/partials/nav.php");
if(!isset($_GET['id'])) {
    flash("Drink ID not set");
    die(header("Location: profile.php"));
}
//TODO: Make this page
$drink_id = $_GET['id'];
$drink = get_drink_info($drink_id);
$reviews = get_drink_reviews($drink_id);
$unrated = false;
if(empty($reviews)) {
    $unrated = true;
} else {
    $sum_ratings = 0;
    foreach($reviews as $review) {
        $sum_ratings+=$review['rating'];
    }
    $avg_rating = $sum_ratings/count($reviews);
}
if (isset($_POST["submit"])) {
    if (!isset($_POST["rating_number"]) || !isset($_POST["comment"])) {
        flash("Please fill all fields", "danger");
    } else {
        send_drink_review($drink_id, $_POST["rating_number"], $_POST["comment"]);
    }
}

?>
<?php echo(display_drink_info($drink)) ?>
<h1>Add Rating</h1>
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
<br>
<!-- Favorite button -->
    <input type="hidden" name="drink_id" value="<?php echo $drink_id; ?>">
    <button type="submit" name="favorite">Favorite</button>

</form>
</div>

<!-- Assuming you have your form and other content above this section -->

<?php
// Check if there are previous ratings and comments
if (!$unrated) {
    echo "<h2>Previous Ratings and Comments:</h2>";
    echo "<p>Average rating: " . $avg_rating . "</p>";
    if(!$unrated);
    foreach ($reviews as $review) {

        echo "<p>Rating: " . $review['rating'] . "</p>";
        echo "<div>";
        echo "<p>Comment: " . $review['comment'] . "</p>";
        echo "</div>";
    }
} else {
    echo "<h2>Be the first to leave a review!</h2>";
}


if (isset($_POST["favorite"])) {
    send_favorite($drink_id);
}
require(__DIR__ . "/../scripts/partials/flash.php");
?>