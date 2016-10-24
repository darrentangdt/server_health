<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
	<?php if ($_SERVER['SCRIPT_FILENAME'] == '/usr/local/server_health/ui/index.php') { ?>
    <meta http-equiv="refresh" content="60">
	<?php } ?>

    <title>Server Health</title>

    <!-- Bootstrap core CSS -->
    <link href="theme/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="theme/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme/theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="theme/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="theme/ie-emulation-modes-warning.js"></script>
	<script src="theme/jquery.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="theme/html5shiv.min.js"></script>
      <script src="theme/respond.min.js"></script>
    <![endif]-->
  </head>

  <body role="document">

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="index.php">Server Health</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
		    <li class="dropdown">
			  <a class="dropdown-toggle" aria-expanded="true" aria-haspopup="true" role="button" data-toggle="dropdown" href="#">
			    Health
			    <span class="caret"></span>
			  </a>
			  <ul class="dropdown-menu">
			    <li><a href="index.php?display=RET_NOT_OK,RET_UNKNOWN,RET_OK">ALL</a></li>
				<li><a href="index.php?display=RET_NOT_OK">NOT OK</a></li>
				<li><a href="index.php?display=RET_UNKNOWN">UNKNOWN</a></li>
				<li><a href="index.php?display=RET_OK">OK</a></li>
			  </ul>
			</li>
            <li><a href="discover.php">Discover</a></li>
			<li><a href="server.php">Server</a></li>
			<?php if (isset($_SESSION[constant('LOGIN')])) { ?>
			<li><a href="signin.php?action=logout" onclick="return confirm('confirm?');">Logout (<?php echo $_SESSION[constant('LOGIN')]; ?>)</a></li>
			<?php } ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <?php /* container start */ ?>

    <div class="container theme-showcase" role="main">

