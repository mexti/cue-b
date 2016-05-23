<?php
/**************************************************************************************************************
 Class Database
			The Database class allows access to a MySQL database in a structured and consistent way.
 **************************************************************************************************************/
defined('Qb_Started') || die("No use starting a class without an include");

class Database {
	protected $connection;
	private $server;
	private $user;
	private $password;
	private $name;
	public $log = array();
	public $error;
	public $count;
	
	public function __construct($options) {
		foreach($options as $option=>$value) {
			$this->$option=$value;
		}
		$this->connection = $this->error = null;
		$this->log[] = (object)['timestamp'=>time(),'type'=>'INFO','message'=>"Constructed new class"];
		$this->connect();
	}
	
	public function __destruct() {
		$this->disconnected() || $this->disconnect();
	}
	
	public function connect() {
		$this->connection = new mysqli($this->server,$this->user,$this->password,$this->name);
		if(mysqli_connect_errno()) {
			$this->error = (object)['id'=>mysqli_connect_errno(),'message'=>mysqli_connect_error()];
			$this->log[] = (object)['timestamp'=>time(),'type'=>'ERROR','message'=>"{$this->error['message']} (#{$this->error['id']})"];
		} else {
			$this->error = null;
			$this->log[] = (object)['timestamp'=>time(),'type'=>'INFO','message'=>"Established a connection to the database"];
		}
		return is_null($this->error);
	}
	
	public function connected() {
		return $this->connection;
	}
	
	public function disconnect() {
		$this->connection->close();
		$this->log[] = (object)['timestamp'=>time(),'type'=>'INFO','message'=>"Closed the connection"];
		return $this->connection = null;
	}
	
	public function disconnected() {
		return is_null($this->connection);
	}
	
	public function delete($fields) {
		$this->delete = $fields;
		return $this;
	}
	
	public function from($table) {
		$this->from = $table;
		return $this;
	}
	
	public function group($fields) {
		$this->group = $fields;
		return $this;
	}
	
	public function having($conditions) {
		$this->having = $conditions;
		return $this;
	}
	
	public function innerjoin($table) {
		$this->innerjoin = $table;
		return $this;
	}
	
	public function insert($table) {
		$this->method = "insert";
		$this->insert = $table;
		return $this;
	}
	
	public function join($table,$id) {
		$this->innerjoin = $table;
		$this->on = $id;
		return $this;
	}
	
	public function limit($num) {
		$this->limit = $num;
		return $this;
	}
	
	public function offset($num) {
		$this->offset = $num;
		return $this;
	}
	
	public function on($id) {
		$this->on = $id;
		return $this;
	}
	
	public function order($sort) {
		$this->order = $sort;
		return $this;
	}
	
	public function select($fields) {
		$this->method = 'select';
		$this->select = $fields;
		return $this;
	}
	
	public function update($table) {
		$this->method = 'update';
		$this->update = $table;
		return $this;
	}
	
	public function where($conditions) {
		$this->where = $conditions;
		return $this;
	}
	
	public function query() {
		switch($this->method) {
			case 'insert':
				$query = "INSERT INTO ".$this->quotename($this->insert);
				$query .= isset($this->columns) ? " (".self::quotename($this->columns).")" : "";
				$query .= isset($this->values) ? " VALUES (".self::quote($this->values).")" : "";
				break;
			case 'select':
				$query = "SELECT ".$this->quotename($this->select);
				$query .= isset($this->from) ? " FROM ".self::quotename($this->from) : "";
				$query .= isset($this->innerjoin) ? " INNER JOIN ".self::quotename($this->innerjoin) : "";
				$query .= isset($this->on) ? " ON ".self::quotename($this->on)."=".self::quotename($this->innerjoin).".`id`" : "";
				$query .= isset($this->where) ? " WHERE ".$this->where : "";
				$query .= isset($this->order) ? " ORDER BY ".$this->order : "";
				$query .= isset($this->group) ? " GROUP BY ".$this->group : "";
				$query .= isset($this->having) ? " HAVING ".$this->having : "";
				$query .= isset($this->limit) ? " LIMIT ".$this->limit : "";
				$query .= isset($this->offset) ? " OFFSET ".$this->offset : "";
				break;
			case 'update':
				$query = "UPDATE ".$this->quotename($this->update);
				$query .= isset($this->set) ? " SET ".self::comma($this->set) : "";
				$query .= isset($this->where) ? " WHERE ".$this->where : "";
				break;
			default:
				$query = "";
		}
		unset($this->insert);
		unset($this->columns);
		unset($this->values);
		unset($this->select);
		unset($this->from);
		unset($this->innerjoin);
		unset($this->on);
		unset($this->having);
		unset($this->where);
		unset($this->order);
		unset($this->group);
		unset($this->limit);
		unset($this->offset);
		unset($this->update);
		unset($this->set);
		return $query;
	}
	
	public function run($query="") {
		$resultset = $this->connection->query($query = empty($query) ? $this->query() : $query);
		$this->log[] = (object)['timestamp'=>time(),'type'=>'VERBOSE','message'=>$query];
		if($this->connection->errno) {
			$this->error = (object)['id'=>$this->connection->errno,'message'=>$this->connection->error];
			$this->log[] = (object)['timestamp'=>time(),'type'=>'ERROR','message'=>"{$this->error->message} (#{$this->error->id})"];
			!isset($_REQUEST['debug']) || var_dump($this->log);
		} else {
			$this->error = null;
			$this->log[] = (object)['timestamp'=>time(),'type'=>'INFO','message'=>"Query returned ".$this->connection->affected_rows." row(s)"];
			$this->count = $this->connection->affected_rows;
		}
		return $resultset;
	}
	
	public function load($resultset="") {
		if($resultset = empty($result) ? $this->run() : $resultset) {
			return $resultset->fetch_all(MYSQLI_ASSOC);
		} else {
			return array();
		}
	}
	
	public function loadObject($resultset="") {
		if($resultset = empty($result) ? $this->run() : $resultset) {
			return $resultset->fetch_object();
		} else {
			return (object)[];
		}
	}
	
	public function quotename($object) {
		$first = true;
		$result = "";
		if(is_array($object)) {
			foreach($object as $item) {
				$first ? $first=false : $result.=',';
				$result .= self::quotename($item);
			}
		} elseif(strpos($object,',')) {
			$array = explode(',',$object);
			foreach($array as $item) {
				$first ? $first=false : $result.=',';
				$result .= self::quotename($item);
			}
		} else {
			if(preg_match("/^`?([a-zA-Z_][a-zA-Z0-9_]*)`?$/",$object,$matches)) {
				$result = "`{$matches[1]}`";
			} else {
				$result = $object;
			}
		}
		return $result;
	}
	
	public function quote($object) {
		$first = true;
		$result = "";
		if(is_array($object)) {
			foreach($object as $item) {
				$first ? $first=false : $result.=',';
				$result .= self::quote($item);
			}
		} elseif(strpos($object,',')) {
			$array = explode(',',$object);
			foreach($object as $item) {
				$first ? $first=false : $result.=',';
				$result .= self::quote($item);
			}
		} else {
			$result = "'".$this->connection->real_escape_string($object)."'";
		}
		return $result;
	}
	
	public function comma($object) {
		if(is_array($object)) {
			return implode(',',$object);
		} else {
			return $object;
		}
	}
}
?>