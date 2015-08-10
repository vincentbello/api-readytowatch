<?php

// Database constants
defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER') ? null : define("DB_USER", "root");
defined('DB_PASS') ? null : define("DB_PASS", "root");
defined('DB_NAME') ? null : define("DB_NAME", "readytowatch");

// Other defaults
defined('DEFAULT_USER_IMAGE') ? null : define("DEFAULT_USER_IMAGE", IMAGES_PATH.DS."no_user_image.png");
defined('DEFAULT_ACTOR_IMAGE') ? null : define("DEFAULT_ACTOR_IMAGE", IMAGES_PATH.DS."no_image_found.png");
defined('DEFAULT_BACKDROP_IMAGE') ? null : define("DEFAULT_BACKDROP_IMAGE", IMAGES_PATH.DS."no_backdrop.png");

?>
