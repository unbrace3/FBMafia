<?php
$access_token = "EAAH9ZB08zbAwBAPv86uoe8TZBKyMxr8ZANzUlDs9ujKWdIJpHZAktTun7A7UZA8Mqq3b21a9ZA1ZC3Nb5XWzZBfvNw1pgWfyvbNIxlYuhnrQoBLl4NbhWxxwa2N2EwOPqdocyxB0HZBP9Wddfn9JW1rYOQ0dYwNB8ulkJNVcJdZCquOAZDZD";
$verify_token = "my_access_code";
$hub_verify_token = null;

function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
} 

# This function reads your DATABASE_URL configuration automatically set by Heroku
# the return value is a string that will work with pg_connect
function pg_connection_string() {
  return "dbname=abcdefg host=****.amazonaws.com port=5432 user=**** password=**** sslmode=require";
}
 
# Establish db connection
$db = pg_connect(pg_connection_string());
if (!$db) {
    echo "Database connection error."
    exit;
}
 
$counter = pg_query($db, "SELECT userID FROM players WHERE id=1");
$st->execute();
$counter=$st->fetch(PDO::FETCH_OBJ);

$isGame = array();
$gameHoster = array();
$input = json_decode(file_get_contents('php://input'), true);
$sender = $input['entry'][0]['messaging'][0]['sender']['id'];
$message = $input['entry'][0]['messaging'][0]['message']['text'];
$message_to_reply = '';
/**
 * Some Basic rules to validate incoming messages
 */
/*if(preg_match('[time|current time|now]', strtolower($message))) {
    // Make request to Time API
    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
    $result = file_get_contents("http://www.timeapi.org/utc/now?format=%25a%20%25b%20%25d%20%25I:%25M:%25S%20%25Y");
    if($result != '') {
        $message_to_reply = $result;
    }
} else {
    $message_to_reply = 'Huh! what do you mean?';
}*/
if(preg_match('[start game]', strtolower($message))) {
	$counter=$counter+1;
	$gameID=generateRandomString();
	$isGame[$gameID]=true;
	$gameHoster[$gameID]=$sender;
	$message_to_reply = $gameID+$counter;
} else {
	$message_to_reply = 'Mafia incoming! Stay tuned!';
}
//API Url
$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;
//Initiate cURL.
$ch = curl_init($url);
//The JSON data.

$jsonData = '{
	"recipient":{
		"id":"'.$sender.'"
	},
	"message":{
		"text":"'.$message_to_reply.'"
	}
}';
//Encode the array into JSON.
$jsonDataEncoded = $jsonData;
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
//Execute the request
if(!empty($input['entry'][0]['messaging'][0]['message'])){
	$result = curl_exec($ch);
}

