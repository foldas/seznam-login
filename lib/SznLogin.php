<?php
class SznLogin {
	/** @var string */
	private $clientId;
	/** @var string */
	private $clientSecret;
	/** @var string */
	private $token;
	/** @var string */
	private $redirectUri;
	/** @var int */
	private $timeout;
	/**
	 * @param string $clientId
	 * @param string $clientSecret
	 */
	public function __construct($clientId,$clientSecret) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->token = '';
        $this->redirectUri = '';
		$this->timeout = 30;
	}
	/**
	 * @param int $seconds
	 */
    public function setTimeout($seconds) {
		$this->timeout = (int)$seconds;
	}
	/**
	 * @return int
	 */
	public function getTimeout() {
		return $this->timeout;
	}
	/**
	 * @param string $redirectUri
	 */
	public function setRedirectUri($redirectUri) {
		$this->redirectUri = $redirectUri;
	}
	/**
	 * @return string
	 */
	public function getRedirectUri() {
		return $this->redirectUri;
	}
	/**
	 * @return string
	 */
	public function getLoginUrl() {
		$result="https://login.szn.cz/api/v1/oauth/auth?client_id=".$this->clientId."&scope=identity&response_type=code&redirect_uri=".urlencode($this->getRedirectUri());
		return $result;
	}
	/**
	 * @param string $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}
	/**
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}
	/**
	 * Authorize and get user info include access token.
	 * @return array
	 */
	public function authorize() {
		$data = [
			'grant_type' => "authorization_code",
			'code' => $this->getToken(),
			'redirect_uri' => $this->getRedirectUri(),
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret
		];
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://login.szn.cz/api/v1/oauth/token');
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeout());
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			$curl_error = curl_error($ch);
		}
		curl_close($ch);
		if (isset($curl_error)) {
			$result=[
				'success' => false,
				'response' => $curl_error
			];
		} else {
			$output=json_decode($response, true);
			if ($output['status']==200) {
				$result=[
					'success' => true,
					'oauth_user_id' => $output['oauth_user_id'],
					'email' => $output['account_name'],
					'access_token' => $output['access_token'],
					'refresh_token' => $output['refresh_token']
				];
			} else {
				$result=[
					'success' => false,
					'response' => $output['message']
				];
			}
		}
		return $result;
	}
	/**
	 * Destroy access token.
	 * @param string $dtoken
	 * @param int $typ
	 * @return array
	 */
	public function destroyToken($dtoken,$typ=1) {
		if ($typ==1) {
			$data = [
				'token_type_hint' => "access_token",
				'token' => $dtoken
			];
		} else {
			$data = [
				'token_type_hint' => "refresh_token",
				'token' => $dtoken
			];
		}
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://login.szn.cz/api/v1/oauth/revoke');
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer '.$dtoken]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeout());
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			$curl_error = curl_error($ch);
		}
		curl_close($ch);
		if (isset($curl_error)) {
			$result=[
				'success' => false,
				'response' => $curl_error
			];
		} else {
			$output=json_decode($response, true);
			if ($output['status']==200) {
				$result=[
					'success' => true
				];
			} else {
				$result=[
					'success' => false,
					'response' => $output['message']
				];
			}
		}
		return $result;
	}
	/**
	 * Get user information.
	 * @param string $dtoken
	 * @return array
	 */
	public function userInfo($dtoken) {
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://login.szn.cz/api/v1/user');
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer '.$dtoken]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->getTimeout());
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			$curl_error = curl_error($ch);
		}
		curl_close($ch);
		if (isset($curl_error)) {
			$result=[
				'success' => false,
				'response' => $curl_error
			];
		} else {
			$output=json_decode($response, true);
			if ($output['status']==200) {
				$result=[
					'success' => true,
					'oauth_user_id' => $output['oauth_user_id'],
					'email' => $output['email'],
					'firstname' => $output['firstname'],
					'lastname' => $output['lastname']
				];
			} else {
				$result=[
					'success' => false,
					'response' => $output['message']
				];
			}
		}
		return $result;
	}
}