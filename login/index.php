<?php
require_once __DIR__ . '/../Trello/trello.class.php';

use Valenciohoffman\Service\Trello\Client;

// Response for the API
$response       = [];
$options = array(
	// Describe the name of your application
	'application_name'  => 'Vamidicreations',
	// Your api key generated from the Trello API
	'key'               => 'PASTE_YOUR_API_KEY',
	// Your api secret generated from the Trello API;
	'secret'            => 'PASTE_YOUR_API_SECRET',
	// Redirect uri for when the user is authenticated
	'callback'          => 'http://api.valencio.nl/',
	// Permissions needed to preform some actions
	'scope'       => array('read', 'write', 'account')
);
// Get the trello api client object
$trello         = new Client($options);
// Get the token from Trello API
$trello->getToken();