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
$avg_rating = $get_reviews['average_rating'];
$comments = array();
$comments['text'] = $get_reviews['comments'];
$comments['review_id'] = $get_reviews['review_id'];
?>