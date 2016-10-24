<?php

$cwd = dirname(__FILE__);

require($cwd . '/../lib/const.php');
require($cwd . '/../lib/util.php');
require($cwd . '/../lib/db_conn.php');
require($cwd . '/../lib/db_tbl.php');
require($cwd . '/../lib/db_tbl_server.php');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == 'logout') {
	unset($_SESSION[constant('LOGIN')]);
}

$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';

$result = '';

if (isset($_POST['username'])) {
	$result = 'please enter the correct username and password';

	foreach ($signins as $signin) {
		if ($signin['username'] == $username && $signin['password'] == $password) {
			$_SESSION[constant('LOGIN')] = $signin['username'];
			header('Location: index.php');
			break;
		}
	}
}

require($cwd . '/includes/html_header.php');
?>

<link href="theme/signin.css" rel="stylesheet">


<form class="form-signin" method="post">
  <h2 class="form-signin-heading">Please sign in</h2>
  
  <?php if (!empty($result)) { ?>
  <div class="alert alert-info"><?php echo $result; ?></div>
  <?php } ?>
  
  <label for="username">Username</label>
  <input type="input" name="username" value="<?php echo $username; ?>" id="username" class="form-control" required autofocus>
  
  <label for="password">Password</label>
  <input type="password" name="password" value="<?php echo $password; ?>" id="password" class="form-control" required>
  
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>

<?php require($cwd . '/includes/html_footer.php'); ?>