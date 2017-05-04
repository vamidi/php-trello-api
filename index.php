<?php

session_start();

use Valenciohoffman\Service\Trello\Client;

require_once __DIR__ . '/Trello/trello.class.php';

$response = [];
if ( isset( $_GET["access_token"] ) ) {
	$options = array(
		// Describe the name of your application
		'application_name'  => 'Vamidicreations',
		// Your api key generated from the Trello API
		'key'               => (isset($_SESSION["api_key"])) ? $_SESSION["api_key"] : "",
		// Your api secret generated from the Trello API;
		'secret'            => (isset($_SESSION["api_secret"])) ? $_SESSION["api_key"] : "",
		// Redirect uri for when the user is authenticated
		'callback'          => (isset($_SESSION["callback"])) ? $_SESSION["callback"] : "",
		'token'             => (isset($_GET["access_token"])) ?  $_GET["access_token"] : "",
		// Permissions needed to preform some actions
		'scope'       => array('read', 'write', 'account')
	);
	// Get the trello client
	$trello = new Client( $options );
	// Do a search query on the REST API
	$boards = ( $trello->rest( "GET", array(
		"path" => 'members/me/boards'
	) ) );
	// if the boards returns a boolean that the query might be wrong
	if ( is_bool( $boards ) && $boards === false ) {
		$response["error"]     = true;
		// Log the curl error
		$response["error_msg"] = $trello->error();
	} else if ( is_array( $boards ) ) {
		$response["error"]     = false;
		$response["error_msg"] = $trello->error();
		// Set the board in the response
		$response["boards"]    = $boards;
	}

	// print out the response
	echo json_encode( $response );
}