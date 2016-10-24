<?php

$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');

$objs = db_tbl_server::find_objs_all_auth_y_ip();

$label_css = array(
	constant('RET_NOT_OK') => 'label-warning',
	constant('RET_UNKNOWN') => 'label-warning',
	constant('RET_OK') => 'label-success',
);

$label_name = array(
	constant('RET_NOT_OK') => 'not ok',
	constant('RET_UNKNOWN') => 'unknown',
	constant('RET_OK') => 'ok',
);

$counters = array();
$counters[constant('RET_NOT_OK')] = 0;
$counters[constant('RET_UNKNOWN')] = 0;
$counters[constant('RET_OK')] = 0;

$types = array();
$types[constant('RET_NOT_OK')] = array();
$types[constant('RET_UNKNOWN')] = array();
$types[constant('RET_OK')] = array();

$health_updated_newest = $health_updated_oldest = '';

if (isset($objs)) {
	foreach ($objs as $obj) {
		if ($obj->health_ret == constant('RET_NOT_OK') || $obj->health_ret == constant('RET_UNKNOWN') || $obj->health_ret == constant('RET_OK')) {
			$health_updated = strtotime($obj->health_updated);
			
			if ($health_updated_newest == '') {
				$health_updated_newest = $health_updated;
			}
			if ($health_updated_oldest == '') {
				$health_updated_oldest = $health_updated;
			}
			
			if ($health_updated_newest < $health_updated) {
				$health_updated_newest = $health_updated;
			}
			
			if ($health_updated_oldest > $health_updated) {
				$health_updated_oldest = $health_updated;
			}
			
			$counters[$obj->health_ret]++;
			$types[$obj->health_ret][] = $obj;
		}
	}
}

$display = isset($_REQUEST['display']) ? $_REQUEST['display'] : 'RET_NOT_OK,RET_UNKNOWN';
$display_arr = explode(',', $display);

$objs = array();

if (in_array('RET_NOT_OK', $display_arr)) {
	foreach ($types[constant('RET_NOT_OK')] as $obj) {
		$objs[] = $obj;
	}
}

if (in_array('RET_UNKNOWN', $display_arr)) {
	foreach ($types[constant('RET_UNKNOWN')] as $obj) {
		$objs[] = $obj;
	}
}

if (in_array('RET_OK', $display_arr)) {
	foreach ($types[constant('RET_OK')] as $obj) {
		$objs[] = $obj;
	}
}

require($cwd . '/includes/html_header.php');
?>

<h2>Server Health</h2>

<h4>
	<span class="label label-info">ALL <?php echo $counters[constant('RET_NOT_OK')] + $counters[constant('RET_UNKNOWN')] + $counters[constant('RET_OK')]; ?></span>
	<?php if ($counters[constant('RET_NOT_OK')] != 0) { ?>
	<span class="label label-warning">NOT OK <?php echo $counters[constant('RET_NOT_OK')]; ?></span>
	<?php } ?>
	<?php if ($counters[constant('RET_UNKNOWN')] != 0) { ?>
	<span class="label label-warning">UNKNOWN <?php echo $counters[constant('RET_UNKNOWN')]; ?></span>
	<?php } ?>
	<?php if ($counters[constant('RET_OK')] != 0) { ?>
	<span class="label label-success">OK <?php echo $counters[constant('RET_OK')]; ?></span>
	<?php } ?>
</h4>
<h4>
	<?php if ($health_updated_oldest != '') { ?>
	<span class="label <?php echo (time() - $health_updated_oldest < 1800) ? 'label-info' : 'label-warning'; ?>">OLDEST <?php echo  date('Y-m-d H:i:s', $health_updated_oldest); ?></span>
	<?php } ?>
	<?php if ($health_updated_newest != '') { ?>
	<span class="label label-info">NEWEST <?php echo  date('Y-m-d H:i:s', $health_updated_newest); ?></span>
	<?php } ?>
	<span class="label label-info">TIME <?php echo  date('Y-m-d H:i:s', time()); ?></span>
</h4>
<div>
<a class="btn btn-link" href="index_health.php" target="_blank">check all</a>
<a class="btn btn-link" href="index_logout.php" target="_blank">logout all</a>
</div>

<table class="table table-striped">
<thead>
<tr>
	<th>IP</th>
	<th>Health</th>
	<th>Output</th>
	<th>Updated</th>
	<th>Action</th>
</tr>
</thead>
<tbody>

<?php if (!empty($objs)) { ?>
<?php foreach ($objs as $obj) { ?>
<tr>
	<td><a href="server.php?ip=<?php echo htmlspecialchars($obj->ip); ?>" target="_blank" title="info"><?php echo htmlspecialchars($obj->ip); ?></span></td>
	<td><span class="label <?php echo $label_css[$obj->health_ret]; ?>"><?php echo $label_name[$obj->health_ret]; ?></span></td>
	<td><?php echo htmlspecialchars($obj->health_out); ?></td>
	<td><?php echo htmlspecialchars($obj->health_updated); ?></td>
	<td>
	<a class="btn btn-xs btn-link" href="index_health.php?ip=<?php echo $obj->ip; ?>" target="_blank">check</a>
    <a class="btn btn-xs btn-link" href="index_logout.php?ip=<?php echo $obj->ip; ?>" target="_blank">logout</a>
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

<?php require($cwd . '/includes/html_footer.php'); ?>