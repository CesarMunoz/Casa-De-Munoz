<?php
/**
* post_tweet.php
* Example of posting a tweet with OAuth
* Latest copy of this code: 
* http://140dev.com/twitter-api-programming-tutorials/hello-twitter-oauth-php/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
*/

// TWITTER CREDS
$twitter_consumer_secret = $_SERVER['TWITTER_CONSUMER_SECRET'];
$twitter_user_secret = $_SERVER['TWITTER_USER_SECRET'];
$twitter_consumer_key = $_SERVER['TWITTER_CONSUMER_KEY'];
$twitter_user_token = $_SERVER['TWITTER_USER_TOKEN'];

//print "Posting...<br><br>";
$result = post_tweet($tweet_text, $twitter_consumer_secret, $twitter_user_secret, $twitter_consumer_key, $twitter_user_token);

// SEND TEXT MSG TO TRACE TWITTER RESULT
/*  $user_text_address = $_SERVER['USER_TEXT_ADDRESS'];
  $from = "d@cdm.cm.com";
  $headers = "From:" . $from;
  mail($user_text_address, "4sq Check-in Tweet Result:  ", $result, $headers);*/


print "<br>Response code: " . $result . "\n";

function post_tweet($tweet_text, $twitter_consumer_secret, $twitter_user_secret, $twitter_consumer_key, $twitter_user_token) {

  // Use Matt Harris' OAuth library to make the connection
  // This lives at: https://github.com/themattharris/tmhOAuth
  require_once('tmhoauth/tmhOAuth.php');
      
  // Set the authorization values
  // In keeping with the OAuth tradition of maximum confusion, 
  // the names of some of these values are different from the Twitter Dev interface
  // user_token is called Access Token on the Dev site
  // user_secret is called Access Token Secret on the Dev site
  // The values here have asterisks to hide the true contents 
  // You need to use the actual values from Twitter
  $connection = new tmhOAuth(array(
    'consumer_key' => $twitter_consumer_key,
    'consumer_secret' => $twitter_consumer_secret,
    'user_token' => $twitter_user_token,
    'user_secret' => $twitter_user_secret,
  )); 
  
  // Make the API call
  $connection->request('POST', 
    $connection->url('1/statuses/update'), 
    array('status' => $tweet_text));
  
  return $connection->response['code'];
}
?>