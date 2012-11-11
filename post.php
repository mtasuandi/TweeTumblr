<?php
session_start();

#Call Library
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

		if(($users = $client->Initialize()))
		{
			if(($users = $client->Process()))
			{
				if(strlen($client->access_token))
				{
					#Get Tumblr User Data
					$users = $client->CallAPI(
						'http://api.tumblr.com/v2/user/info', 
						'GET', array(), array('FailOnAccessError'=>true), $user);
				}
			}
			$users 	= $client->Finalize($users);
		}
		
		#Check User Tumblr
		if($user){
		
		#Check If Tweet Not Empty
		if(!empty ($_POST['tweet'])){
			
			#Check User Twitter Token
			$user_token 	= $_POST['user_token'];
			$user_secret	= $_POST['user_secret'];
			$twitteroauth = new TwitterOAuth(
						TWITTER_CONSUMER_KEY, 
						TWITTER_CONSUMER_SECRET, 
						$user_token, 
						$user_secret
					);
		
		#Get Tweet Posted By User
		$tweet = $_POST['tweet'];
		
		#Check If Tweet With Image
		if($_FILES['image']['name'] != ""){
			
			# Update Status With Media
			# API Twitter v1.1 https://api.twitter.com/1.1/statuses/update_with_media.json
			$image 	= "@{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}";
			$post 	= $twitteroauth->upload('https://api.twitter.com/1.1/statuses/update_with_media.json', 
										array('media[]'  => $image, 'status' => $tweet));
			
			#Check If PostToTumblr Is Checked
			if($_POST['posttotumblr'] == 'PostToTumblr'){
				#Send to Tumblr
				$tum_img = $post->entities->media[0]->media_url;
				if($tum_img != ''){
					#Initialize
					if(($tum_pic = $client->Initialize())){
						#Process
						if(($tum_pic = $client->Process())){
							#Check User Token
							if(strlen($client->access_token)){
								#Call API
								$tum_pic = $client->CallAPI(
									'http://api.tumblr.com/v2/blog/mtasuandi.tumblr.com/post',
									'POST', array('type' => 'photo', 'source' => $tum_img), array('FailOnAccessError'=>true), $data_tum_pic);
							}
						}
						#Finish
						$tum_pic 	= $client->Finalize($tum_pic);
					}
				}
			}
			if($post){
				echo 'Tweet with media sent';
			}elseif($post && $data_tum_pic){
				echo 'Tweet sent and posted to Tumblr';
			}
		}else{
			# Update Status Without Media
			# API Twitter v1.1 https://api.twitter.com/1.1/statuses/update.json
			# Send Tweet
			$post 	= $twitteroauth->post('https://api.twitter.com/1.1/statuses/update.json', array('status' => $tweet));
			
			if($_POST['posttotumblr'] == 'PostToTumblr'){
				
				#Send to Tumblr
				$post_type  = 'text';
				$post_title = $tweet;
				$post_body  = $tweet;
				#Initialize
					if(($tum = $client->Initialize())){
						#Process
						if(($tum = $client->Process())){
							#Check User Token
							if(strlen($client->access_token)){
								#Call API
								$tum = $client->CallAPI(
									'http://api.tumblr.com/v2/blog/mtasuandi.tumblr.com/post',
									'POST', array('type' => $post_type, 'title' => $post_title,'body' => $post_body), array('FailOnAccessError'=>true), $data_tum);
							}
						}
						#Finish
						$tum 	= $client->Finalize($tum);
					}
			}
			if($post){
				echo 'Tweet sent';
			}elseif($post && $data_tum){
				echo 'Tweet sent and posted to Tumblr';
			}
		} #Check File
		} #Check Tweet
		} #Check Tumblr User
	
	#Exit From Tumblr
	if($client->exit)
	exit;
?>