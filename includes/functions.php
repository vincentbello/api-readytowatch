<?php

function redirect_to( $location = NULL) {
	if ($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}

function output_message($message = "") {
	if (!empty($message)) {
		return "<p class=\"message\">{$message}</p>";
	} else {
		return "";
	}
}

function __autoload($class_name) {
	$class_name = strtolower($class_name);
	$path = LIB_PATH.DS."{$class_name}.php";
	if (file_exists($path)) {
		require_once($path);
	} else {
		die("The file {$class_name}.php could not be found.");
	}
}

function include_layout_template($template = "", $args = array()) {
	$arguments = $args;
	include(SITE_ROOT.DS.'public'.DS.'templates'.DS.$template);
}

function datetime_to_text($datetime="") {
	$unixdatetime = strtotime($datetime);
	return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function days_from_now($timestamp = "") {
	$days = (time() - strtotime($timestamp))/86400;
	return $days;
}

function strallpos($p, $a, $offset=0, &$i=null) { 
	if ($offset > strlen($p)) trigger_error("strallpos(): Offset not contained in string.", E_USER_WARNING); 
	$match = array(); 
	for ($i=0; (($pos = strpos($p, $a, $offset)) !== false); $i++) { 
	  $match[] = $pos; 
	  $offset = $pos + strlen($a); 
	} 
	return $match; 
}

function gen_timestamp() {
	return date('Y-m-d H:i:s');
}

function to_url($str) {
	$str = str_replace("'", " ", $str);
	$patterns = array('/^([^a-z A-Z0-9\-]+)/', '/([^a-z A-Z0-9\-]+)$/', '/[^a-z A-Z0-9\-]+/');
	$replacements = array('', '', ' ');
	$str = preg_replace($patterns, $replacements, $str);
	return strtolower(str_replace(" ", "-", $str));
}

function get_random($array) {
	return $array[array_rand($array)];
}

// Helper method to get a string description for an HTTP status code
function getStatusCodeMessage($status)
{
    // these could be stored in a .ini file and loaded
    // via parse_ini_file()... however, this will suffice
    // for an example
    $codes = Array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );
 
    return (isset($codes[$status])) ? $codes[$status] : '';
}
 
// Helper method to send a HTTP response code/message
function sendResponse($status = 200, $body = '', $content_type = 'text/html')
{
    $status_header = 'HTTP/1.1 ' . $status . ' ' . getStatusCodeMessage($status);
    http_response_code($status);
    header('Content-type: ' . $content_type);
    echo $body;
}

function export_arr($arr) {
    $exp = array();
    foreach($arr as $obj) {
        $exp[] = $obj->export();
    }
    return !empty($exp) ? $exp : false;
}

?>