<?php

/* requires */

$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');
require($cwd . '/../lib/m_crawler.php');
require($cwd . '/../lib/m_identify.php');

/* main */

$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '';
$end = isset($_REQUEST['end']) ? $_REQUEST['end'] : '';

header('X-Accel-Buffering: no');

printf("<pre>\n");
printf("identify %s %s\n", $start, $end);

if (util::match_ip_address($start) && util::match_ip_address($end)) {
	$start_l = ip2long($start);
	$end_l = ip2long($end);
	
	if ($start_l > $end_l) {
		$tmp_l = $start_l;
		$start_l = $end_l;
		$end_l = $tmp_l;
	}
	
	identify($start_l, $end_l);
	
	printf("completed\n");
} else {
	printf("invalid request\n");
}

printf("</pre>\n");

/* functions */

function identify($start_l, $end_l)
{
	for ($ip_l = $start_l; $ip_l <= $end_l; $ip_l++) {
		$ip = long2ip($ip_l);
		$so = m_identify::match($ip);
		
		if ($so != 'unknown') {
			if (tbl_server_save_obj_so($ip, $so) >= 0) {
				printf("%s is %s, save succeeded\n", $ip, $so);
			} else {
				printf("%s is %s, save failed\n", $ip, $so);
			}
		} else {
			printf("%s is %s, skipped\n", $ip, $so);
		}
		
		ob_flush();
		flush();
	}
}

function tbl_server_save_obj_so($ip, $so)
{
	$obj = db_tbl_server::find_obj_by_id($ip);
		
	if (isset($obj)) {
		if ($obj->so != $so) {
			$row_count = db_tbl_server::update_obj_identify($ip, $so);
		} else {
			$row_count = 0;
		}
	} else {
		$row_count = db_tbl_server::insert_obj_identify($ip, $so);
	}
	
	return $row_count;
}