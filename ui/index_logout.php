<?php

/* requires */

$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');
require($cwd . '/../lib/m_crawler.php');
require($cwd . '/../lib/m_cmd.php');

/* main */

header('X-Accel-Buffering: no');

$ip = isset($_REQUEST['ip']) ? $_REQUEST['ip'] : '';

printf("<pre>\n");

if ($ip == '') {
	printf("logout all\n");
} else {
	printf("logout %s\n", $ip);
}

logout($ip);

printf("</pre>\n");

/* functions */

function logout($ip)
{
	$objs = db_tbl_server::find_objs_all_auth_y_ip($ip);
	
	if (isset($objs)) {
		
		if (empty($objs)) {
			printf("empty\n");
		}
		
		foreach ($objs as $obj) {
			$m_cmd = new m_cmd();
			
			if ($m_cmd->get_server_info($obj->ip, 'health_')) {
				$result = $m_cmd->logout();
								
				printf("%s %s\n", $obj->ip, $result['out']);
			} else {
				printf("%s get_server_info failed\n", $obj->ip);
			}
			
			ob_flush();
			flush();
		}
		
		printf("completed\n");
	}
}