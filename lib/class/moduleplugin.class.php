<?php
/**************************************************************************************************************
 Class ModulePlugin
			The ModulePlugin class extends the Plugin class. This plugin is part of the system and allows
			preprocessing of the module on the page before the output is actually displayed.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class ModulePlugin extends Plugin {
	protected $plugin = "Module";
}
?>