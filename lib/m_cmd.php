<?php

class m_cmd
{	
	private $server;
	private $module;
	
	private function get_module_filename()
	{
		return dirname(__FILE__) . '/m_' . $this->server->so . '.so.php';
	}
	
	public function get_server_info($ip, $s_id = 'default_')
	{
		$result = false;
		
		$this->server = db_tbl_server::find_obj_by_id($ip);
		
		if (isset($this->server)) {
			if (file_exists($this->get_module_filename())) {
				require_once($this->get_module_filename());
				$class_name = 'm_' . $this->server->so;
				$this->module = new $class_name($ip, $s_id);
				
				$result = true;
			}
		}
		
		return $result;
	}
	
	private function login_safe()
	{
		$result = false;
		
		for ($i = 0; $i < 2; $i++) {
			if ($this->module->is_logged_in() === true) {
				$this->module->write_log('logged in');
				return true;
			}
		}
		
		$result = $this->module->login($this->server->username, $this->server->password);
		
		return $result;
	}
	
	public function login()
	{
		if ($this->login_safe()) {
			array_unshift($this->module->logs, 'login succeeded');
			$ret = 0;
		} else {
			array_unshift($this->module->logs, 'login failed');
			$ret = 1;
		}
		
		return array(
			'ret' => $ret,
			'out' => implode(', ', $this->module->logs),
		);
	}
	
	private function logout_safe()
	{
		if ($this->module->is_logged_in()) {
			if ($this->module->logout()) {
				return true;
			}
		} else {
			$this->module->write_log('is_logged_in is false');
		}
		
		return false;
	}
	
	public function logout()
	{
		if ($this->logout_safe()) {
			array_unshift($this->module->logs, 'logout succeeded');
			$ret = 0;
		} else {
			array_unshift($this->module->logs, 'logout failed');
			$ret = 1;
		}
		
		return array(
			'ret' => $ret,
			'out' => implode(', ', $this->module->logs),
		);
	}
	
	public function access($username, $password) 
	{		
		$this->module->logout();
		
		if ($this->module->login($username, $password)) {
			$ret = 0;
		} else {
			$this->module->write_log('login failed');
			$ret = 1;
		}
		
		$this->module->logout();
		
		return array(
			'ret' => $ret,
			'out' => implode(', ', $this->module->logs),
		);
	}
	
	private function health_safe()
	{
		$ret = 3;
		
		if ($this->login_safe()) {
			return $this->module->health();
		} else {
			$this->module->write_log('login failed');
		}
		
		return $ret;
	}
	
	public function health()
	{
		$ret = $this->health_safe();
		
		if ($ret == 0) {
			array_unshift($this->module->logs, 'OK');
		} else {
			array_unshift($this->module->logs, 'NOT OK');
		}
		
		return array(
			'ret' => $ret,
			'out' => implode(', ', $this->module->logs),
		);
	}
	
	private function ext2_safe()
	{
		$ret = 1;
		
		if ($this->login_safe()) {
			return $this->module->ext2();
		} else {
			$this->module->write_log('login failed');
		}
		
		return $ret;
	}
	
	public function ext2()
	{
		$ret = $this->ext2_safe();
		
		return array(
			'ret' => $ret,
			'out' => implode(', ', $this->module->logs),
		);
	}
}
