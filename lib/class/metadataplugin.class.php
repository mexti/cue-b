<?php
/**************************************************************************************************************
 Class MetadataPlugin
			The MetadataPlugin class extends the Plugin class. The class makes sure that added stylesheets
			and scripts are correctly added to the header section. The plugin also adds the correct title and
			metadata to the page.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class MetadataPlugin extends Plugin {
	protected $plugin = "Metadata";
	
	public function display($html="") {
		global $_template,$_content,$_cue_b,$_db;
		$html = preg_replace("`<{$_cue_b->title} />`sim",$_content->getTitle(),$html);	// Replace title
		if($icon=$_template->getProperty('image')) {
			$_db->select("`name`,`contenttype`")->from("`files`")->where("`id`='{$icon}'");
			$file = $_db->loadObject();
			if(!empty($file))
				$html = preg_replace("`(</title>)`sim","$1<link href=\"/image/{$file->name}\" rel=\"shortcut icon\" type=\"{$file->contenttype}\" />",$html);
		}
		$scriptAdd = "";
		$scripts = $_template->getScripts();
		foreach($_template->getScripts() as $script)
			$scriptAdd .= "<script src=\"lib/js/{$script}\" type=\"text/javascript\"></script>";
		if(!empty($scriptAdd))
			$html = preg_replace("`(</head>)`","{$scriptAdd}$1",$html);					// Add scripts just before the </head> tag
		echo $html;
	}
}
?>