<?php
/**************************************************************************************************************
 Class Setting
			The Setting class loads all settings for a web site.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

$_settings = (object)[];

class Setting {
	private $db;
	
	// Constructor for content all classes
	public function __construct() {
	}
	
	// Get site settings and load into global variable
	public function get() {
		global $_config;
		global $_settings;
		$this->db = new Database($_config->database);
		$this->db->connect();
		$this->db->connected() || trigger_error("Could not connect to database from ".__FILE__,E_USER_ERROR);
		$this->db->select("*")->from("`settings`");
		$_settings = $this->db->loadObject();
		$this->db->disconnect();
	}
}
?>