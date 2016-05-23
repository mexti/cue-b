<?php
/**************************************************************************************************************
 Class MenuOption
			The MenuOption class extends the Object class. This class holds the options and suboptions within
			the menu that can be displayed on the website. Options that do not have a parent are considered
			part of the top row. If they do have a parent, then these are suboptions.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class MenuOption extends Object {
	protected $table = "MenuOptions";
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_content;
		$object = json_decode($this->properties->slug);
		$_content = new $object->object($object->id);
		$_content->display();
	}
}
?>