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

$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';

header('X-Accel-Buffering: no');

printf("<pre>\n");
printf("authenticate %s %s\n", $username, $password);

auth($username, $password);

printf("completed\n");
printf("</pre>\n");

/* functions */

function auth($username, $password)
{
	$objs = db_tbl_server::find_objs_all_auth_n();
	
	if (isset($objs)) {
		
		if (empty($objs)) {
			printf("empty\n");
		}
		
		foreach ($objs as $obj) {
			$m_cmd = new m_cmd();
			if ($m_cmd->get_server_info($obj->ip, 'access_')) {
				$result = $m_cmd->access($username, $password);
				if ($result['ret'] == 0) {
					if (db_tbl_server::update_obj_auth_y($obj->ip, $username, $password) >= 0) {
						printf("%s save succeeded\n",  $obj->ip);
					} else {
						printf("%s save failed\n",  $obj->ip);
					}
				} else {
					printf("%s %s\n",  $obj->ip, $result['out']);
				}
			} else {
				printf("%s get_server_info failed\n", $obj->ip);
			}
			
			ob_flush();
			flush();
		}
	}
}