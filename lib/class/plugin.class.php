<?php
/**************************************************************************************************************
 Class Plugin
			The Plugin class extends the Object class. This class is used to load the available plugins,
			which process the generated output.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Plugin extends Object {
	protected $table = "Plugins";
	protected $plugin;
	
	// Load available plugins
	static public function load() {
		global $_db;
		$_db->select("*")->from("`plugins`")->where("`enabled`");
		$plugins = $_db->load();
		foreach($plugins as $ix=>$plugin) {
			$plugins[$ix] = (object)$plugin;
		}
		return $plugins;
	}
	
	// Find tags and return array of keys and values
	static protected function findTags($html,$tag,$property) {   																// TODO: Need to change this so that it reads all properties
		$result = array();
		preg_match_all("`<\s*{$tag}([^>]*)>`sim",$html,$tags);
		for($i=0;$i<count($tags[0]);$i++) {
			preg_match_all("`(\w+)=['\"]([^'\"]*)['\"]`iu",$tags[1][$i],$attributes);
			$properties = array();
			for($n=0;$n<count($attributes[0]);$n++) {
				$properties[strtolower($attributes[1][$n])] = $attributes[2][$n];
			}
			if(isset($properties[strtolower($property)])) {
				$properties['node'] = "`".$tags[0][$i]."`";
				$result[] = $properties;
			}
		}
		return $result;
	}
	
	public function printer($html="") {
		$this->display($html);
	}
	
	public function screen($html="") {
		$this->display($html);
	}
	
	public function display($html="") {
		global $_cue_b;
		$nodes = self::findTags($html,$_cue_b->include,$this->plugin);
		foreach($nodes as $node) {
			$class = $node[strtolower($this->plugin)].$this->plugin;
			$object = new $class(isset($node['name']) ? $node['name'] : (isset($node['id']) ? $node['id'] : 0));		// TODO: How can I check if the class was loaded
			$html = preg_replace($node['node'],$object->process(),$html);
		}
		echo $html;
	}
}
?>