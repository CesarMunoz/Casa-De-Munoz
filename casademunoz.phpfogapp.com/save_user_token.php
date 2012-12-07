<?php
/**
* This will collect the end user token from 4SQ
* 
* 
* @author Cesar Munoz
* @license GNU Public License
*/

require_once("FoursquareAPI.class.php");

// PHPFOG DB
$server = $_SERVER['MYSQL_DB_HOST'];
$user_name = $_SERVER['MYSQL_USERNAME'];
$pass = $_SERVER['MYSQL_PASSWORD'];
$db = $_SERVER['MYSQL_DB_NAME'];


// FOURSQUARE CREDS
$client_key = $_SERVER['4SQ_CLIENT_ID'];
$client_secret = $_SERVER['4SQ_CLIENT_SECRET'];
$casa_de_munoz_venue_id = $_SERVER['4SQ_CASA_VENUE_ID'];
$uri = $_SERVER['4SQ_URI'];


// Load the Foursquare API library
$foursquare = new FoursquareAPI($client_key, $client_secret);


// GET TOKEN
$token_val = $_GET['token_val'];
//print_r ('print_r $token_val: '.$token_val);
//echo '$token_val: '.$token_val;

if( isset($token_val) ){
	header( 'Location: https://casademunoz.phpfogapp.com/oops.html' ) ;
	header( 'Location: http://localhost/casa/oops.html' ) ;
	echo '<br><br>echo $token_val: '.$token_val;
}

$foursquare->SetAccessToken($token_val);


// GET USER INFO
$user_json_data = $foursquare->GetPrivate("users/self");
$full_user_data = json_decode($user_json_data);
$firstName = $full_user_data->response->user->firstName;
$lastName = $full_user_data->response->user->lastName;
$id = $full_user_data->response->user->id;


// CONNECT TO DB AND SAVE DATA
$connect = mysql_connect($server, $user_name, $pass) or die(mysql_error());
echo "Connected to MySQL<br/>";
mysql_select_db($db) or die(mysql_error());
echo "Connected to Database<br/>";

$con = mysql_connect($server, $user_name, $pass);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db($db, $con);

$sql="INSERT INTO 4SQ_USER_TOKENS (user_id, user_token, FirstName, LastName) VALUES ('$id','$token_val','$firstName','$lastName')";

if (!mysql_query($sql,$con))
  {
  die('Error: ' . mysql_error());
  }
echo "data added";

mysql_close($con);

header( 'Location: https://casademunoz.phpfogapp.com/user_authorized.html' ) ;

?>
