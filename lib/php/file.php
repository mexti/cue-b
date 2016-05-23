<?php
// Initialisation
if(isset($_GET['debug'])) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

// Autoload classes
if(!function_exists('__autoload')) {
	function __autoload($class) {
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/lib/class/".strtolower($class).".class.php")) {
			require_once($_SERVER['DOCUMENT_ROOT']."/lib/class/".strtolower($class).".class.php");
		}
		class_exists($class) || trigger_error("Could not load class {$class} in ".__FUNCTION__,E_USER_ERROR);
	}
}

// Load configuration
require_once($_SERVER['DOCUMENT_ROOT']."/.config.php");

// Preset defaults
isset($_GET['id']) || isset($_GET['name']) || trigger_error("Could not get id or name ".__FILE__,E_USER_ERROR);

// Start database connection
$_db = new Database($_config->database);
$_db->connect();
$_db->connected() || trigger_error("Could not connect to database from ".__FILE__,E_USER_ERROR);
if(isset($_GET['id'])) {
	$_db->select("*")->from("`files`")->where("`id`='{$_GET['id']}'");
} else {
	$_db->select("*")->from("`files`")->where("`name`='{$_GET['name']}'")->limit("1");
}
$image = $_db->loadObject();
$_db->disconnect();

if(!empty($image)) {
	header("Content-Length: ".strlen($image->content));
	header("Content-type: {$image->contenttype}");
	!isset($_GET['download']) || header("Content-Disposition: attachment; filename=\"{$image->name}\"");
	echo($image->content);
}
?>