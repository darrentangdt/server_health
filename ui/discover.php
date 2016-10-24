<?php
$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');

require($cwd . '/includes/html_header.php');
?>

<h2>Discover</h2>

<form action="discover_id.php" method="post" target="_blank" class="form-horizontal">
<fieldset>
<div class="form-group">
	<label class="col-sm-1 control-label">start</label>
	<div class="col-sm-2"><input type="text" class="form-control" id="start" name="start" value=""/></div>
	<label class="col-sm-1 control-label">end</label>
	<div class="col-sm-2"><input type="text" class="form-control" id="end" name="end" value=""/></div>
	<div class="col-sm-2"><input type="submit" class="form-control btn btn-sm btn-default" value="Identify" onclick="return confirm('confirm?');"/></div>
</div>
</fieldset>
</form>

<form action="discover_auth.php" method="post" target="_blank" class="form-horizontal">
<fieldset>
<div class="form-group">
	<label class="col-sm-1 control-label">username</label>
	<div class="col-sm-2"><input type="text" class="form-control" id="username" name="username" value=""/></div>
	<label class="col-sm-1 control-label">password</label>
	<div class="col-sm-2"><input type="text" class="form-control" id="password" name="password" value=""/></div>
	<div class="col-sm-2"><input type="submit" class="form-control btn btn-sm btn-default" value="Authenticate" onclick="return confirm('confirm?');"/></div>
</div>
</fieldset>
</form>

<div id="auth_n">loading data...</div>

<script type="text/javascript" charset="utf-8">
$(function() {
	$(function() {
		i = setInterval(runajax, 1000);
		
		function runajax() {
			$.ajax({ 
				type : "get",
				async : true,
				cache : false,
				url : "discover_auth_n.php"
			}).success(function(data) {
				$("#auth_n").html(data);
			}).error(function() {
				$("#auth_n").html("load data failed");
			});
		}
	});
	
	$("#start").blur(function(){
		if ($("#end").val() == "") {
			$("#end").val($("#start").val());
		}
	});
});
</script>

<?php require($cwd . '/includes/html_footer.php'); ?>
