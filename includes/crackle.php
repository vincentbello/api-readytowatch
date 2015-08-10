<?php

require_once(LIB_PATH.DS."database.php");

class Crackle extends LinkObject {

	protected static $table_name = "crackle";
	protected static $db_fields = array('id', 'link', 'timestamp');

	public $id;
	public $link = "";
	public $timestamp;

	public static $long_form = "Crackle";

	public static function get_link($movieId) {

		$link = self::find_by_id($movieId);
		if ($link && days_from_now($link->timestamp) < LINK_UPDATE_DAYS) {
			// good
		} else {
			$exists = $link ? true : false;

			$movie = Movie::find_by_id($movieId);

			$link = new Crackle();
			$link->id = $movieId;
			$link->timestamp = gen_timestamp();

			$title = preg_replace('/[^a-z0-9]+/i', ' ', $movie->title);
			$url_friendly_title = str_replace(' ', '-', $title);
			if (strlen(file_get_contents("http://www.crackle.com/c/{$url_friendly_title}")) > 0) {
		        $link->link = "http://www.crackle.com/c/{$url_friendly_title}";
			}

			if ($exists) {
				$link->update();
			} else {
				$link->create();
			}
		}

		return $link;
	}
}

?>