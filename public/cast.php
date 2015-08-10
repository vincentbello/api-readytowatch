<?php require_once("../includes/initialize.php");

// require key and id parameter.
if (empty($_GET['id']) || empty($_GET['key'])) {
	sendResponse(400, "Invalid request. Please provide \"ID\" and \"key\" parameters.");
} else if ($_GET['key'] != API_KEY) {
	sendResponse(403, "Invalid API key.");
} else {

	$cast = export_arr(Actor::find_cast_for($_GET['id']));

	sendResponse(200, json_encode($cast));

}



?>