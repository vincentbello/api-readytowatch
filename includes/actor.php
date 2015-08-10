<?php

require_once(LIB_PATH.DS."database.php");

class Actor extends DatabaseObject {

	protected static $table_name = "actors";
	protected static $db_fields = array('id', 'person_id', 'name', 'photo',
		'about', 'dob', 'dod', 'imdb_id', 'backdrop', 'backdrop_id', 'character');

	public $id;
	public $person_id;
	public $name;
	public $photo;
	public $about;
	public $dob;
	public $dod;
	public $imdb_id;
	public $backdrop = DEFAULT_BACKDROP_IMAGE;
	public $backdrop_id;

	public $character = "";

	public $label = "Actor";

	public static function find_by_id($id = 0) {
		global $database;
		$sql = "SELECT FROM " . self::$table_name . " a";
		$sql .= " INNER JOIN person_type p ON a.person_id = p.person_id";
		$sql .= " WHERE id=" . $database->escape_value($id) . " LIMIT 1";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	public static function find_cast_for($movie_id) {
		global $database;
		$sql = "SELECT a.*, b.character FROM " . self::$table_name . " a";
		$sql .= " INNER JOIN roles b ON a.id = b.actor_id AND b.movie_id = {$movie_id}";
		$sql .= " ORDER BY b.star DESC";

		return self::find_by_sql($sql);		
	}

	public static function array_of_names($movie_id) {
		$cast = self::find_cast_for($movie_id);
		$list = array();
		foreach($cast as $actor) {
			$list[] = $actor->name;
		}
		return !empty($list) ? $list : false;
	}

	public static function list_of_names($movie_id) {
		$list = self::array_of_names($movie_id);
		return ($list) ? implode(CONCATENATOR, $list) : false;
	}

}


?>