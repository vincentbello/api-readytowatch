<?php

require_once(LIB_PATH.DS."database.php");

class Alert extends DatabaseObject {

	protected static $table_name = "alerts";
	protected static $db_fields = array('id', 'user_id', 'any', 'itunes', 'amazon', 'netflix', 'youtube', 'crackle', 'google_play');

	public $user_id;
	public $id;
	public $any;
	public $itunes;
	public $amazon;
	public $netflix;
	public $youtube;
	public $crackle;
	public $google_play;

	public static function find_for_user($user_id = 0) {
		global $database;
		
		$sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = {$user_id}";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? $result_array : false;
	}

	public static function find_movies($user_id = 0) {
		global $database;

		$sql = "SELECT m.* FROM movies m INNER JOIN alerts a ON a.id = m.id WHERE a.user_id = {$user_id}";

		$movies = Movie::find_by_sql($sql);
		foreach($movies as $movie) {
			if ($alert = static::find($user_id, $movie->id)) {
				$movie->alert = $alert;
			}
		}

		return !empty($movies) ? $movies : false;

	}

	public static function find($user_id = 0, $id = 0) {
		global $database;
		
		$sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = {$user_id} AND id = {$id} LIMIT 1";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	public function delete() {
		global $database;
		$sql = "DELETE FROM " . static::$table_name . " WHERE user_id=" . $database->escape_value($this->user_id) . 
		" AND id=" . $database->escape_value($this->id) . " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
}


?>