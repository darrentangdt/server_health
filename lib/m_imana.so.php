<?php

class m_imana extends m_crawler
{
	function __construct($ip, $s_id = 'default_', $protocol = 'https://', $port = '443')
	{
		parent::__construct($ip, $s_id, $protocol, $port);
				
		$this->write_log('huawei imana server');
	}
	
	public function imana_curl($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		return $this->curl($path, $postfields, $headers, $filename);
	}
	
	public function login($username, $password)
	{
		$data = array(
			'lang' => 'en',
			'UserName' => $username,
			'Password' => $password,
			'authenticateType' => '0',
			'domain' => '0',
		);
		
		$path = '/goform/Login';
		$postfields = http_build_query($data);
		$pattern = 'url=https';
				
		if (($result = $this->imana_curl($path, $postfields)) !== false) {
			if (strpos($result, $pattern) !== false) {
				return true;
			}
		}
		
		return false;
	}
	
	public function logout()
	{
		$path = '/goform/Logout';
		$pattern = '</HTML>';
		
		if (($result = $this->imana_curl($path)) !== false) {
			if (strpos($result, $pattern) !== false) {
				return true;
			}
		}
		
		return false;
	}
	
	public function is_logged_in()
	{
		$path = '/sysstatus.asp';
		$pattern = 'deviceStatus';
		
		$result = $this->imana_curl($path);
		
		if (($result = $this->imana_curl($path)) !== false) {
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
			if ($data[0] == 0 && $data[1] == 0) {
				$ret = 0;
			}
			$out = 'Critical: ';
			$out .= trim($data[0]);
			$out .= ', Major: ';
			$out .= trim($data[1]);
			$out .= ', Minor: ';
			$out .= trim($data[2]);
			$this->write_log($out);
		} else {
			$out = 'get_data_health failed';
			$this->write_log($out);
			$ret = 3;
		}
		
		return $ret;
	}
	
	private function get_data_health()
	{
		$path = '/getalartnum_top.asp';
		$pattern = ':';
		
		if (($result = $this->imana_curl($path)) !== false) {
			if (strpos($result, ':') !== false) {
				$data = explode(':', $result);
				if (count($data) == 4) {
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
			
			$out = 'Product Name: ';
			$out .= trim($data['Product Name']);
			$out .= ', Product Serial Number: ';
			$out .= trim($data['Product Serial Number']);
			$this->write_log($out);
		} else {
			$out = 'get_data_ext2 failed';
			$this->write_log($out);
		}
		
		return $ret;
	}
	
	private function get_data_ext2()
	{
		$path = '/syssummary.asp';
		$pattern = 'Product Serial Number';
		
		if (($result = $this->imana_curl($path)) !== false) {
			if (strpos($result, $pattern) !== false) {
				$data = array();
				
				$data['Product Serial Number'] = util::get_string_between($result, "Product Serial Number</td>\r\n    <td class=\"td_right\">", '</td>');
				$data['Product Name'] = util::get_string_between($result, "Product Name</td>\r\n    <td class=\"td_right\">", "</td>");
				
				if ($data['Product Serial Number'] !== false) {
					if ($data['Product Name'] !== false) {
						return $data;
					}
				}
			}
		}
		
		return false;
	}
}