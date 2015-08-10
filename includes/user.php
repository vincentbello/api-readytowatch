<?php

require_once(LIB_PATH.DS."database.php");

class User extends DatabaseObject {

	protected static $table_name = "users";
	protected static $db_fields = array('id', 'username', 'password',
		'fb_id', 'fb_fname', 'fb_lname', 'email', 'image', 'adult',
		'amazon_prime', 'netflix', 'hash', 'resetHash', 'token', 'active');

	public $id;
	public $username = NULL;
	public $password = NULL;
	public $fb_id = NULL;
	public $fb_fname = NULL;
	public $fb_lname = NULL;
	public $email = NULL;
	public $image = DEFAULT_USER_IMAGE;
	public $adult = false;
	public $amazon_prime = true;
	public $netflix = true;
	public $hash;
	public $resetHash;
	public $token;
	public $active = false;

	public function full_name() {
		if (isset($this->fb_fname) && isset($this->fb_lname)) {
			return $this->fb_fname . " " . $this->fb_lname;
		} else {
			return "";
		}
	}

	public static function username_exists($username = "") {
		global $database;
		$username = $database->escape_value($username);

		$sql = "SELECT " . self::$db_fields[0] . " FROM " . self::$table_name . " WHERE username='{$username}'";
		$result_array = self::find_by_sql($sql);
		return !empty($result_array);
	}

	public static function authenticate($username = "", $password = "") {
		global $database;
		$username = $database->escape_value($username);
		$password = self::hash_password($database->escape_value($password));

		$sql = "SELECT * FROM " . self::$table_name . " WHERE username='{$username}' AND password='{$password}' AND active=1";
		$result_array = self::find_by_sql($sql);

		// session history

		return !empty($result_array) ? array_shift($result_array) : false;
	}

	public static function hash_password($password) {

		return hash("sha256", $password);
	}

	public function has_username() {
		return $this->username != NULL;
	}

	public function has_fb() {
		return $this->fb_id != NULL;
	}

	public function display_name() {
		return $this->has_fb() ? $this->fb_fname : $this->username;
	}

	public function truncate_display_name() {
		$name = $this->display_name();
		return strlen($name) > 15 ? substr($name, 0, 15) . "..." : $name;
	}
}

?>
