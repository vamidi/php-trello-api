<?php
/**
 * Created by PhpStorm.
 * User: irenechiu
 * Date: 13-05-17
 * Time: 18:44
 */
namespace Github\Service;

use Configurations\Configuration;

require_once '../Configurations/configuration.class.php';

/**
 * @Created in PhpStorm 2017.1.
 * @author: Valencio Hoffman
 * @Date: 03-05-17
 * @license: Apache 2.0
 *
 * @version: 1.0.0
 *
 * Github class
 * This class is created to make an connection through the authorize
 * route from Github API.
 * Also possible is to make curl requests to the Github API to receive your
 * repository or user information etc.
 */
class Client
{

    /**
     * Default client options
     *
     * @var array
     */
    protected static $defaultOptions = [

        // Base url needed to make the connection
        'base_url'          => 'https://github.com',

        // Your client id generated from the Github API
        'client_id'         => 'PASTE_YOUR_CLIENT_ID',

        // Your api secret generated from the Trello API;
        'client_secret'      => 'PASTE_YOUR_API_SECRET',

        // Whether or not unauthenticated users will be offered an option to sign up for GitHub during the OAuth flow
        'allow_signup'      => true,

        // Redirect uri for when the user is authenticated
        'redirect_uri'      => null,

        // FAn unguessable random string. It is used to protect against cross-site request forgery attacks.
        'state'             => "RANDOM_STRING",

        // Permissions needed to preform some actions
        'scope'             => array('user'),

        // The user code received when making the connection
        'code'              => null,
        // The user access token received when making the connection after the authorization
        'access_token'      => '',
        'proxy'             => null
    ];

    /**
     * Redirect handler for the hashtag in the browser
     * If your page does not exist then the application will
     * not load its content.
     *
     * @var string
     */
    private $callback_handler_uri = "http://api.valencio.nl/login/callback.php";

    /**
     * Last error received from a curl request
     *
     * @var string
     */
    private $lastError = "";

    public function __construct($options) {
        // if args is not an array or the array is empty dont do anything
        static::$defaultOptions = Configuration::setMany($options, static::$defaultOptions);
    }

    // Authorize the user
    public function authorizeUser() {

        session_start();

        // We are already logged in so don't log in again
        if($this->isAuthorized()) {
            return;
        }

        // If the application_token looks incorrect we display help
        $url_token = $this->buildUrl();
        if ( strlen( static::$defaultOptions["client_id"] ) < 30 ) {
            // 0) Fetch the Application tokenn
            // Source: https://trello.com/docs/gettingstarted/index.html#getting-a-token-from-a-user
            // We get the app token with "read" only access forever
            die( "Go to this URL with your web browser (eg. Chrome) to authorize your Github Backups to run:\n$url_token\n" );

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
        $_SESSION["client_id"] = static::$defaultOptions["client_id"];
        $_SESSION["client_secret"] = static::$defaultOptions["client_secret"];
        $_SESSION["redirect_uri"] = static::$defaultOptions["redirect_uri"];

        $url = '';
        $url .=  static::$defaultOptions["base_url"] .'/login/oauth/authorize?';
        $url .= 'client_id='. static::$defaultOptions["client_id"];
        $url .= '&redirect_uri='. $this->callback_handler_uri;
        $url .= '&scope='. implode(",", static::$defaultOptions["scope"]);
        $url .= '&state='. static::$defaultOptions["state"];

        return $url;
    }

    /**
     * To see if the user is logged in
     *
     * @return bool
     */
    public function isAuthorized() {
        return (static::$defaultOptions["code"] != null);
    }

    /**
     * Set the token
     *
     * @param null $token
     */
    public function setToken($token = null) {
        if($token != null) {
            static::$defaultOptions["access_token"] = $token;
        }
    }
}