<?php require_once("../includes/initialize.php");

// require key and id parameter.
if (empty($_GET['id']) || empty($_GET['key'])) {
	sendResponse(400, "Invalid request. Please provide \"ID\" and \"key\" parameters.");
} else if ($_GET['key'] != API_KEY) {
	sendResponse(403, "Invalid API key.");
} else {

	$movie = Movie::find_by_id($_GET['id']);

	$result = $movie->export();
	sendResponse(200, json_encode($result));

}



?>