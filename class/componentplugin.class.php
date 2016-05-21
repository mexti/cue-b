<?php
/**************************************************************************************************************
 Class ComponentPlugin
			The ComponentPlugin class extends the Plugin class. This plugin is part of the system and allows
			preprocessing of components on the page before the output is actually displayed.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class ComponentPlugin extends Plugin {
	protected $plugin = "Component";
	
	public function display($html="") {
		global $_cue_b,$_article;
		$nodes = self::findTags($html,$_cue_b->include,$this->plugin);
		foreach($nodes as $node) {
			$class = $node[strtolower($this->plugin)];
			$object = new $class(isset($node['name']) ? $node['name'] : (isset($node['id']) ? $node['id'] : 0));		// TODO: How can I check if the class was loaded
			$html = preg_replace($node['node'],$object->process(),$html);
		}
		echo $html;
	}
}
?>