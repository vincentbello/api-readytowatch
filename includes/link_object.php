<?php

require_once(LIB_PATH.DS."database.php");

class LinkObject extends DatabaseObject {

	public static function getData($endpoint) {
	    $session = curl_init($endpoint);
	    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	    $data = curl_exec($session);
	    curl_close($session);
	    $decoded = json_decode($data);
	    if ($decoded === NULL) die('Could not fetch data.');
	    return $decoded;
	}

	// generate "time ago" from timestamp
	public function gen_time() {
	    date_default_timezone_set("America/New_York");
	    $timenow = time();
	    $timebefore = strtotime($this->timestamp);
	    $sb = $timenow - $timebefore;
	    $response = "As of ";
	    if ($sb < 10) {
	        $response .= "just now";
	    } else if ($sb < 60) {
	        $response .= "$sb second" . (($sb > 1)?"s":"") . " ago";
	    } else if ($sb < 3600) {
	        $response .= floor($sb/60) . " minute" . ((floor($sb/60) > 1)?"s":"") . " ago";
	    } else if ($sb < 86400) {
	        $response .= floor($sb/3600) . " hour" . ((floor($sb/3600) > 1)?"s":"") . " ago";
	    } else if ($sb < 129600) {
	        $response .= "yesterday";
	    } else {
	        $response .= floor($sb/(60*60*24)) . " days ago";
	    }
	    return $response;
	}

	public function display_link() {

		return $this->link;
	}

	public static function get_table_name() {
		return static::$table_name;
	}

	public function is_rentable() {

		return $this->has_attribute("rent");
	}

	public function has_link() {
		return (strlen($this->link) > 0) ? true : false;
	}

	public function export() {
		$arr = $this->attributes();
		$arr["type"] = static::$table_name;
		$arr["link"] = $this->display_link();
		return $arr;
	}

	public static function link_exists($movieId) {
		$linkObj = static::find_by_id($movieId);
		return ($linkObj) ? (($linkObj->has_link()) ? true : false) : false;
	}


}

?>