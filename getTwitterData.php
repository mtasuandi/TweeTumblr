<?php

require("twitter/twitteroauth.php");
require 'config/twitter_config.php';
session_start();

if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
    
	#We've got everything we need
    $twitteroauth = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	
	#Let's request the access token
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
	
	#Save it in a session var
    // $_SESSION['access_token'] = $access_token;
	
	#Let's get the user's info
    $user_info = $twitteroauth->get('account/verify_credentials');
	
	#Check Data 
    if (isset($user_info->error)) {
        #Something's wrong, go back to square 1  
		header('Location: LoginTwitter.php');
    }else{
		session_start();
		$_SESSION['twitter_access_token']	= $access_token;
		$_SESSION['twitter_otoken']			= $_SESSION['oauth_token'];
		$_SESSION['twitter_otoken_secret']	= $_SESSION['oauth_token_secret'];
        $_SESSION['twitter_username']	 	= $user_info->screen_name;
        header("Location: LoginTumblr.php");
    }
} else {
    #Something's missing, go back to square 1
    header('Location: LoginTwitter.php');
}
?>
