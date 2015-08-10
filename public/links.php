<?php require_once("../includes/initialize.php");

// require key and id parameter.
if (empty($_GET['id']) || empty($_GET['key'])) {
	sendResponse(400, "Invalid request. Please provide \"ID\" and \"key\" parameters.");
} else if ($_GET['key'] != API_KEY) {
	sendResponse(403, "Invalid API key.");
} else {

	$links = Movie::export_links($_GET['id']);

	$linksObject = array("links" => $links);

	sendResponse(200, json_encode($linksObject));
}

?>