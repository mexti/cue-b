<?php
/**************************************************************************************************************
 Class Module
			The Module class extends the Object class. This class is used to process the content of the modules
			used on the pages, and insert it into the specified include tags.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Module extends Object {
	protected $table = "Modules";
	protected $module;
	
	// Load available modules
	static public function load() {
		global $_db;
		$_db->select("*")->from("`modules`")->where("`enabled`");
		$modules = $_db->load();
		foreach($modules as $ix=>$module) {
			$modules[$ix] = (object)$module;
		}
		return $modules;
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
		global $_cue_b; echo "Test";
		//$nodes = self::findTags($html,$_cue_b->include,$this->module);
		//foreach($nodes as $node) {
			//$class = $node[strtolower($this->module)].$this->module;
			
			//$object = new $class(isset($node['name']) ? $node['name'] : (isset($node['id']) ? $node['id'] : 0));		// TODO: How can I check if the class was loaded
			//$html = preg_replace($node['node'],$object->process(),$html);
		//}
		echo $html;
	}
}
?>