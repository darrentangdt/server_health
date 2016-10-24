<?php

class m_imm2 extends m_crawler
{
	function __construct($ip, $s_id = 'default_', $protocol = 'https://', $port = '443')
	{
		parent::__construct($ip, $s_id, $protocol, $port);
				
		$this->write_log('ibm imm2 server');
	}
	
	public function imm2_curl($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		return $this->curl($path, $postfields, $headers, $filename);
	}
	
	public function login($username, $password)
	{
		$post = array(
			'user' => $username,
			'password' => $password,
			'SessionTimeout' => 1200,
		);
		
		$path = '/data/login';
		$postfields = http_build_query($post);
		$pattern = '"forwardUrl":"index-console.php"';
		
		if (($result = $this->imm2_curl($path, $postfields)) !== false) {
			if (strpos($result, $pattern) !== false) {
				return true;
			}
		}
		
		return false;
	}
	
	public function logout()
	{
		$path = '/data/logout';
		$pattern = 'password';
		
		if (($result = $this->imm2_curl($path)) !== false) {
			if (strpos($result, $pattern) !== false) {
				return true;
			}
		}
		
		return false;
	}
	
	public function is_logged_in()
	{
		$path = '/designs/imm/dataproviders/imm_global.php';
		$pattern = 'events_critical';
		
		if (($result = $this->imm2_curl($path)) !== false) {
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
			if ($data['events_critical'] == 0 && $data['events_warning'] == 0) {
				$ret = 0;
			}
			$out = 'events_critical: ';
			$out .= trim($data['events_critical']);
			$out .= ', events_warning: ';
			$out .= trim($data['events_warning']);
			$this->write_log($out);
		} else {
			$ret = 3;
			$out = 'get_data_health failed';
			$this->write_log($out);
		}
		
		return $ret;
	}
	
	public function get_data_health()
	{
		$path = '/designs/imm/dataproviders/imm_global.php';
		
		if (($result = $this->imm2_curl($path)) !== false) {
			if (($data = util::json_decode_safe($result)) !== false) {
				if (isset($data['items'][0]['events_critical'])) { 
					if (isset($data['items'][0]['events_warning'])) {
						return $data['items'][0];
					}
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
			
			$out = 'machine_name: ';
			$out .= trim($data['machine_name']);
			$out .= ', serial_number: ';
			$out .= trim($data['serial_number']);
			$this->write_log($out);
		} else {
			$out = 'get_data_ext2 failed';
			$this->write_log($out);
		}
		
		return $ret;
	}
	
	public function get_data_ext2()
	{
		$path = '/designs/imm/dataproviders/imm_info.php';
		
		if (($result = $this->imm2_curl($path)) !== false) {
			if (($data = util::json_decode_safe($result)) !== false) {
				if (isset($data['items'][0]['machine_name'])) { 
					if (isset($data['items'][0]['serial_number'])) {
						return $data['items'][0];
					}
				}
			}
		}
		
		return false;
	}
}