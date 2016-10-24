<?php

$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');

$ip = isset($_REQUEST['ip']) ? $_REQUEST['ip'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$result = '';

switch ($action) {
case 'reset':
	$obj = db_tbl_server::find_obj_by_id($ip);
	
	if (isset($obj)) {
		$row_count = db_tbl_server::update_obj_auth_n($ip);
		
		if ($row_count >= 0) {
			$result = 'reset succeeded';
		} else {
			$result = 'reset failed';
		}
	} else {
		$result = 'not found';
	}
	break;
	
case 'delete':
	$obj = db_tbl_server::find_obj_by_id($ip);
	
	if (isset($obj)) {
		$row_count = db_tbl_server::delete_obj_by_id($ip);
		
		if ($row_count >= 0) {
			$result = 'delete succeeded';
		} else {
			$result = 'delete failed';
		}
	} else {
		$result = 'not found';
	}
	break;
}

$objs = db_tbl_server::find_objs_all($ip);

require($cwd . '/includes/html_header.php');
?>

<h2>Server<?php if (!empty($ip)) { echo ' (' . $ip . ')'; } ?></h2>

<div>
<a class="btn btn-link" href="server_ext2.php" target="_blank">ext2 all</a>
</div>

<table class="table table-striped">
<thead>
<tr>
	<th>IP</th>
	<th>Ext1</th>
	<th>Ext2</th>
	<th>Identified</th>
	<th>Action</th>
</tr>
</thead>
<tbody>

<?php if (isset($objs) && !empty($objs)) { ?>
<?php foreach ($objs as $obj) { ?>
<tr>
	<td><?php echo htmlspecialchars($obj->ip); ?></td>
	<td><span class="editable" id="<?php echo htmlspecialchars($obj->ip); ?>"><?php echo htmlspecialchars($obj->ext1); ?></span></td>
	<td><?php echo htmlspecialchars($obj->ext2); ?></td>
	<td><?php echo $obj->identify_updated; ?></td>
	<td>
	<a class="btn btn-xs btn-link" href="server_ext2.php?ip=<?php echo $obj->ip; ?>"  target="_blank">ext2</a>
	<?php if ($obj->auth == constant('AUTH_Y')) { ?>
    <a class="btn btn-xs btn-link" href="?action=reset&ip=<?php echo $obj->ip; ?>" onclick="return confirm('reset?');" target="_blank">reset</a>
	<?php } ?>
	<a class="btn btn-xs btn-link" href="?action=delete&ip=<?php echo $obj->ip; ?>" onclick="return confirm('delete?');" target="_blank">delete</a>
    <a class="btn btn-xs btn-link" href="https://<?php echo $obj->ip; ?>" target="_blank">link</a>
	</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
	<td colspan="5">We didn't find any results</td>
</tr>
<?php } ?>

</tbody>
</table>

<?php if (!empty($result)) { ?>
<div class="alert alert-info"><?php echo $result; ?></div>
<?php } ?>

<script src="theme/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
$(function() {
	$(".editable").editable("server_ext1.php", { 
		indicator : "Saving...",
		tooltip   : "Click to edit...",
		style  : "inherit",
		onblur : "submit"
	});
});
</script>

<?php require($cwd . '/includes/html_footer.php'); ?>