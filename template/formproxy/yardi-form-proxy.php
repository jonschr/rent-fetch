<?php

//* Load WordPress so that we get access to those functions
require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/wp-load.php');

$curl = curl_init();

//* We know the URL already
$url = 'https://api.rentcafe.com/rentcafeapi.aspx?requestType=lead';

//* Get the variables from the POST request
if ( isset( $_POST['FirstName'] ) )
	$FirstName = urlencode( htmlspecialchars( $_POST['FirstName'] ) );
	
if ( isset( $_POST['LastName'] ) )
	$LastName = urlencode( htmlspecialchars( $_POST['LastName'] ) );

if ( isset( $_POST['Email'] ) )
	$Email = urlencode( htmlspecialchars( $_POST['Email'] ) ); 
	
if ( isset( $_POST['Phone'] ) )
	$Phone = urlencode( htmlspecialchars( $_POST['Phone'] ) );
	
if ( isset( $_POST['Message'] ) )
	$Message = urlencode( htmlspecialchars( $_POST['Message'] ) );
	
if ( isset( $_POST['PropertyCode'] ) )
	$propertycode = urlencode( htmlspecialchars( $_POST['PropertyCode'] ) );
	
if ( isset( $_POST['Source'] ) )
	$source = urlencode( htmlspecialchars( $_POST['Source'] ) );
	
//* Get private information
$yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
$username = $yardi_integration_creds['yardi_username'];
$password = $yardi_integration_creds['yardi_password'];

//* Add the variables to the URL
if ( $FirstName )
	$url = $url . '&firstName=' . $FirstName;
	
if ( $LastName )
	$url = $url . '&lastName=' . $LastName;
	
if ( $Email )
	$url = $url . '&email=' . $Email;
	
if ( $Phone )
	$url = $url . '&phone=' . $Phone;
	
if ( $Message )
	$url = $url . '&message=' . $Message;
	
if ( $propertycode )
	$url = $url . '&propertycode=' . $propertycode;
	
if ( $username )
	$url = $url . '&username=' . $username;
	
if ( $password )
	$url = $url . '&password=' . $password;
	
if ( $source )
	$url = $url . '&source=' . $source;
	
//* Set up CURL
curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
));

//* Execute
$response = curl_exec($curl);

curl_close($curl);

//* Return the response
echo $response;
