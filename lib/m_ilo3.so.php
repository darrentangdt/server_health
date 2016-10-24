<?php

class m_ilo3 extends m_crawler
{
	private $session_loaded;
	private $session_key;
	
	private function session_load()
	{
		if (!$this->session_loaded) {
			if (file_exists($this->cookiejar2)) {
				$content = file_get_contents($this->cookiejar2);
				if ($content !== false) {
					$session_key = util::get_string_between($content, '"session_key":"', '","');
					if ($session_key !== false) {
						$this->session_loaded = true;
						$this->session_key = $session_key;
					}
				}
			}
		}
		
		return $this->session_loaded;
	}
	
	private function session_cleanup()
	{
		$this->session_loaded = false;
		$this->session_key = '';
	}
	
	private function session_reload()
	{
		$this->session_cleanup();
		
		return $this->session_load();
	}
	
	function __construct($ip, $s_id = 'default_', $protocol = 'https://', $port = '443')
	{
		parent::__construct($ip, $s_id, $protocol, $port);
				
		$this->write_log('hp ilo3/4 server');
	}
	
	public function ilo3_curl($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		return $this->curl($path, $postfields, $headers, $filename);
	}
	
	public function login($username, $password)
	{
		$post = array(
			'method' => 'login',
			'user_login' => $username,
			'password' => $password,
		);
		
		$path = '/json/login_session';
		$postfields = json_encode($post);
		$headers = array();
		$filename = $this->cookiejar2;
		$pattern = 'session_key';
				
		if (($result = $this->ilo3_curl($path, $postfields, $headers, $filename)) !== false) {
			if (strpos($result, $pattern) !== false) {
				if ($this->session_reload()) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function logout()
	{
		if ($this->session_load()) {
			$post = array(
				'method'=>'logout',
				'session_key'=>$this->session_key,
			);
			
			$path = '/json/login_session';
			$postfields = json_encode($post);
						
			if (($result = $this->ilo3_curl($path, $postfields)) !== false) {
				if ($result === '') {
					$this->session_cleanup();
					return true;
				}
			}
		} else {
			$this->write_log('session_load failed');
		}
		
		return false;
	}
	
	public function is_logged_in()
	{
		$path = '/json/session_info';
		$pattern = 'user_name';
		
		if (($result = $this->ilo3_curl($path)) !== false) {
			if (strpos($result, $pattern) !== false) {
				return true;
			}
		}
		
		return false;
	}
	
	public function health()
	{
		$ret = 1;
		
		if (($data = $this->get_data_health()) !== false) {
			if ($data['system_health'] == 'OP_STATUS_OK') {
				$ret = 0;
			}
			$out = 'system_health: ';
			$out .= trim($data['system_health']);
			$this->write_log($out);
		} else {
			$out = 'get_data_health failed';
			$this->write_log($out);
			$ret = 3;
		}
		
		return $ret;
	}
	
	public function get_data_health()
	{
		$path = '/json/overview';
		
		if (($result = $this->ilo3_curl($path)) !== false) {
			if (($data = util::json_decode_safe($result)) !== false) {
				if (isset($data['system_health'])) {
					return $data;
				}
			}
		}
		
		return false;
	}
	
	public function ext2()
	{
		$ret = 1;
		
		if (($data = $this->get_data_ext2()) !== false) {
			$ret = 0;
			
			$out = 'product_name: ';
			$out .= trim($data['product_name']);
			$out .= ', serial_num: ';
			$out .= trim($data['serial_num']);
			$this->write_log($out);
		} else {
			$out = 'get_data_ext2 failed';
			$this->write_log($out);
		}
		
		return $ret;
	}
	
	public function get_data_ext2()
	{
		$path = '/json/overview';
		
		if (($result = $this->ilo3_curl($path)) !== false) {
			if (($data = util::json_decode_safe($result)) !== false) {
				if (isset($data['product_name'])) { 
					if (isset($data['serial_num'])) {
						return $data;
					}
				}
			}
		}
		
		return false;
	}
}