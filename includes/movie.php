<?php

require_once(LIB_PATH.DS."database.php");

class Movie extends DatabaseObject {

	protected static $table_name = "movies";
	protected static $db_fields = array('id', 'title', 'year', 'release_date',
		'runtime', 'genres', 'adult', 'synopsis', 'tagline', 'director',
		'img_link', 'language', 'mpaa', 'revenue', 'imdb_id', 'trailer',
		'popularity', 'backdrop');

	public $id;
	public $title;
	public $year;
	public $release_date;
	public $runtime;
	public $genres;
	public $adult;
	public $synopsis;
	public $tagline;
	public $director;
	public $img_link;
	public $language;
	public $mpaa;
	public $revenue;
	public $imdb_id;
	public $trailer;
	public $popularity = 0;
	public $backdrop = DEFAULT_BACKDROP_IMAGE;

	public $favorited = false;
	public $link;
	public $alert;

	public function cast() {
		return Actor::find_cast_for($this->id);
	}

	// public static function find_by_id($id = 0, $user_id = 0) {
	// 	global $database;

	// 	if ($user_id > 0) {
	// 		$sql = "SELECT *, IF(f.movie_id IS NULL, FALSE, TRUE) AS favorited ";
	// 		$sql .= "FROM movies m ";
	// 		$sql .= "LEFT JOIN favorites f ON (f.movie_id = m.id AND f.user_id = {$user_id}) ";
	// 		$sql .= "WHERE m.id = {$id} LIMIT 1";

	// 		$result_array = self::find_by_sql($sql);

	// 		return !empty($result_array) ? array_shift($result_array) : false;
	// 	} else {
	// 		return self::find_by_id($id);
	// 	}
	// }

	public static function find_complete_by_id($id = 0) {
		global $database;
		$sql = "SELECT * FROM " . self::$table_name . " INNER JOIN imdb ON movies.id=imdb.id";
		$sql .= " WHERE movies.id=" . $database->escape_value($id) . " LIMIT 1";
		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	public static function find_popular($link_class = "Itunes", $user_id = 0) {
		$sql = "SELECT l.* FROM " . $link_class::get_table_name() . " l";
		$sql .= " JOIN popular p ON p.id = l.id AND p.type = '" . $link_class::get_table_name() . "' LIMIT 18";
		$links = $link_class::find_by_sql($sql);

		$movies = array();
		foreach ($links as $link) {
			$movie = self::find_by_id($link->id);
			$movie->link = $link;
			if ($user_id > 0 && Favorite::find($user_id, $link->id)) {
				$movie->favorited = true;
			}
			$movies[] = $movie;
		}

		return !empty($movies) ? $movies : false;
	}

	public static function find_random_popular($link_class = "Itunes") {
		global $database;
		$link_class = $database->escape_value($link_class);
		$sql = "SELECT l.* FROM " . $link_class::get_table_name() . " l";
		$sql .= " JOIN popular p ON p.id = l.id AND p.type = '" . $link_class::get_table_name() . "' ORDER BY RAND() LIMIT 1";

		$links = $link_class::find_by_sql($sql);

		$movie = self::find_by_id($links[0]->id);
		$movie->link = $links[0];
		return ($movie) ? $movie : false;
	}

	public static function find_related($id = 0) {
		// $sql = "SELECT mvs.id, m.title, m.year, m.img_link";
		$sql = "SELECT m.*";
		$sql .=	" FROM movie_keywords mvs";
		$sql .=	" JOIN movie_keywords mvs2";
		$sql .=	" ON mvs2.id = $id";
		$sql .=	" AND mvs.keyword_id = mvs2.keyword_id";
		$sql .=	" JOIN movies m";
		$sql .=	" ON (m.id = mvs.id AND mvs.id != $id)";
		$sql .=	" GROUP BY mvs.id";
		$sql .=	" HAVING COUNT(*) >= 3";
		$sql .=	" ORDER BY COUNT(*) DESC";
		$sql .=	" LIMIT 20";

		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}

	public static function find_movies_known_for($actor_id = 0) {
		global $database;
		$sql = "SELECT m.* FROM movies m INNER JOIN roles r";
		$sql .= " ON r.movie_id = m.id WHERE r.actor_id = " . $database->escape_value($actor_id);
		$sql .= " ORDER BY m.popularity DESC, r.star DESC LIMIT 4";

		$movies = self::find_by_sql($sql);
		return !empty($movies) ? $movies : false;
	}

	public static function find_movies_acted($actor_id = 0) {
		global $database;
		$sql = "SELECT m.*, r.character FROM " . self::$table_name . " m INNER JOIN roles r";
		$sql .= " ON m.id = r.movie_id WHERE r.actor_id = " . $database->escape_value($id) . " ORDER BY m.year DESC";

		$movies = self::find_by_sql($sql);
		return !empty($movies) ? $movies : false;
	}

	public function gen_title() {
		return $this->title . " (" . $this->year . ") | readyto.watch";
	}

	public static function get_filmography($actor_id = 0) {
		global $database;
		$sql = "SELECT m.*, r.character
				FROM movies m
				INNER JOIN roles r
				ON m.id = r.movie_id
				WHERE r.actor_id = {$actor_id}";

		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}

	public static function get_favorites($user_id = 0) {
		global $database;
		$sql = "SELECT * FROM movies m 
				INNER JOIN favorites f 
				ON m.id=f.movie_id 
				WHERE f.user_id={$user_id}";

		$result_array = self::find_by_sql($sql);
		foreach($result_array as $r) {
			$r->favorited = true;
		}
		return !empty($result_array) ? $result_array : false;
	}

	public function director() {
		global $database;
		$sql = "SELECT * FROM actors WHERE id=" . $this->id . " LIMIT 1";
		$result_array = Actor::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	public function gen_image_link($width = 92) {
		if (isset($this->img_link)) {
			return "http://image.tmdb.org/t/p/w{$width}" . $this->img_link;
		} else {
			return "images/no_image_found.png";
		}
	}

	public function gen_backdrop_link($width = 780) {
		if (isset($this->backdrop)) {
			return "http://image.tmdb.org/t/p/w{$width}" . $this->backdrop;
		} else {
			return "images/no_image_found.png";
		}
	}

	public function gen_runtime() {
		if ($this->runtime < 60) {
			return $this->runtime . ' min';
		} else {
			$h = floor($this->runtime/60);
			$m = $this->runtime % 60;
			return $h . 'h' . (($m < 10) ? '0'.$m : $m);
		}
	}

	public function gen_genres() {
		$genreArr = explode(' | ', $this->genres);
		foreach($genreArr as &$g) {
			$g = "<a class='genre-link' href='genre/" . urlencode(strtolower($g)) . "'>$g</a>";
		}
		return implode(' | ', $genreArr);
	}

	public function write_url() {
		return "/movie/" . $this->id . "/" . to_url($this->title);
	}



	public function export_simple() {
		return $this->attributes();
	}

	public function export() {
		$arr = $this->attributes();
		$arr["linkCount"] = $this->get_link_count();
		$arr["favorited"] = $this->favorited;
		$arr["alert"] = $this->alert;
		return $arr;
	}

	public function get_link_count() {
		global $LINK_CLASS_NAMES;
		$count = 0;
		foreach($LINK_CLASS_NAMES as $linkClass) {
			if ($linkClass::link_exists($this->id)) {
				$count++;
			}
		}
		return $count;
	}

	public static function export_links($id = 0) {
		global $LINK_CLASS_NAMES;
		$linkArray = array();
		foreach($LINK_CLASS_NAMES as $linkClass) {
			if ($link = $linkClass::find_by_id($id)) {
				$linkArray[] = $link->export();
			}
		}
		return $linkArray;
	}


}


?>