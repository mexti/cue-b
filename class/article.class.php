<?php
/**************************************************************************************************************
 Class Article
			The Article class extends the Object class. This class contains the articles in the website,
			i.e. the main content.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Article extends Object {
	protected $table = "Articles";
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		var_dump($this);
	}
}
?>