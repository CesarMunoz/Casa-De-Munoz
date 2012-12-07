<?php
	
	$client_key = $_SERVER['4SQ_CLIENT_ID'];
	$uri = $_SERVER['4SQ_URI'];

	// CODE METHOD - CANNOT BE DONE USING THE SHARED PHPFOG CLOUD SERVICE
	// READ NOTES IN SAVE_USER_TOKEN.PHP file
	//header( 'Location: https://foursquare.com/oauth2/authenticate?client_id='.$client_key.'&response_type=code&redirect_uri='.$uri ) ;

	// TOKEN METHOD
	header( 'Location: https://foursquare.com/oauth2/authenticate?client_id='.$client_key.'&response_type=token&redirect_uri='.$uri ) ;

?>