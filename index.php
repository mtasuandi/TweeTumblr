<?php
session_start();
ob_start();
if (isset($_SESSION['OAUTH_ACCESS_TOKEN'])) {
    // Redirection to login page twitter or facebook
    header("location: LoginTumblr.php");
}
if (array_key_exists("login", $_GET)) {
    $oauth_provider = $_GET['oauth_provider'];
    if ($oauth_provider == 'tumblr') {
        header("Location: LoginTumblr.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<title>TweeTumb</title>
<link rel="shortcut icon" href="https://developers.google.com/_static/images/favicon.ico">
<link rel="stylesheet" href="../backend/css/css3-buttons.css" type="text/css" media="screen">
<link href="http://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Molengo" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../backend/css/style.css" type="text/css" media="screen">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

<style>
	a, a:active, a:visited { color: #607890; }
	a:hover { color: #036; }
		
	.buttons {
			background: #F1F1F1;
			padding: 11px;
			border: 1px solid #D2D2D2;
			width: 843px;
			margin-bottom: 20px;
		}
		
		body {
			font-family: 'PT Sans', Arial, Helvetica, sans-serif;
			text-align: center;
			background: url(../backend/images/texture.png) top center fixed;
			margin: 0;
		}
		
		#container {
			text-align: left;
			background: #FFF;
			width: 865px;
			margin: 20px auto;
			padding: 20px;
			border-left: 1px solid #CCC;
			border-right: 1px solid #CCC;
			
			-moz-box-shadow: 0px 0px 10px #BBB;
			-webkit-box-shadow: 0px 0px 10px #BBB;
			box-shadow: 0px 0px 10px #BBB;
		}
		
		h1, h2, h3, h4, h5 {
			font-family: Molengo, Arial, Helvetica, sans-serif;
			margin: 0 0 14px 0;
			padding: 0;
		}
		
		p {
			margin: 0 0 7px 0;
			padding: 0;
		}
</style>
</head><link rel="stylesheet" type="text/css" href="data:text/css,">
<body>
<div id="container">
	<div class="buttons">
		<h2>TweeTumb</h2>
	</div>
		
	<div class="buttons">
		<a href="?login&oauth_provider=tumblr" class="button"><span class="icon icon197"></span><span class="label">Login To Tumblr</span></a>
	</div>
</div>
<? ob_flush();?>
</body>
</html>