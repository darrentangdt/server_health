<?php

$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');

$objs = db_tbl_server::find_objs_all_auth_n();
?>

<table class="table table-striped">
<thead>
<tr>
  <th>IP</th>
  <th>Type</th>
  <th>Identified</th>
</tr>
</thead>
<tbody>

<?php if (isset($objs) && !empty($objs)) { ?>
<?php foreach ($objs as $obj) { ?>
<tr>
  <td><?php echo htmlspecialchars($obj->ip); ?></td>
  <td><?php echo htmlspecialchars($obj->so); ?></td>
  <td><?php echo htmlspecialchars($obj->identify_updated); ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
  <td colspan="3">We didn't find any results</td>
</tr>
<?php } ?>

</tbody>
</table>
