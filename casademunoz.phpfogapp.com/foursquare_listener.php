<?php
	require_once("FoursquareAPI.class.php");
	require_once("GetUserToken.class.php");
	
	$client_key = $_SERVER['4SQ_CLIENT_ID'];
	$client_secret = $_SERVER['4SQ_CLIENT_SECRET'];
	$auth_token = $_SERVER['4SQ_AUTH_TOKEN'];  // CURRENTLY CASA MUNOZ' AUTH TOKEN
	$casa_de_munoz_venue_id = $_SERVER['4SQ_CASA_VENUE_ID'];

	// Load the Foursquare API library
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	$foursquare->SetAccessToken($auth_token);


	//this ^^ decodes the checkin POST json data and assigns it to a PHP array
	$json = json_decode($_REQUEST["checkin"]);
	$json_raw = $_REQUEST["checkin"];
	

	//interpert the php array from above, assigning each element from foursquare it's own variable. A sample push is shown at https://developer.foursquare.com/overview/realtime
	//<------------------------------------------------------- USER WHO CHECKED IN DATA -------------------------------------------------------
	$checkin_id = $json->id;
	$user_id = $json->user->id;

	//$user_id = '22800384'; // NINJA N
	//$user_id = '281175'; // ZENNIA
	//$user_id = '8974528'; // SCOTT
	//$user_id = '18966447'; // FAKEJOY ??????
	//$user_id = '8219350'; // AMANDA
	//$user_id = '10765442'; // RAYZA
	//$user_id = '1325573'; // MIKE MUNOZ
	//$user_id = '10803358'; // STEVEN MUNOZ
	//$user_id = '12854167'; // GQ
	//$user_id = '1'; // JIMMY FOURSQUARE
	//$user_id = '39633498'; // CESAR BBDO

	if(strlen($user_id) < 1){
		echo "$user_id length: ".strlen($user_id);
		$user_id = '7357331'; // CESAR MUNOZ
	}
	
	// User info from check-in push is NOT as detailed as a user info request which follows below
	//$user_firstName = $json->user->firstName;
	//$user_lastName = $json->user->lastName;  //note: foursquare push api test does NOT send last name for some bizzarre reason. a real push will.
	//$user_gender = $json->user->gender;
	//$user_homeCity = $json->user->homeCity;

	//$venue_id = $json->venue->id;
	//$venue_name = $json->venue->name;
	//$venue_location_address = $json->venue->location->address;
	//$venue_location_lat = $json->venue->location->lat;
	//$venue_location_lng = $json->venue->location->lng;
	//$venue_location_city = $json->venue->location->city;
	//$venue_location_state = $json->venue->location->state;
	//$venue_location_postalCode = $json->venue->location->postalCode;


	//<------------------------------------------------------- REQUEST FULL USER DATA -------------------------------------------------------
	$response = $foursquare->GetPrivate("users/".$user_id);
	$full_user_data = json_decode($response);
	//print_r($full_user_data);
	//echo "<br><br>full_user_data: <br><br>".$full_user_data;
	$firstName = $full_user_data->response->user->firstName;
	$twitter = $full_user_data->response->user->contact->twitter;
	$gender = $full_user_data->response->user->gender;

	echo "<br><br>firstName: ".$firstName;
	echo "<br><br>twitter: ".$twitter;
	

	//<------------------------------------------------------- REQUEST CASA DE MUNOZ VENUE DATA -------------------------------------------------------
	$casa_id = $casa_de_munoz_venue_id;
	//$casa_id = "4a6ef9a8f964a52020d51fe3"; //BBDO venue for testing
	$response = $foursquare->GetPublic("venues/".$casa_id);
	$venues = json_decode($response);
	//print_r($venues->response->venue->mayor);
	$venue_mayor_id = $venues->response->venue->mayor->user->id;
	echo "<br><br>mayor (user id): ".$venue_mayor_id."<br><br>";

	// DOES USER HAVE TWITTER HANDLE?
	$canTweet = true;
	if(strlen($twitter) < 1){
		$canTweet = false;
	}

	// TO REPLY OR TWEET?
	if($canTweet){
		//SEND TWEET
		$send_tweet = true;
		$send_reply = false;
	}else{
		//SEND REPLY
		$send_reply = true;
		$send_tweet = false;
	}
	$send_reply = true;
	$send_tweet = true;

	//<------------------------------------------------------- SEND REPLY TO CHECK IN
	if($send_reply){
		// CURERENTLY WAITING TO SEE WHICH KEY IS NEEDED IN ORDER TO BE REPLIED
		// IF ITS THE APPS KEY THAT IS SET AT THE TOP AND WILL REMAIN CONSTANT
		// IF THE END USERS KEY IS NEEDED THEN THE SCRIPT BELOW WILL HANDLE
		$needEndUserToken = true;
		if($needEndUserToken){
			$getToken = new GetUserToken();
			$host = $_SERVER['MYSQL_DB_HOST'];
			$dbUser = $_SERVER['MYSQL_USERNAME'];
			$dbPass = $_SERVER['MYSQL_PASSWORD'];
			$dbName = $_SERVER['MYSQL_DB_NAME'];
			$getToken->SetUserVars($user_id, $host, $dbUser, $dbPass, $dbName);
			$end_user_token = $getToken->GetToken();
			echo '$end_user_token: '.$end_user_token;
		}

		if($user_id == '7357331'){ // IS ME?
			$reply_text = "Welcome home, Sir.";
		}else if($venue_mayor_id == $user_id){ // IS MAYOR?
			if($gender == "male"){
				$reply_text = "Welcome back, Mr. Mayor.";
			}else if($gender == "female"){
				$reply_text = "Welcome back, Mrs. Mayor.";
			}else{
				$reply_text = ""; // NO GENDER SCENARIO?
			}
		}else if($user_id == '8219350' || $user_id == '10765442' || $user_id == '10803358' || $user_id == '1325573'){ // RAYZA, STEVEN, AMANDA, MICHAEL
			$tweet_text = "Actual Munoz checking in! You are home - open the fridge, get food, get drinks and dont be afraid to kick back and put your feet up.";
		}else{
			$reply_text = "Welcome, please make yourself at home.";
		}

		// SEND REPLY
		$foursquare->SetAccessToken($end_user_token);
		$foursquare->SetReplyText("Your replies are being posted!");
		$reply_result = $foursquare->SendReply("checkins/".$checkin_id."/reply");
		
		// SEND EMAIL/TXT MSG TO TRACE REPLY RESULT
		//	$user_text_address = $_SERVER['USER_TEXT_ADDRESS'];
		$from = "d@cdm.cm.com";
		$headers = "From:" . $from;
		//mail($user_text_address, "Reply2 Result:  ", $reply2, $headers);
		mail("cesar.t.munoz@gmail.com", "End User Key is:  ", $end_user_token, $headers);
		mail("cesar.t.munoz@gmail.com", "Reply Result is:  ", $reply_result, $headers);
	}
	

	//<------------------------------------------------------- SEND TWEET TO CHECK IN
	echo 'send_tweet: '.$send_tweet ;
	if($send_tweet){
		$random = rand(0, 13000); // TEMPORARY MEASURE TO ALLOW NON DUPLICATE TWEETS
		
		if($user_id == '281175'){
			$tweet_text = "Madrina @".$twitter." is in the house! Tweet Tweet!!"; //ZENNIA
		}else if($user_id == '1325573'){
			$tweet_text = "Padrino @".$twitter." is in the house! Tweet Tweet!!"; // MICHAEL
		}else if($user_id == '8219350' || $user_id == '10801047' || $user_id == '10803358'){
			$tweet_text = "Wassup cuz! @".$twitter." - mi casa es su casa ;)"; // RAYZA, STEVEN, AMANDA
		}else if($user_id == '7357331'){
			$tweet_text = "@".$twitter." Welcome home, Sir.".$random; // ME
		}
	}else{
		$tweet_text = $firstName." is in the house!!!"; // ANYONE ELSE
	}
	include "post_tweet.php";



	// SEND TEXT MSG
	//$user_text_address = $_SERVER['USER_TEXT_ADDRESS'];
	//$from = "domicilio@casademunoz.cesarmunoz.com";
	//$headers = "From:" . $from;
	//mail($user_text_address, "4sq Check-in", $firstName . " checked-in to Casa de Munoz and has a twitter handle of ".$twitLength." letters long", $headers);
	//mail($user_text_address, "4sq Check-in", $twitLength . " is twitter handle length", $headers);



	// SEND EMAIL
	//$body = $json_raw." <br><br><br><br><br><br>    Tweet Text: ".$tweet_text." <br>Check-in reply: ".$reply_text;
	//$body = $json_raw." <br><br><br><br><br><br>    Tweet Text: ".$tweet_text;
	//mail("cesar.t.munoz@gmail.com", "4sq Check-in", $body, $headers);


?>