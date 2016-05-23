<?php
/**************************************************************************************************************
 Class Language
			The Language class extends the Object class. This class holds the languages that are defined for
			the website. The language allows not only for translations of texts, but also allow for selection
			of different menus, articles, categories, etc.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Language extends Object {
	protected $table = "Languages";
	
	public function getMainMenu() {
		return $this->mainmenu;
	}
	
	public function getHome() {
		return json_decode($this->home);
	}
	
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