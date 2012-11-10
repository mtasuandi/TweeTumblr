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
				
				#Send Post
				$post_type  = 'text';
				$post_title = 'Title Post By Tumblr API';
				$post_body  = 'Body Post By Tumblr API';

				// $success = $client->CallAPI(
					// 'http://api.tumblr.com/v2/blog/mtasuandi.tumblr.com/post',
					// 'POST', array('type' => $post_type, 'title' => $post_title,'body' => $post_body), array('FailOnAccessError'=>true), $post);
			}
		}
		$success 	= $client->Finalize($success);
		//$users		= $client->Finalize($users);
	}
	if($client->exit)
		exit;
?>
	<?php
	if($success){ ?>
	<div class="buttons">
		Tumblr Account: <? echo $user->response->user->name; ?>
	</div>
	<div class="buttons">
		<?php
		if(isset($_SESSION['twitter_otoken'])){
			//echo 'Twitter Token: '.$_SESSION['twitter_otoken'].'<br/>';
			//echo 'Twitter Token Secret: '.$_SESSION['twitter_otoken_secret'].'<br/>';
			echo 'Twitter Account: '.$_SESSION['twitter_username'].'<br/>';
		}else{
			?>
			<a href="?login&oauth_provider=twitter" class="button"><span class="icon icon197"></span><span class="label">Login To Twitter</span></a>
			<?php
		}
		?>
	</div>
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

	?>	
	<div class="buttons">
		<form action="post.php" method="POST" enctype="multipart/form-data">
		<textarea id="tweet" name="tweet" cols="50" rows="5" onfocus="setbg('#e5fff3');" onblur="setbg('white')"></textarea><br/>
		<div id="prop">
		<input type="file" name="image" /><p/>
		<input type="hidden" name="user_token" id="user_token" value="<? echo $user_token; ?>"/>
		<input type="hidden" name="user_secret" id="user_secret" value="<? echo $user_secret; ?>"/><br/>
		<input id="submit" type="submit" value="Post to Twitter and Tumblr" onClick="javascript:checkValue();"/>
		</div>
		</form>
	</div>
	
	<?php 
	}	
	?>
</div>
</body>
</html>
<? ob_flush(); ?>