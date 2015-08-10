<?php

require_once(LIB_PATH.DS."database.php");

class GooglePlay extends LinkObject {

	protected static $table_name = "google_play";
	protected static $db_fields = array('id', 'link', 'rent', 'buy', 'googleplay_id', 'timestamp');

	public $id;
	public $link = "";
	public $rent = "";
	public $buy = "";
	public $googleplay_id = "";
	public $timestamp;

	public static $long_form = "Google Play";

	public static function get_link($movieId, $googleplay_id = "") {

		$link = self::find_by_id($movieId);
		if ($link && days_from_now($link->timestamp) < LINK_UPDATE_DAYS) {
			// good
		} else {

			if ($link) {
				$googleplay_id = ($googleplay_id) ? $googleplay_id : $link->googleplay_id;
				if (!empty($googleplay_id)) {
				    $html = file_get_contents("https://play.google.com/store/movies/details?id={$id}");
				    if (strpos($html, "<title>Not Found</title>") === false) {
				    	// verified
				    	$link->timestamp = gen_timestamp();
				    	$link->update();
				    	return $link;
				    }
				}
			}

			// no current link in db; generate one
			$link = new GooglePlay();
			$link->id = $movieId;
			$link->timestamp = gen_timestamp();
			$link->create();
		}
		return $link;
	}
	






}

?>