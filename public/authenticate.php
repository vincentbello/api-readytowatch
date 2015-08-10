<?php
require_once("../includes/initialize.php");

// require key and id parameter.
if (empty($_POST['username']) || empty($_POST['key']) || empty($_POST['password'])) {
	sendResponse(400, "Invalid request. Please provide \"key\" and \"username\" and \"password\" parameters.");
} else if ($_POST['key'] != API_KEY) {
	sendResponse(403, "Invalid API key.");
} else {

	$user = User::authenticate($_POST['username'], $_POST['password']);

	if ($user) {

		sendResponse(200, json_encode($user->export()));

	} else {
		if (User::username_exists($_POST['username'])) {
			$response = array("message" => "Sorry, this password is incorrect.");
		} else {
			$response = array("message" => "Sorry, this username does not exist.");
		}
		sendResponse(200, json_encode($response));
	}

}



?>