<?php
/**************************************************************************************************************
 Class MainContent
			The MainContent class extends the Object class. This class is used to display the main content
			of the page. This could be an article, a selected menu option, a category, or any other type of
			content.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class MainContent extends Object {
	public function __construct() {
		return null;
	}
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_breadcrumbs,$_content;
		if(empty($_breadcrumbs)) {
			if(empty($_content)) {
				// TODO: Do some interesting stuff here to get the home page, depending on the language
			} else {
				$_content->display();
			}
		} else {
			$lastBreadcrumb = end($_breadcrumbs);
			$_content = new $lastBreadcrumb['object']($lastBreadcrumb['id']);
			$_content->display();
		}
	}
}
?>