<?php
/**************************************************************************************************************
 Class Menu
			The Menu class extends the Object class. This class holds the menus that can be displayed on the
			website. All menus have several menu options.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Menu extends Object {
	protected $table = "Menus";
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		$this->dump();
	}
}
?>