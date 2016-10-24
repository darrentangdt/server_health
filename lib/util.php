<?php

class util
{
	public static function get_string_between($text, $start, $end)
	{
		if ($start == '') {
			$offset_start = 0;
		} else {
			$offset_start = strpos($text, $start);
		}
		
		if ($offset_start === false) {
			return false;
		}
		
		$offset_start += strlen($start);
		
		if ($end == '') {
			$offset_end = strlen($text);
		} else {
			$offset_end = strpos($text, $end, $offset_start);
		}
		
		if ($offset_end === false) {
			return false;
		}
		
		$len = $offset_end - $offset_start;
		return substr($text, $offset_start, $len);
	}
	
	public static function json_decode_safe($text)
	{
		$data = json_decode($text, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			return false;
		}
		
		return $data;
	}
	
	public static function xml_decode_safe($text)
	{		
		if (($xml = simplexml_load_string($text)) === false) {
			return false;
		}
		
		$data = self::json_decode_safe(json_encode($xml));
		
		if ($data === false) {
			return false;
		}
		
		return $data;
	}
	
	public static function mkdir_p($filename)
	{
		$dir = dirname($filename);
		
		if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
			return false;
		}
		
		return true;
	}
	
	public static function match_ip_address($ip)
	{	
		$ds = explode('.', $ip);
		
		if (count($ds) != 4) {
			return false;
		}
		
		foreach ($ds as $d) {
			if (!is_numeric($d)) {
				return false;
			}
					
			if ($d < 0 || $d > 255) {
				return false;
			}
		}
		
		return true;
	}
	
	public static function readline($prompt = '')
	{
		echo $prompt;
		return rtrim(fgets(STDIN), "\r\n");
	}
}