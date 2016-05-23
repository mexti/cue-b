<?php
// Load configuration
require_once($_SERVER['DOCUMENT_ROOT']."/.config.php");

// Start session
!isset($_config->sessionName) || session_name($_config->sessionName);
session_start();

// Start the application
define('Qb_Started',true);

// Check if parameters are given
isset($_POST) || isset($_POST['username']) || isset($_POST['password']) || exit(json_encode((object)['success'=>'false','error'=>'4','message'=>'No valid input supplied','target'=>'']));

require_once($_SERVER['DOCUMENT_ROOT']."/lib/class/database.class.php");

$db = new Database($_config->database);
$db->connect();
$db->connected() || exit(json_encode((object)['success'=>'false','error'=>'3','message'=>'Could not connect to database','target'=>'']));
$db->select("`id`,`name`,`title`,`password`,`roles`")->from("`users`")->where("`name`='{$_POST['username']}' OR `email`='{$_POST['username']}'")->limit("1");
$user = $db->loadObject();

if(empty($user)) {
	echo(json_encode((object)['success'=>'false','error'=>'1','message'=>'Incorrect user name or password','target'=>'']));
} else {
	$salt = substr($user->password,0,29);
	if(crypt($_POST["password"],$salt)==$user->password) {
		$db->run("UPDATE `users` SET `lastloggedon`=NOW(),`logonattempts`=`logonattempts`+1,`succesfulattempts`=`succesfulattempts`+1 WHERE `id`='{$user->id}'");
		isset($_SESSION['alerts']) || $_SESSION['alerts'] = array();
		$_SESSION['alerts'][] = (object)['type'=>'success','error'=>'0','message'=>"Successfully logged on as <strong>{$user->name}</strong>",'fade'=>true];
		echo(json_encode((object)['success'=>'true','error'=>'0','message'=>"Successfully logged on as <strong>{$user->name}</strong>",'target'=>(isset($_SESSION['logonTarget']) ? $_SESSION['logonTarget'] : "/")]));
		unset($user->password);
		$_SESSION["user"] = $user;
		unset($_SESSION['logonTarget']);
	} else {
		$db->run("UPDATE `users` SET `logonattempts`=`logonattempts`+1,`failedattempts`=`failedattempts`+1 WHERE `id`='{$user->id}'");
		echo(json_encode((object)['success'=>'false','error'=>'2','message'=>'Incorrect user name','target'=>'']));
	}
}

$db->disconnect();
?>