<?php

$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');

$ip = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$ext1 = isset($_REQUEST['value']) ? $_REQUEST['value'] : '';

$obj = db_tbl_server::find_obj_by_id($ip);

if (isset($obj)) {
	$row_count = db_tbl_server::update_obj_ext1($ip, $ext1);

	if ($row_count >= 0) {
		echo $ext1;
	} else {
		echo $obj->ext1 . ', save failed';
	}
} else {
	echo $obj->ext1 . ', not found';
}