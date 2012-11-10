<?php
session_start();

#Call Library OAuth
require('api/http.php');
require('api/oauth_client.php');
require('config/tumblr_config.php');
require('twitter/twitteroauth.php');
require('config/twitter_config.php');

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
	if($user){
		if(!empty ($_POST['tweet'])){

		$user_token 	= $_POST['user_token'];
		$user_secret	= $_POST['user_secret'];
		$twitteroauth = new TwitterOAuth(
					TWITTER_CONSUMER_KEY, 
					TWITTER_CONSUMER_SECRET, 
					$user_token, 
					$user_secret
				);
		$tweet = $_POST['tweet'];
		if($_FILES['image']['name'] != ""){
			
			# Update Status Without Media
			# https://api.twitter.com/1.1/statuses/update_with_media.json
			
			$image 	= "@{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}";
			$post 	= $twitteroauth->post('https://upload.twitter.com/1/statuses/update_with_media.json', 
										array('media[]'  => $image, 'status' => $tweet, 'multipart' => true));
			
		}else{
		
			# Update Status With Media
			# https://api.twitter.com/1.1/statuses/update.json
			# Send Tweet
			$post 	= $twitteroauth->post('https://api.twitter.com/1.1/statuses/update.json', array('status' => $tweet));
			
			#Send to Tumblr
			$post_type  = 'text';
			$post_title = 'TITLE '.$tweet;
			$post_body  = 'BODY '.$tweet;

			$success = $client->CallAPI(
				'http://api.tumblr.com/v2/blog/mtasuandi.tumblr.com/post',
				'POST', array('type' => $post_type, 'title' => $post_title,'body' => $post_body), array('FailOnAccessError'=>true), $post);
			$success 	= $client->Finalize($success);
			
			echo 'Success Gan';
			
		}
		}
	}
?>