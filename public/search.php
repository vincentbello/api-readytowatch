<?php require_once("../includes/initialize.php");

// require key and id parameter.
if (empty($_GET['q']) || empty($_GET['key'])) {
	sendResponse(400, "Invalid request. Please provide \"q\" and \"key\" parameters.");
} else if ($_GET['key'] != API_KEY) {
	sendResponse(403, "Invalid API key.");
} else {

	$searchTerm = $database->escape_value($_GET['q']);
	$sql = "SELECT * FROM movies WHERE title LIKE '% {$searchTerm}%' OR title LIKE '{$searchTerm}%' ORDER BY popularity DESC LIMIT 6";
	$movies = Movie::find_by_sql($sql);

	$results = array();
	foreach($movies as $movie) {
		$results[] = $movie->export();
	}
	$popular = array("movies" => $results);

	sendResponse(200, json_encode($popular));

}



?>