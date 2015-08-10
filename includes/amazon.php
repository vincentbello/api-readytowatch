<?php

require_once(LIB_PATH.DS."database.php");

class Amazon extends LinkObject {

	protected static $table_name = "amazon";
	protected static $db_fields = array('id', 'link', 'rent', 'buy', 'asin', 'timestamp');

	public $id;
	public $link = "";
	public $rent = "";
	public $buy = "";
	public $asin = "";
	public $timestamp;

	public static $long_form = "Amazon Instant Video";

	public static function get_link($movieId) {

		$link = self::find_by_id($movieId);
		$exists = $link ? true : false;
		if ($link && days_from_now($link->timestamp) < LINK_UPDATE_DAYS) {
			// good
		} else {
			$exists = $link ? true : false;

			$movie = Movie::find_by_id($movieId);

			$link = new Amazon();
			$link->id = $movieId;
			$link->timestamp = gen_timestamp();

			require_once(LIB_PATH.DS."amazon_api_class.php");
			$runtime = $movie->runtime;
			$title = preg_replace('/[^a-z0-9]+/i', ' ', $movie->title);
		    $obj = new AmazonProductAPI();
		    try {
		        $result = $obj->searchProducts($title,
		        	                              AmazonProductAPI::VIDEO,
		                                          "TITLE");
		    }
		    catch (Exception $e) {
		        echo $e->getMessage();
		    }
	
		    $cast = Actor::list_of_names($movie->id);

		    foreach($result->Items->Item as $k) {
		    if ($k->ItemAttributes->Binding == 'Amazon Instant Video') {
		    if (((abs($k->ItemAttributes->RunningTime - $runtime) < 5) && (strpos($cast,(string)$k->ItemAttributes->Actor) !== false))
		    	|| ((abs($k->ItemAttributes->RunningTime - $runtime) < 5) && (levenshtein($title,(string)$k->ItemAttributes->Title) <= 3))) {
		    	$link->link = $k->DetailPageURL;
		    	$link->asin = $k->ASIN; 
		    	break;
		    } }	}
		    if (strlen($link->link) > 0) {
		        $html = file_get_contents($link->link);
		            if ((!strpos($html, 'movie is currently unavailable')) && (strlen($html) > 20)) {
		            $html = substr($html, strpos($html, '<ul class="button-list">'), 2000);
		            $matches = array();
		            $rent = "";
		            $buy = "";
		            preg_match_all("/Rent SD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
		            if (sizeof($matches[0]) > 0) $rent .= substr($matches[0][0], 8);
		            preg_match_all("/Rent HD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
		            if (sizeof($matches[0]) > 0) $rent .= "|" . substr($matches[0][0], 8);
		        	preg_match_all("/Buy SD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
		            if (sizeof($matches[0]) > 0) $buy .= substr($matches[0][0], 7);
		            preg_match_all("/Buy HD [$][1-9]+[.][1-9][1-9]/", $html, $matches);
		            if (sizeof($matches[0]) > 0) $buy .= "|" . substr($matches[0][0], 7);
		        
		        	$link->rent = $rent;
		            $link->buy = $buy;
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