<?php

require_once(LIB_PATH.DS."database.php");

class Favorite extends DatabaseObject {

	protected static $table_name = "favorites";
	protected static $db_fields = array('user_id', 'movie_id');

	public $user_id;
	public $movie_id;

	public static function find_for_user($user_id = 0) {
		global $database;
		
		$sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = {$user_id}";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? $result_array : false;
	}

	public static function find($user_id = 0, $movie_id = 0) {
		global $database;
		
		$sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = {$user_id} AND movie_id = {$movie_id} LIMIT 1";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	public function delete() {
		global $database;
		$sql = "DELETE FROM " . static::$table_name . " WHERE user_id=" . $database->escape_value($this->user_id) . 
		" AND movie_id=" . $database->escape_value($this->movie_id) . " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
}


?>