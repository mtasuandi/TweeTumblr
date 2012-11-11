<?php
session_start();
ob_start();
# login_with_tumblr.php
# @(#) $Id: login_with_tumblr.php,v 1.2 2012/10/05 09:22:40 mlemos Exp $
#Call Library OAuth
require('api/http.php');
require('api/oauth_client.php');
require('config/tumblr_config.php');
require('twitter/twitteroauth.php');
require('config/twitter_config.php');
if (array_key_exists("login", $_GET)) {
    $oauth_provider = $_GET['oauth_provider'];
    if ($oauth_provider == 'twitter') {
        header("Location: LoginTwitter.php");
    }
}
?>
<!DOCTYPE html">
<html>
<head>
<title>Login With Tumblr</title>
<link rel="shortcut icon" href="https://developers.google.com/_static/images/favicon.ico">
<link rel="stylesheet" href="../backend/css/css3-buttons.css" type="text/css" media="screen">
<link href="http://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Molengo" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../backend/css/style.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/table.css" type="text/css" media="screen">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="js/strlen.js"></script>
<script src="js/jquery.jqEasyCharCounter.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#tweet').jqEasyCounter({
			'maxChars': 140,
			'maxCharsWarning': 135
		});
	});
	</script>
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

#prop{
	margin: 5px;
}
</style>
</head><link rel="stylesheet" type="text/css" href="data:text/css,">
<body>
<div id="container">
<?php
	$client 				= new oauth_client_class;
	$client->debug 			= 1;
	$client->server 		= 'Tumblr';
	$client->redirect_uri 	= 'http://'.$_SERVER['HTTP_HOST'].dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/index.php';
	$application_line 		= __LINE__;
	$client->client_id 		= TUMBLR_CONSUMER_KEY;
	$client->client_secret 	= TUMBLR_CONSUMER_SECRET;

	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->access_token))
			{
				#Get Tumblr User Data
				$success = $client->CallAPI(
					'http://api.tumblr.com/v2/user/info', 
					'GET', array(), array('FailOnAccessError'=>true), $user);
			}
		}
		$success 	= $client->Finalize($success);
	}
	if($client->exit)
		exit;
?>
	<?php
	if($success){ 
		$tumblr_name 		= $user->response->user->name;
		$tumblr_url			= $user->response->user->blogs[0]->url;
		$tumblr_title		= $user->response->user->blogs[0]->title;
		$tumblr_following	= $user->response->user->following;
		$tumblr_followers	= $user->response->user->blogs[0]->followers;
		$tumblr_description	= $user->response->user->blogs[0]->description;
	?>
	
	<div class="buttons">
		<table border="0" cellpadding="0" cellspacing="0" class="vertical">
		<th colspan="2">Login Profile</th>
		<tr><td><img src="icon/tumblr.png"></img></td><td><? echo '<a href="http://'.$tumblr_name.'.tumblr.com" target="_blank">'.$tumblr_name.'</a>'; ?></td></tr>
		<? if(isset($_SESSION['twitter_otoken'])){
		echo '<tr><td><img src="icon/twitter.png"></img></td><td><a href="http://twitter.com/'.$_SESSION['twitter_username'].'" target="_blank">'.$_SESSION['twitter_username'].'</a></td></tr>';
		}
		?>
		</table>
	</div>
	
	
		<?php
		if(!isset($_SESSION['twitter_otoken'])){
			?>
			<div class="buttons">
			<a href="?login&oauth_provider=twitter" class="button"><span class="icon icon197"></span><span class="label">Connect To Twitter</span></a>
			</div>
			<?php
		}
		?>
	
	<script>
		function setbg(color)
		{
		document.getElementById("tweet").style.background=color
		}
		function checkValue(){
			var tweet = document.getElementById("tweet").value;
			if(tweet == ''){
				alert('Type something :9');
				return false;
			}
		}
	</script>
	<?php
	$user_token 	= $_SESSION['twitter_access_token']['oauth_token'];
	$user_secret 	= $_SESSION['twitter_access_token']['oauth_token_secret'];
	
	if(isset($_SESSION['twitter_username'])){
	?>
	<div class="buttons">
		<form action="post.php" method="POST" enctype="multipart/form-data">
		<div id="prop">
		<textarea id="tweet" name="tweet" cols="50" rows="5" onfocus="setbg('#white');" onblur="setbg('white')"></textarea>
		<input type="file" name="image" /><p/>
		<input type="hidden" name="user_token" id="user_token" value="<? echo $user_token; ?>"/>
		<input type="hidden" name="user_secret" id="user_secret" value="<? echo $user_secret; ?>"/>
		<input type="checkbox" name="posttotumblr" id="posttotumblr" value="PostToTumblr"/>&nbsp;Post To Tumblr<p/>
		<button class="action blue" id="login" name="login" onClick="checkValue();"><span class="label">Send</span></button>
		</div>
		</form>
	</div>
	
	<?php
	}
	}	
	?>
</div>
</body>
</html>
<? ob_flush(); ?>