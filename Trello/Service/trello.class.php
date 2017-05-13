<?php

namespace Trello\Service\Trello;

require_once 'configuration.class.php';

/**
 * @Created in PhpStorm 2017.1.
 * @author: Valencio Hoffman
 * @Date: 03-05-17
 * @license: Apache 2.0
 *
 * @version: 1.0.1
 *
 * Trello class
 * This class is created to make an connection through the authorize
 * route from Trello API.
 * Also possible is to make curl requests to the Trello API to receive your
 * boards, cards etc.
 */
class Client {

	/**
	 * Default client options
	 *
	 * @var array
	 */
	protected static $defaultOptions = [

		// Base url needed to make the connection
		'base_url'          => 'https://trello.com',

		// Describe the name of your application
		'application_name'  => 'YOUR_APPLICATION_NAME',

		// Your api key generated from the Trello API
		'key'               => 'PASTE_YOUR_API_KEY',

		// Your api secret generated from the Trello API;
		'secret'            => 'PASTE_YOUR_API_SECRET',

		// Redirect uri for when the user is authenticated
		'callback'          => null,

		// For when the token should be expired
		'expiration'        => "never",

		// Permissions needed to preform some actions
		'scope'       => array('read', 'write'),

		// The user token received when making the connection
		'token'             => null,
		'proxy'             => null,
		'version'           => '1'
	];

	/**
	 * Redirect handler for the hashtag in the browser
	 * If your page does not exist then the application will
	 * not load its content.
	 *
	 * @var string
	 */
	private $callback_handler_uri   = "http://api.valencio.nl/login/callback.php";

	/**
	 * Last error received from a curl request
	 *
	 * @var string
	 */
	private $lastError              = "";

	public function __construct($options) {
		// if args is not an array or the array is empty dont do anything
        static::$defaultOptions = Configuration::setMany($options, static::$defaultOptions);
	}

	// Authorize the user
	public function getToken() {

		session_start();

		// We are already logged in so don't log in again
		if($this->isAuthorized()) {
			return;
		}

		// If the application_token looks incorrect we display help
		$url_token = $this->buildUrl();
		if ( strlen( static::$defaultOptions["key"] ) < 30 ) {
			// 0) Fetch the Application tokenn
			// Source: https://trello.com/docs/gettingstarted/index.html#getting-a-token-from-a-user
			// We get the app token with "read" only access forever
			die( "Go to this URL with your web browser (eg. Chrome) to authorize your Trello Backups to run:\n$url_token\n" );

		}
		header('location: '. $url_token);
	}

	/**
	 * Build authorize url
	 *
	 * @return string
	 */
	public function buildUrl() {

		// store them temporary
		$_SESSION["api_key"] = static::$defaultOptions["key"];
		$_SESSION["api_secret"] = static::$defaultOptions["secret"];
		$_SESSION["callback"] = static::$defaultOptions["callback"];

		$url = '';
		$url .=  static::$defaultOptions["base_url"] .'/'. static::$defaultOptions["version"] .'/authorize?';
		$url .= 'key='. static::$defaultOptions["key"];
		$url .= '&name='. static::$defaultOptions["application_name"];
		$url .= '&expiration='. static::$defaultOptions["expiration"];
		$url .= '&return_url='. $this->callback_handler_uri;
		$url .= '&scope='. implode(",", static::$defaultOptions["scope"]);
		$url .= '&response_type=token';

		return $url;
	}

	/**
	 * To see if the user is logged in
	 *
	 * @return bool
	 */
	public function isAuthorized() {
		return (static::$defaultOptions["token"] != null);
	}

	/**
	 * Set the token
	 *
	 * @param null $token
	 */
	public function setToken($token = null) {
		if($token != null) {
			static::$defaultOptions["token"] = $token;
		}
	}

	/**
	 * Last error received from cURL request
	 *
	 * @return string
	 */
	public function error() {
		return $this->lastError;
	}

	/**
	 * rest
	 * This method actually performs the calls back to the Trello REST service
	 *
	 * @param string $method
	 * @param array $params
	 * @return mixed array of stdClass objects or false on failure
	 * @throws \Exception
	 */
	public function rest($method, $params = array()) {
		$restData = array();
		$path = (isset($params["path"])) ? $params["path"] : "";
		if (static::$defaultOptions["token"] && static::$defaultOptions["key"]) {
			$restData['token'] = static::$defaultOptions["token"];
			$restData['key'] = static::$defaultOptions["key"];
		}

		if (is_array($params)) {
			$restData = array_merge($restData, $params);
		}

		// Perform the CURL query
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);

		switch ($method) {
			case 'GET':
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($restData, '', '&'));
				$restData = array();
				break;
			case 'PUT':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($restData, '', '&'));
				$restData = array();
				break;
			case 'DELETE':
			case 'DEL':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			default:
				throw new \Exception('Invalid method specified');
				break;
		}

		$url = $this->buildRequestUrl($method, $path, $restData);

		curl_setopt($ch, CURLOPT_URL, $url);

		// Grab the response from Trello
		$responseBody = curl_exec($ch);
		if (!$responseBody) {

			// If there was a CURL error of some sort, log it and return false
			$this->lastError = curl_error($ch);
			return false;
		}

		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$responseBody = trim($responseBody);
		if (substr($responseCode, 0, 1) != '2') {

			// If we didn't get a 2xx HTTP response from Trello, log the responsebody as an error
			$this->lastError = $responseBody;
			return false;
		}

		$this->lastError = '';
		return json_decode($responseBody);
	}

	/**
	 * buildRequestUrl
	 * Parse arguments sent to the rest function.  Might be extended in future for callbacks.
	 *
	 * @param  string $method
	 * @param  string $path
	 * @param  array $data
	 * @return string
	 */
	protected function buildRequestUrl($method, $path, $data) {
		$url = static::$defaultOptions["base_url"] ."/". static::$defaultOptions["version"] ."/{$path}";

		// If we're using oauth, account for it
		// These methods require the data appended to the URL
		if (in_array($method, array('GET', 'DELETE', 'DEL')) && !empty($data)) {
			$url .= '?' . http_build_query($data, '', '&');
			return $url;
		}

		return "";
	}
}