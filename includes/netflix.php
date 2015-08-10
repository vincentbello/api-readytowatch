<?php

require_once(LIB_PATH.DS."database.php");

class Netflix extends LinkObject {

	protected static $table_name = "netflix";
	protected static $db_fields = array('id', 'link', 'timestamp');

	public $id;
	public $link;
	public $timestamp;

	public static $long_form = "Netflix";

	public static function get_link($movieId) {

		$link = self::find_by_id($movieId);
		$exists = $link ? true : false;
		if ($link && days_from_now($link->timestamp) < LINK_UPDATE_DAYS) {
			// good
		} else {
			$exists = $link ? true : false;

			$movie = Movie::find_by_id($movieId);

			$link = new Netflix();
			$link->id = $movieId;
			$link->timestamp = gen_timestamp();

			$years_from_search = array();
		    $links_from_search = array();
		    $titles_from_search = array();
		    $html = file_get_contents("http://instantwatcher.com/titles?q=" . urlencode($movie->title));
		    $matches = strallpos($html, "<span class=\"releaseYear\">");
		    $link_matches = strallpos($html, "location.href");
		    $title_matches = strallpos($html, "class=\"title-list-item-link");
		    if (sizeof($matches) == sizeof($link_matches)) {
		
		        for($i = 0; $i < sizeof($matches); $i++) {
		            $years_from_search[] = substr($html, $matches[$i]+26, 4);
		            $links_from_search[] = str_replace('Player?movieid=', 'Movie/', strstr(substr($html, $link_matches[$i]+17), "'", true));
		            $titles_from_search[] = substr(strstr(strstr(substr($html, $title_matches[$i]), ">"), "</a>", true), 1);
		    
		            if (($years_from_search[$i] == $movie->year) && (levenshtein($titles_from_search[$i], $movie->title) < 2)) {
		                $link->link = $links_from_search[$i];
		                break;
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
	






}

?>