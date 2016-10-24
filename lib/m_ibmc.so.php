<?php

class m_ibmc extends m_crawler
{
	private $session_loaded;
	private $token;
	
	private function session_load()
	{
		if (!$this->session_loaded) {
			if (file_exists($this->cookiejar2)) {
				$content = file_get_contents($this->cookiejar2);
				if ($content !== false) {
					if (!empty($content)) {
						$this->session_loaded = true;
						$this->token = $content;
					}
				}
			}
		}
		
		return $this->session_loaded;
	}
	
	private function session_cleanup()
	{
		$this->session_loaded = false;
		$this->token = '';
	}
	
	private function session_reload()
	{
		$this->session_cleanup();
		
		return $this->session_load();
	}
	
	function __construct($ip, $s_id = 'default_', $protocol = 'https://', $port = '443')
	{
		parent::__construct($ip, $s_id, $protocol, $port);
				
		$this->write_log('huawei ibmc server');
	}
	
	public function ibmc_curl_with_token($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		$postfields = 'token=' . $this->token . $postfields;
		
		return $this->curl($path, $postfields, $headers, $filename);
	}
	
	public function ibmc_curl_without_token($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		return $this->curl($path, $postfields, $headers, $filename);
	}
	
	public function login($username, $password)
	{
		$data = array(
			'user_name' => $username,
			'check_pwd' => $password,
			'logtype' => '0',
			'func' => 'AddSession',
		);
		
		$path = '/bmc/php/processparameter.php';
		$postfields = http_build_query($data);
		$pattern = 'AddSession';
		
		if (($result = $this->ibmc_curl_without_token($path, $postfields)) !== false) {
			if ($this->get_token()) {	
				if ($this->session_reload()) {
					return true;
				}
			} else {
				$this->write_log('get_token failed');
			}
		}
		
		return false;
	}
	
	private function get_token()
	{
		$path = '/bmc/php/gettoken.php';
		$postfields = ' '; /* post */
		$headers = array();
		$filename = $this->cookiejar2;
		
		if (($result = $this->ibmc_curl_without_token($path, $postfields, $headers, $filename)) !== false) {
			if (!empty($result)) {
				return true;
			}
		}
		
		return false;
	}
	
	public function logout()
	{
		$path = '/bmc/php/processparameter.php';
		$postfields = '&func=DelSession';
		$pattern = 'DelSession';
		
		if ($this->session_load()) {
			if (($result = $this->ibmc_curl_with_token($path, $postfields)) !== false) {
				if (strpos($result, $pattern) !== false) {
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
		$path = '/bmc/php/getcurrentuser.php';
		$pattern = 'UserId';
		
		if ($this->session_load()) {
			if (($result = $this->ibmc_curl_with_token($path)) !== false) {
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
				if ($data['CriticalAlarmNum'] == 0 && $data['MajorAlarmNum'] == 0) {
					$ret = 0;
				}
				$out = 'CriticalAlarmNum: ';
				$out .= trim($data['CriticalAlarmNum']);
				$out .= ', MajorAlarmNum: ';
				$out .= trim($data['MajorAlarmNum']);
				$out .= ', MinorAlarmNum: ';
				$out .= trim($data['MinorAlarmNum']);
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
		$path = '/bmc/php/getmultiproperty.php';
		$postfields = "&str_input=[{\"class_name\":\"Warning\",\"obj_name\":\"Warning\",\"property_list\":[\"CriticalAlarmNum\",\"MajorAlarmNum\",\"MinorAlarmNum\"]},{\"class_name\":\"Led\",\"obj_name\":\"UIDLed\",\"property_list\":[\"State\"]}]";
		$pattern = 'Warning';
		
		if (($result = $this->ibmc_curl_with_token($path, $postfields)) !== false) {
			if (strpos($result, $pattern) !== false) {				
				$result = str_replace('%22', '"', $result); /* 192.168.225.145 */
				
				$data = array();
				
				$data['CriticalAlarmNum'] = util::get_string_between($result, '"CriticalAlarmNum": ', ',');
				$data['MajorAlarmNum'] = util::get_string_between($result, '"MajorAlarmNum": ', ',');
				$data['MinorAlarmNum'] = util::get_string_between($result, '"MinorAlarmNum": ', ',');
				
				if ($data['CriticalAlarmNum'] !== false) {
					if ($data['MajorAlarmNum'] !== false) {
						if ($data['MinorAlarmNum'] !== false) {
							return $data;
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
				
				$out = 'SystemName: ';
				$out .= trim($data['SystemName']);
				$out .= ', DeviceSerialNumber: ';
				$out .= trim($data['DeviceSerialNumber']);
				$this->write_log($out);
			} else {
				$out = 'get_data_health failed';
				$this->write_log($out);
			}
		} else {
			$this->write_log('session_load failed');
		}
		
		return $ret;
	}
	
	private function get_data_ext2()
	{
		$path = '/bmc/php/getmultiproperty.php';
		$postfields = "&str_input=[{ \"class_name\":\"BMC\", \"obj_name\":\"BMC\", \"property_list\":[\"SystemName\", \"PMEBuilt\", \"PMEVer\",\"FlashUnitNum\",\"DeviceGuid\",\"DeviceSerialNumber\"]}, { \"class_name\":\"Bios\", \"obj_name\":\"Bios\", \"property_list\":[\"Version\", \"UnitNum\", \"StartOption\"]}, { \"class_name\":\"EthGroup\", \"obj_name\":\"EthGroup0\", \"property_list\":[\"IpAddr\"]}]";
		$pattern = 'SystemName';
		
		if (($result = $this->ibmc_curl_with_token($path, $postfields)) !== false) {
			if (strpos($result, $pattern) !== false) {				
				$result = str_replace('%22', '"', $result); /* 192.168.225.145 */
				
				$data = array();
				
				$data['DeviceSerialNumber'] = util::get_string_between($result, '"DeviceSerialNumber": "', '",');
				$data['SystemName'] = util::get_string_between($result, '"SystemName": "', '",');
				
				if ($data['DeviceSerialNumber'] !== false) {
					if ($data['SystemName'] !== false) {
						return $data;
					}
				}
			}
		}
		
		return false;
	}
}