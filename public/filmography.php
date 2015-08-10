<?php require_once("../includes/initialize.php");

// require key and actor id parameter.
if (empty($_GET['id']) || empty($_GET['key'])) {
	sendResponse(400, "Invalid request. Please provide \"ID\" and \"key\" parameters.");
} else if ($_GET['key'] != API_KEY) {
	sendResponse(403, "Invalid API key.");
} else {

	$movies = Movie::get_filmography($_GET['id']);

	$results = array();
	foreach($movies as $movie) {
		$results[] = $movie->export();
	}
	sendResponse(200, json_encode($results));
}



?>