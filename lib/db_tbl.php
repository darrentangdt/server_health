<?php

class db_tbl
{
	protected $_data = array();

	public function __construct($row = array())
	{		
		foreach ($row as $k=>$v) {
			$this->{$k} = $v;
		}
	}

	public function __isset($name)
	{
		return isset($this->_data[$name]);
	}

	public function __unset($name)
	{
		unset($this->_data[$name]);
	}

	public function __set($name, $value)
	{

		$this->_set_data($name, $value);
	}

	public function __get($name)
	{

		return $this->_get_data($name);
	}

	private function _set_data($name, $value)
	{
		$this->_data[$name] = $value;
	}

	private function _get_data($name)
	{
		if (array_key_exists($name, $this->_data)) {
			return $this->_data[$name];
		} else {
			return null;
		}
	}
	
	private static function bind_value($stmt, $binds)
	{
		foreach ($binds as $bind_k=>$bind_v) {
			if (is_int($bind_v)) {
				$stmt->bindValue($bind_k, $bind_v, PDO::PARAM_INT);
			} else {
				$stmt->bindValue($bind_k, $bind_v);
			}
		}
	}
	
	public static function find($sql, $binds = array())
	{
		$objs = null;
		
		$stmt = db_conn::get()->prepare($sql);
		
		self::bind_value($stmt, $binds);
		
		if ($stmt->execute()) {
			$objs = array();
			
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$objs[] = new self($row);
			}
		}

		return $objs;
	}
	
	public static function find_one($sql, $binds = array())
	{
		$obj = null;
		
		$stmt = db_conn::get()->prepare($sql);
		
		self::bind_value($stmt, $binds);
		
		if ($stmt->execute()) {			
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$obj = new self($row);
			}
		}

		return $obj;
	}
	
	public static function execute($sql, $binds = array())
	{
		$count = -1;
		
		$stmt = db_conn::get()->prepare($sql);
		
		self::bind_value($stmt, $binds);
		
		if ($stmt->execute()) {
			$count = $stmt->rowCount();
		}

		return $count;
	}
	
	public static function count($sql, $binds = array())
	{
		$count = -1;
		
		$stmt = db_conn::get()->prepare($sql);
		
		self::bind_value($stmt, $binds);
		
		if ($stmt->execute()) {
			$count = $stmt->fetchColumn();
		}

		return $count;
	}
	
	public static function save($data)
	{
		$count = -1;
		
		if (isset($data['sql_check']) && isset($data['binds_check']) && isset($data['sql_insert']) && isset($data['binds_insert']) && isset($data['sql_update']) && isset($data['binds_update'])) {
			$sql_check = $data['sql_check'];
			$binds_check = $data['binds_check'];
			
			$objs = self::find($sql_check, $binds_check);
			
			if (isset($objs) && count($objs) > 0) {
				$sql_save = $data['sql_update'];
				$binds_save = $data['binds_update'];
			} else {
				$sql_save = $data['sql_insert'];
				$binds_save = $data['binds_insert'];
			}
			
			$count = self::execute($sql_save, $binds_save);
		}
		
		return $count;
	}
}