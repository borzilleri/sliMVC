<?php defined('SYS_ROOT') OR die('Direct script access prohibited');?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		
		<title>Welcome to sliMVC!</title>
		
	</head>
	
	<body>
		<h1>Welcome to sliMVC</h1>
		<p>A slimmer MVC framework for PHP.</p>
		
		<p>You passed in the following parameters:<br />
			<ol>
			<? foreach($args as $a): ?>
				<li><tt><?=$a;?></tt></li>
			<? endforeach; ?>
			</ol>
		</p>
	</body>
</html>