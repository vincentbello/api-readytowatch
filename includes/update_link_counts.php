<?php require_once("../includes/initialize.php");

foreach($LINK_CLASS_NAMES as $className) {
	$popular_movies = Movie::find_popular($className);


	$sql = "UPDATE popular SET link_count = {$linkCount} WHERE ";
	$database->query($sql);
}







?>