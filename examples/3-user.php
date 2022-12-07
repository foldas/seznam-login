<?php
/**
 * Get user detail from Seznam account.
 * @param string $client_id
 * @param string $client_secret
 * @param string $access_token
 */
require_once __DIR__.'/../lib/SznLogin.php';

$client_id = "";
$client_secret = "";
$access_token = "";

$client = new SznLogin($client_id,$client_secret);
$client->setTimeout(5);	// lower timeout from default 30
$user=$client->userInfo($access_token);
if ($user['success']===true) {
	if (!empty($user['email'])) echo "E-mail: ".$user['email']."<br/>\n";
	if (!empty($user['firstname'])) echo "Jméno: ".$user['firstname']."<br/>\n";
	if (!empty($user['lastname'])) echo "Příjmení: ".$user['lastname']."<br/>\n";
} else {
	echo $user['response'];	// error
}
