<?php

class m_idrac7 extends m_crawler
{
	private $session_loaded;
	private $st1, $st2;
	
	private function session_load()
	{
		if (!$this->session_loaded) {
			if (file_exists($this->cookiejar2)) {
				$content = file_get_contents($this->cookiejar2);
				if ($content !== false) {
					$st1 = util::get_string_between($content, 'ST1=', ',ST2=');
					$st2 = util::get_string_between($content, 'ST2=', '</forwardUrl>');
					if ($st1 !== false && $st2 !== false) {
						$this->session_loaded = true;
						$this->st1 = $st1;
						$this->st2 = $st2;
					}
				}
			}
		}
		
		return $this->session_loaded;
	}
	
	private function session_cleanup()
	{
		$this->session_loaded = false;
		$this->st1 = '';
		$this->st2 = '';
	}
	
	private function session_reload()
	{
		$this->session_cleanup();
		
		return $this->session_load();
	}
	
	function __construct($ip, $s_id = 'default_', $protocol = 'https://', $port = '443')
	{
		parent::__construct($ip, $s_id, $protocol, $port);
				
		$this->write_log('dell idrac7/8 server');
	}
	
	public function idrac7_curl_with_st($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		$headers[] = "idracAutoRefresh: 1";
		$headers[] = "Cookie:tteriesIcon=status_ok; fansIcon=status_ok; intrusionIcon=status_ok; removableFlashMediaIcon=status_ok; temperaturesIcon=status_ok; voltagesIcon=status_ok; powerSuppliesIcon=status_ok; sysidledicon=ledIcon%%20grayLed; tokenvalue=" . $this->st1;
		$headers[] = "ST2: " . $this->st2;
		
		return $this->curl($path, $postfields, $headers, $filename);
	}
	
	public function idrac7_curl_without_st($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		$headers[] = "Cookie: tokenvalue=undefined; batteriesIcon=status_ok; fansIcon=status_ok; intrusionIcon=status_ok; removableFlashMediaIcon=status_ok; temperaturesIcon=status_ok; voltagesIcon=status_ok; powerSuppliesIcon=status_ok; sysidledicon=ledIcon%20grayLed; -http-session-=";
		
		return $this->curl($path, $postfields, $headers, $filename);
	}
	
	public function login($username, $password)
	{
		$data = array(
			'user' => $username,
			'password' => $password,
		);
		
		$path = '/data/login';
		$postfields = http_build_query($data);
		$headers = array();
		$filename = $this->cookiejar2;
		$pattern = 'ST1=';
		
		if (($result = $this->idrac7_curl_without_st($path, $postfields, $headers, $filename)) !== false) {
			if (strpos($result, $pattern) !== false) {
				if ($this->session_reload()) {
					return true;
				}
			}
		}
	}
	
	public function logout()
	{
		$path = '/data/logout';
		
		if ($this->session_load()) {
			if (($result = $this->idrac7_curl_with_st($path)) !== false) {
				$this->session_cleanup();
				return true;
			}
		} else {
			$this->write_log('session_load failed');
		}
		
		
		return false;
	}
	
	public function is_logged_in()
	{
		$path = '/data?get=sysDesc';
		$pattern = '<status>ok</status>';
		
		if ($this->session_load()) {
			if (($result = $this->idrac7_curl_with_st($path)) !== false) {
				if (strpos($result, $pattern) !== false) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function health()
	{
		$ret = 1;
		
		if ($this->session_load()) {
			if (($data = $this->get_data_health()) !== false) {
				if ($data['lcdVisibleErrCount'] == 0) {
					$ret = 0;
				}
				$out = 'lcdVisibleErrCount: ';
				$out .= trim($data['lcdVisibleErrCount']);
				$out .= ', lcdHiddenErrCount: ';
				$out .= trim($data['lcdHiddenErrCount']);
				$out .= ', lcdText: ';
				$out .= trim($data['lcdText']);
				$this->write_log($out);
			} else {
				$out = 'get_data_health failed';
				$this->write_log($out);
				$ret = 3;
			}
		} else {
			$this->write_log('session_load failed');
		}
		
		return $ret;
	}
	
	private function get_data_health()
	{
		$path = '/data?get=pwState,sysDesc,svcTag,hostEventStatus,lcdText,lcdChassisStatus,lcdEventStatus';
		$pattern = '<status>ok</status>';
		
		if (($result = $this->idrac7_curl_with_st($path)) !== false) {
			if (strpos($result, $pattern) !== false) {
				if (($data = util::xml_decode_safe($result)) !== false) {
					if (isset($data['lcdVisibleErrCount'])) {
						if (isset($data['lcdHiddenErrCount'])) {
							if (isset($data['lcdText'])) {
								return $data;
							}
						}
					}
				}
			}
		}
		
		return false;
	}
	
	public function ext2()
	{
		$ret = 1;
		
		if ($this->session_load()) {
			if (($data = $this->get_data_ext2()) !== false) {
				$ret = 0;
				
				$out = 'sysDesc: ';
				$out .= trim($data['sysDesc']);
				$out .= ', svcTag: ';
				$out .= trim($data['svcTag']);
				$this->write_log($out);
			} else {
				$out = 'get_data_ext2 failed';
				$this->write_log($out);
			}
		} else {
			$this->write_log('session_load failed');
		}
		
		return $ret;
	}
	
	private function get_data_ext2()
	{
		$path = '/data?get=pwState,sysDesc,svcTag,hostEventStatus,lcdText,lcdChassisStatus,lcdEventStatus';
		$pattern = '<status>ok</status>';
		
		if (($result = $this->idrac7_curl_with_st($path)) !== false) {
			if (strpos($result, $pattern) !== false) {
				if (($data = util::xml_decode_safe($result)) !== false) {
					if (isset($data['sysDesc'])) {
						if (isset($data['svcTag'])) {
							return $data;
						}
					}
				}
			}
		}
		
		return false;
	}
}