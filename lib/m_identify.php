<?php

class m_identify
{
	public static function connect($ip, $port = 443, $timeout = 3)
	{
		$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
		
		$result = is_resource($fp);
		
		if ($fp !== false) {
			fclose($fp);
		}
		
		return $result;
	}

	public static function match($ip)
	{
		$so = 'unknown';
		
		if (self::connect($ip)) {
			$cfgs = self::get_cfgs();
			
			foreach ($cfgs as $so_tmp=>$cfg) {
				$crawler = new m_crawler($ip, 'identify_', $cfg['protocol'], $cfg['port']);
			
				$result = $crawler->curl($cfg['path']);
				
				if ($result !== false) {
					if (strpos($result, $cfg['pattern']) !== false) {
						$so = $so_tmp;
						break;
					}
				}
			}
		}
		
		return $so;
	}
	
	public static function get_cfgs()
	{
		$cfgs = array(
			/* hp ilo3 ilo4 */
			'ilo3'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'/json/login_session',
				'pattern'=>'server_name',
			),
			/* huawei ibmc */
			'ibmc'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'',
				'pattern'=>'iBMC',
			),
			/* huawei imana */
			'imana'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'/login.asp',
				'pattern'=>'iMana',
			),
			/* dell idrac7 idrac8 */
			'idrac7'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'/data?get=prodServerGen',
				'pattern'=>'prodServerGen',
			),
			/* ibm imm2 */
			'imm2'=>array(
			'protocol'=>'https://',
				'port'=>'443',
				'path'=>'/designs/imm/index.php',
				'pattern'=>'/designs/imm/login',
			),
			/* hp ilo2 */
			/*
			'ilo2'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'/login.htm',
				'pattern'=>'hp-iLO-Login',
			),
			*/
			/* huawei hmm */
			/*
			'hmm'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'/loginssl.html?lang=en',
				'pattern'=>'HMM',
			),
			*/
			/* dell cmc */
			/*
			'cmc'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'/cgi-bin/webcgi/login',
				'pattern'=>'CMC',
			),
			*/
			/* hp oa */
			/*
			'oa'=>array(
				'protocol'=>'https://',
				'port'=>'443',
				'path'=>'',
				'pattern'=>'hpoa',
			),
			*/
		);
		
		return $cfgs;
	}
}