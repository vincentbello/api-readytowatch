<?php

require_once(LIB_PATH.DS."database.php");

class Itunes extends LinkObject {

	protected static $table_name = "itunes";
	protected static $db_fields = array('id', 'link', 'rent', 'buy', 'itunesId', 'timestamp');

	public $id;
	public $link = "";
	public $rent = "";
	public $buy = "";
	public $itunesId = "";
	public $timestamp;

	public static $long_form = "iTunes Store";

	public static function get_link($movieId, $itunesId = NULL) {

		$link = self::find_by_id($movieId);
		$exists = $link ? true : false;
		if ($link && days_from_now($link->timestamp) < LINK_UPDATE_DAYS) {
			// good
		} else {
			$exists = $link ? true : false;

			if ($link) {
				// use the itunes ID
				$itunesId = ($itunesId) ? $itunesId : $link->itunesId;
			}

			$movie = Movie::find_by_id($movieId);

			$link = new Itunes();
			$link->id = $movieId;
			$link->timestamp = gen_timestamp();

			if ($itunesId == NULL) {
				$director = $movie->director();
				$t = urlencode($movie->title);
				$endpoint = "https://itunes.apple.com/search?term={$t}&media=movie&entity=movie&attribute=movieTerm";
				$r = self::getData($endpoint);
				$directorMatch = true;
				if ($r->resultCount > 0) {
					for ($j = 0; $j < $r->resultCount; $j++) {
			            $result = $r->results[$j];
						if ((abs(floor(($result->trackTimeMillis)/60000) - $runtime) < 20) &&
							((levenshtein($result->trackName, $title) < 5) || (strpos($result->trackName, $title) !== false))) {
							if ( (strpos($result->artistName, $director) !== false)
			                    || ( !$director && ($result->trackName == $title) && (substr($result->releaseDate,0,4) == $year) ) ) {	
			                    $link->link = $result->trackViewUrl;
								$rentArr = array();
								$buyArr = array();
								$rentArr[] = $result->trackRentalPrice;
								$rentArr[] = $result->trackHdRentalPrice;
								$buyArr[] = $result->trackPrice;
								$buyArr[] = $result->trackHdPrice;
								$link->rent = implode("|", $rentArr);
								$link->buy = implode("|", $buyArr);
								$link->itunesId = $result->trackId;
								break;
							}
						}
					}
				}
			} else {
				$link->itunesId = $itunesId;
			    $endpoint = "https://itunes.apple.com/lookup?id={$itunesId}";
			    $r = self::getData($endpoint);
			    if ($r->resultCount > 0) {
			        $link->link = $r->results[0]->trackViewUrl;
			        $rentArr = array();
			        $buyArr = array();
			        $rentArr[] = $r->results[0]->trackRentalPrice;
			        $rentArr[] = $r->results[0]->trackHdRentalPrice;
			        $buyArr[] = $r->results[0]->trackPrice;
			        $buyArr[] = $r->results[0]->trackHdPrice;
			        $link->rent = implode("|", $rentArr);
			        $link->buy = implode("|", $buyArr);
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
}

?>