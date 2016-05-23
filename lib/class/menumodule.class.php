<?php
/**************************************************************************************************************
 Class MenuModule
			The MenuModule class extends the Module class. This class is used to display the main menu of the
			page. Each menu option points to another content page that will be loaded upon click or tap.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class MenuModule extends Module {
	protected $module = "Menu";
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_breadcrumbs,$_content,$_language,$_mainmenu;
		if(empty($_breadcrumbs)) {
			// TODO: Do some interesting stuff here to retrieve the main menu, depending on the language
			$_mainmenu = new Menu(1);
		} else {
			$firstBreadcrumb = reset($_breadcrumbs);
			if($firstBreadcrumb['object']=="language") {
				$_language = new Language($firstBreadcrumb['id'],1);
				$_mainmenu = new Menu($_language->properties->mainmenu);
			} else {
				$_language = new Language(1);			// TODO: Probably need to make a better way to do this
				$_mainmenu = new Menu($_language->properties->mainmenu);
			}
		}
		$_mainmenu->display();
	}
}
?>