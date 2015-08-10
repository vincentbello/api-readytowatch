<?php require_once("../includes/initialize.php");

// require key and id parameter.
// GET OR POST REQUEST
if (!empty($_GET)) { // GET request

	if (empty($_GET['user_id']) || empty($_GET['key'])) {
		sendResponse(400, "Invalid request. Please provide \"key\" and \"user_id\" parameters.");
	} else if ($_GET['key'] != API_KEY) {
		sendResponse(403, "Invalid API key.");
	} else {
	
		$movies = Movie::get_favorites($_GET['user_id']);
	
		$results = array();
		foreach($movies as $movie) {
			$results[] = $movie->export();
		}
		$favorites = array("movies" => $results);
	
		sendResponse(200, json_encode($favorites));
	}
} else if (!empty($_POST)) { // POST request

	if (empty($_POST['user_id']) || empty($_POST['movie_id']) || empty($_POST['type']) || empty($_POST['key'])) {
		sendResponse(400, "Invalid request. Please provide \"key\", \"user_id\", \"type\", and \"movie_id\" parameters.");
	} else if ($_POST['key'] != API_KEY) {
		sendResponse(403, "Invalid API key.");
	} else {

		if ($_POST["type"] == "create") {
			$favorite = new Favorite();
			$favorite->user_id = $_POST['user_id'];
			$favorite->movie_id = $_POST['movie_id'];
			if ($favorite->create()) {
				sendResponse(200, json_encode("Success"));
			} else {
				sendResponse(403, "An error occurred.");
			}
		} else if ($_POST["type"] == "delete") {
			$favorite = Favorite::find($_POST['user_id'], $_POST['movie_id']);
			if ($favorite->delete()) {
				sendResponse(200, json_encode("Success"));
			} else {
				sendResponse(403, "An error occurred.");
			}
		}
	}
} else {
	sendResponse(403, "No request.");
}




?>