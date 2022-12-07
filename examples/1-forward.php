<?php
/**
 * Get redirect URL.
 * @param string $client_id
 * @param string $client_secret
 * @param string $redirect_url
 */
require_once __DIR__.'/../lib/SznLogin.php';

$client_id = "";
$client_secret = "";
$redirect_url = "";

$client = new SznLogin($client_id,$client_secret);
$client->setRedirectUri($redirect_url);
echo $client->getLoginUrl();
