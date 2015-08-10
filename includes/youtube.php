<?php

require_once(LIB_PATH.DS."database.php");

class YouTube extends LinkObject {

	protected static $table_name = "youtube";
	protected static $db_fields = array('id', 'videoId', 'timestamp');

	public $id;
	public $videoId = "";
	public $timestamp;

	public static $long_form = "YouTube";

	public static function get_link($movieId) {

		$link = self::find_by_id($movieId);
		$exists = $link ? true : false;
		if ($link && days_from_now($link->timestamp) < LINK_UPDATE_DAYS) {
			// good
		} else {
			$exists = $link ? true : false;

			$movie = Movie::find_by_id($movieId);

			$link = new YouTube();
			$link->id = $movieId;
			$link->timestamp = gen_timestamp();

			$title = urlencode($movie->title);
			$endpoint = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=10&q={$title}&type=video&videoCategoryId=30&key=AIzaSyBOaDIBNUCfbthPQ0XSZScnqI8jyxJ9G5Q";
			$m = self::getData($endpoint);
			
			foreach($m->items as $mov) {
				$desc = $mov->snippet->description;
		        if (levenshtein($title, $mov->snippet->title) < 2) {
				  foreach($castArr as $actor) {
				      if (strpos($desc, $actor) !== false) {
						$link->videoId = $mov->id->videoId;
						break 2; // break out of both foreach loops
					 }
				  }
		        }
			}

			if ($exists) {
				$link->update();
			} else {
				$link->create();
			}
		}
		return $link;
	}

	public function display_link() {
		return $this->has_link() ? "https://www.youtube.com/watch?v=" . $this->videoId : "";
	}

	public function has_link() {
		return (strlen($this->videoId) > 0) ? true : false; 
	}
	






}

?>