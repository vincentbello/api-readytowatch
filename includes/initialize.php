<?php

// define the core paths
// define them as absolute paths to make sure that require_once works as expected

// DIRECTORY_SEPARATOR is a PHP predefined constant

defined('CONCATENATOR') ? null : define('CONCATENATOR', '|');
defined('LINK_UPDATE_DAYS') ? null : define('LINK_UPDATE_DAYS', 14); // 2 weeks
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'Applications'.DS.'MAMP'.DS.'htdocs'.DS.'api-readytowatch');
defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'includes');
defined('IMAGES_PATH') ? null : define('IMAGES_PATH', SITE_ROOT.DS.'public'.DS.'images');

defined('API_KEY') ? null : define('API_KEY', '09e83e9d105e24932aabefa1092f1123');

$LINK_CLASS_NAMES = array("Itunes", "Amazon", "Netflix", "YouTube", "GooglePlay", "Crackle");

require_once(LIB_PATH.DS."config.php");
require_once(LIB_PATH.DS."functions.php");

require_once(LIB_PATH.DS."database_object.php");
require_once(LIB_PATH.DS."session.php");
require_once(LIB_PATH.DS."memcache.php");
require_once(LIB_PATH.DS."database.php");

require_once(LIB_PATH.DS."link_object.php");
require_once(LIB_PATH.DS."itunes.php");
require_once(LIB_PATH.DS."amazon.php");
require_once(LIB_PATH.DS."netflix.php");
require_once(LIB_PATH.DS."youtube.php");
require_once(LIB_PATH.DS."google_play.php");
require_once(LIB_PATH.DS."crackle.php");

require_once(LIB_PATH.DS."user.php");

?>