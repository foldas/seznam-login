<?php
/**
 * Authorize user.
 * @param string $client_id
 * @param string $client_secret
 * @param string $redirect_url
 * @param string $_GET['code']
 */
require_once __DIR__.'/../lib/SznLogin.php';

$client_id = "";
$client_secret = "";
$redirect_url = "";

$client = new SznLogin($client_id,$client_secret);
$client->setRedirectUri($redirect_url);

if (isset($_GET['code'])) {
    $client->setToken($_GET['code']);
	$seznamAuth = $client->authorize();
	if ($seznamAuth['success']===true) {
		echo "oauth_user_id: ".$seznamAuth['oauth_user_id']."<br/>\n";
		echo "email: ".$seznamAuth['email']."<br/>\n";
		echo "access_token: ".$seznamAuth['access_token']."<br/>\n";
		echo "refresh_token: ".$seznamAuth['refresh_token']."<br/>\n";
	} else {
		echo $user['response'];	// error
	}
}
