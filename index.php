<?php
// Initialisation
if(isset($_GET['debug'])) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}
ob_flush();
define('Qb_Started',true);

// Autoload classes
function __autoload($class) {
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/lib/class/".strtolower($class).".class.php")) {
		require_once($_SERVER['DOCUMENT_ROOT']."/lib/class/".strtolower($class).".class.php");
	}
	class_exists($class) || trigger_error("Could not load class {$class} in ".__FUNCTION__,E_USER_ERROR);
}

// Load configuration
require_once($_SERVER['DOCUMENT_ROOT']."/.config.php");

// Start session
!isset($_config->sessionName) || session_name($_config->sessionName);
session_start();

// Start database connection
$_db = new Database($_config->database);
$_db->connect();
$_db->connected() || trigger_error("Could not connect to database from ".__FILE__,E_USER_ERROR);

// Get site settings
$settings = new Setting();
$settings->get();

// Load user details
empty($_SESSION['user']) || $_user = new User($_SESSION['user']->id);

function process_url($url) {
	global $_db;
	$pieces = explode('/',$url);
	$found = array();
	$lanfilter = $catfilter = "";
	$level = 0;
	//unset($pieces[0]);
	foreach($pieces as $piece) {
		$_db->select("`name`,`table`")->from("`components`")->where("`level`>'{$level}'");
		$objects = $_db->load();
		foreach($objects as $object) {
			$_db->select("`id`")->from("`{$object['table']}`")->where("`name`='{$piece}'{$lanfilter}{$catfilter}")->limit("1");
			$match = $_db->loadObject();
			if(!empty($match)) {
				$found["{$object['name']}:{$match->id}"] = array('level'=>$level,'object'=>$object['name'],'id'=>$match->id,'name'=>"{$piece}",'slug'=>"{\"object\":\"{$object['name']}\",\"id\":\"{$match->id}\"}");
				break;
			}
			$object['name'] = "not-found";
		}
		if($object['name']=='language') $lanfilter = " AND `{$object['name']}`='{$match->id}'";
		elseif($object['name']=='article') return $found;
		else $catfilter = " AND `parent`='{$match->id}'";
		$level++;
	}
	return $found;
}

// Check if user needs to log on
if(isset($_GET['logon'])) {
	$_SESSION['alerts'][] = (object)['type'=>'warning','error'=>'0','message'=>"This page requires you to log on. Please provide your account and password.",'fade'=>true];
	unset($_GET['url']);
	$_GET['component'] = "Article";														// TODO: This is now hard set, changes when languages come around
	$_GET['id'] = "2";
} elseif(isset($_GET['image']) && isset($_GET['name'])) {
	include($_SERVER['DOCUMENT_ROOT']."/lib/php/file.php");
	exit();
}

if(empty($_GET['url'])) {
	if(empty($_GET['id'])) {
		// Home page
	} else {
		if(isset($_GET['component'])) {
			$_content = new $_GET['component']($_GET['id']);
			if(empty($_content))
				trigger_error("Component cannot be fount at ".__FILE__,E_USER_ERROR);	// Something was wrong: this component does not exist
		} else {
			// Now what?
		}
	}
} else {
	$_breadcrumbs = process_url($_GET['url']);
}

/*
// Process query string
$_language = new Language(@$_GET['lang'],'1');																	// If no ?lang= is given, then default to the first defined (default) language
$_mainmenu = new Menu("`main` AND `language`='{$_language->getId()}'");											// Retrieve the main menu for the specified language
$_article = new Article("`home` AND `language`='{$_language->getId()}'");										// Retrieve the home page for the specified language
if(isset($_GET['menu'])) {
	$_menuoption = new MenuOption("`name`='{$_GET['menu']}' AND `language`='{$_language->getId()}'");
	if($_menuoption->exists() && isset($_GET['option'])) {
		$_article = new Article($_menuoption->article);															// Get the article that corresponds with the ?menu= parameter
		$_menusuboption = new MenuOption("`name`='{$_GET['option']}' AND `parent`='{$_menuoption->getId()}'");
		!$_menusuboption->exists() || $_article = new Article($_menuoption->article);							// Get the article that corresponds with the ?option parameter
	}
}
if(!$_article->exists()) {
	trigger_error("There must be something really wrong here at ".__FILE__,E_USER_ERROR);
}
*/

// Load template
$_template = new Template($_settings->template);
$html = $_template->process();

// Load available plugins
$plugins = Plugin::load();
foreach($plugins as $plugin) {
	$class = "{$plugin->name}Plugin";
	$_plugin = new $class($plugin->id);
	$html = $_plugin->process("screen",$html);
}

$_db->disconnect();

echo $html;
?>