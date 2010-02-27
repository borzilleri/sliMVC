<?php defined('SYS_ROOT') OR die('Direct script access prohibited'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?php echo $error ?></title>
		<base href="http://php.net/" />
		<style type="text/css">
			#framework_error {
				width: 45em;
				margin: auto;
				padding: 10px 20px;
				background: #eee;
				border: 1px solid #bbb;
			}
			h1 {
				font-size: 20px;
				margin: 0;
			}
		</style>
	</head>
	<body>
		<div id="framework_error">
			<h1><?php echo htmlspecialchars($error) ?></h1>
			<p><?php echo $message ?></p>
		</div>
	</body>
</html>