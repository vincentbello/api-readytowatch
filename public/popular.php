<?php require_once("../includes/initialize.php");

// require key and id parameter.
if (empty($_GET['type']) || empty($_GET['key'])) {
	sendResponse(400, "Invalid request. Please provide \"key\" and \"type\" parameters.");
} else if ($_GET['key'] != API_KEY) {
	sendResponse(403, "Invalid API key.");
} else {

	if (!empty($_GET['user_id'])) {
		$movies = Movie::find_popular($_GET['type'], $_GET['user_id']);
	} else {
		$movies = Movie::find_popular($_GET['type']);
	}

	$results = array();
	foreach($movies as $movie) {
		$results[] = $movie->export();
	}
	$popular = array("movies" => $results);

	sendResponse(200, json_encode($popular));


}



?>