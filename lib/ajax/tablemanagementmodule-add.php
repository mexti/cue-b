<?php
// Load configuration
require_once($_SERVER['DOCUMENT_ROOT']."/.config.php");

// Start session
!isset($_config->sessionName) || session_name($_config->sessionName);
session_start();

// Autoload class
if(!function_exists('__autoload')) {
	function __autoload($class) {
		require_once("../class/{$class}.class.php");
		if(!class_exists($class))
			trigger_error("Could not load class {$class}",E_USER_ERROR);
	}
}

$user = $_SESSION['user'];
$table = $_SESSION['table'];

// Start the application
define('Qb_Started',true);

$fieldset = $valueset = "";
foreach($_POST as $field=>$value) {
	if($field!='id') {
		if($value=='null') {
			$fieldset .= ",`{$field}`";
			$valueset .= ",NULL";
		} else {
			$fieldset .= ",`{$field}`";
			$valueset .= ",'{$value}'";
		}
	}
}

$db = new Database($_config->database);
$db->connect();
$db->run("INSERT INTO `{$table->name}` (`modified`,`modifiedby`{$fieldset}) VALUES (NOW(),'{$user->id}'{$valueset})");
$db->disconnect();

include("tablemanagementmodule-table.php");
?>