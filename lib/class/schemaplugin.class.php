<?php
/**************************************************************************************************************
 Class SchemaPlugin
			The SchemaPlugin class extends the Plugin class. This plugin replaces any cue-b:scope and
			cue-b:prop attributes with their corresponding schema.org attributes.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class SchemaPlugin extends Plugin {
	protected $plugin = "Schema";
	
	public function display($html="") {
		global $_cue_b;
		$html = preg_replace("`{$_cue_b->scope}=['\"]`sim","itemscope itemtype=\"http://schema.org/",$html);
		$html = preg_replace("`{$_cue_b->prop}`sim","itemprop",$html);
		echo $html;
	}
}
?>