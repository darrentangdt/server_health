<?php

class m_crawler
{
	public $ip;
	public $protocol;
	public $port;
	public $s_id;
	
	public $cookiejar;
	public $cookiejar2;
	
	public $logs;
	
	public $http_code;
	
	function __construct($ip, $s_id = 'default_', $protocol = 'https://', $port = '443')
	{
		$this->ip = $ip;
		$this->s_id = $s_id;
		$this->protocol = $protocol;
		$this->port = $port;
		
		$this->cookiejar = dirname(__FILE__) . '/../var/' . $this->s_id . $this->ip;
		$this->cookiejar2 = $this->cookiejar . '.txt';
		
		$this->http_code = 0;
		$this->logs = array();
	}
	
	public function write_log($str)
	{
		if ($str != '') {
			array_push($this->logs, $str);
		}
	}

	public function curl_perform($options)
	{
		$result = false;
		
		if (($ch = curl_init()) !== false) {
			if (curl_setopt_array($ch, $options) !== false) {
				$result = curl_exec($ch);
				
				if (($this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) !== 200) {
					// $this->write_log('http code is ' . $this->http_code);
				}
				
				if (($error = curl_error($ch)) != '') {
					$this->write_log($error);
				}
			} else {
				$this->write_log('curl_setopt failed');
			}
			
			curl_close($ch);
		} else {
			$this->write_log('curl_init failed');
		}
		
		return $result;
	}
	
	public function curl($path = '', $postfields = '', $headers = array(), $filename = '')
	{
		$this->http_code = 0;
		
		$url = $this->protocol . $this->ip . ':' . $this->port . $path;
		$agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0';
		
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HEADER => false,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_VERBOSE => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 3,
			CURLOPT_USERAGENT => $agent,
			CURLOPT_COOKIEJAR => $this->cookiejar,
			CURLOPT_COOKIEFILE => $this->cookiejar,
		);
		
		if ($postfields != '') {
			$options[CURLOPT_POSTFIELDS] = $postfields;
		}
		
		if (($result = $this->curl_perform($options)) !== false) {
			if ($filename != '') {
				if (file_put_contents($filename, $result, LOCK_EX) === false) {
					$this->write_log('file_put_contents ' . $filename . ' failed');
					$result = false;
				}
			}
		}
		
		return $result;
	}
	
	public function get_http_code()
	{
		return $this->http_code;
	}
}
