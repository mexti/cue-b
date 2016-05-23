<?php
/**************************************************************************************************************
 Class Object
			The Object class is the main class for all components. This class contains definitions for the
			expected functions and offers default functions that enable the engine to retrieve and handle the
			data.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

abstract class Object {
	protected $id;
	protected $name;
	protected $table = "Objects";
	protected $properties;
	
	// Constructor for all components
	public function __construct($id=null,$default=null) {
		global $_db,$_config;
		!empty($_db) || $_db = new Database($_config->database);
		$this->id = null;														// No id yet
		$this->name = get_class($this);											// Preset name as class name
		$this->properties = null;												// No properties at first
		(empty($id) && empty($default)) || $this->get($id,$default);			// if id is given, assign it and get the properties
		return null;
	}
	
	// Find the object using one of three methods:
	//   1. Supplying the id
	//   2. Supplying the name
	//   3. Supplying a where clause
	// All three methods allow for an optional default value
	// On success, the function will retrieve the properties as well and returning these back
	public function get($id,$default=null) {
		global $_db,$_config;
		(!empty($_db) && $_db->connected()) || trigger_error("Could not connect to database for {$this->class} in ".__FUNCTION__,E_USER_ERROR);
		if(is_numeric($id)) {													// Id was given upon construct
			$this->id = $id;													// Store unique id for this component
			return $this->properties = $this->getProperties();					// Return the object properties
		} elseif(preg_match("/`/",$id)) {											// Try to find object by filter
			$_db->select("`id`")->from("`{$this->table}`")->where("{$id}")->limit("1");
			if($object = $_db->loadObject()) {
				$this->id = $object->id;										// If found, store id for this component
				return $this->properties = $this->getProperties();				// Return the object properties
			} else {
				if(empty($default)) {
					trigger_error("No {$class} found with matching where clause '{$id}'",E_USER_ERROR);
				} else {
					return $this->get($default);								// Not found, so return default value
				}
			}
		} else {																// Try to find object by name
			$_db->select("`id`")->from("`{$this->table}`")->where("`name`='{$id}'")->limit("1");
			if($object = $_db->loadObject()) {
				$this->id = $object->id;										// If found, store id for this component
				return $this->properties = $this->getProperties();				// Return the object properties
			} else {
				if(empty($default)) {
					trigger_error("No {$class} found with matching where clause '{$id}'",E_USER_ERROR);
				} else {
					return $this->get($default);								// Not found, so return default value
				}
			}
		}
	}
	
	// Get all the properties of the selected component
	private function getProperties() {
		global $_db;
		if(empty($this->id)) return null;										// If the id is not present, don't do anything
		(!empty($_db) && $_db->connected()) || trigger_error("Could not connect to database for {$this->class} in ".__FUNCTION__,E_USER_ERROR);
		$_db->select("*")->from($this->table)->where("`id`='{$this->id}'");
		return $this->properties = $_db->loadObject();							// Return the properties
	}
	
	// Get a property
	public function getProperty($property='id') {
		return $this->properties->$property;
	}
	
	// Get current component id
	public function getId() {
		return $this->getProperty('id');
	}
	
	// Get the name of current component
	public function getName() {
		return $this->getProperty('name');
	}
	
	// Get the name of current component
	public function getTitle() {
		return $this->getProperty('title');
	}
	
	// Return true if the object exists
	public function exists() {
		return !empty($this->id);
	}
	
	public function dump() {
		echo "</p style=\"red\">".var_dump($this)."</p>";
	}
	
	public function json() {
		echo "</p style=\"purple\">".var_dump($this)."</p>";
	}
	
	abstract public function printer($html="");
	
	abstract public function screen($html="");
	
	abstract public function display($html="");
	
	public function process($media="screen",$html="") {
		ob_start();
		$this->$media($html);
		return ob_get_clean();
	}
}
?>