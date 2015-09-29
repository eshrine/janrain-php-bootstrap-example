<?php
/**
 *  Endpoint to fetch session data that can be exposed client-side.
 *  JSON response.
 */

require_once("janrain.php");

header('Content-Type: application/json');
ini_set('display_errors', False);

session_start();



if (!empty($_POST['token'])) {
    // Use the Janrain access token from the user's session to retrieve the profile data
    trigger_error("Retrieving profile data with access token: {$_POST['token']}");
    echo json_encode(array(
    	'status' => 'success',
    	'data' => janrain_get_profile_data($_POST['token'])
	));
}

?>