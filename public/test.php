<?php require_once("../includes/initialize.php");

$movies = Movie::find_popular("Itunes", 21);
foreach($movies as $movie) {
	print_r(json_encode($movie->export()));
	echo "<br><br>";
}

?>