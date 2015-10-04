<?php
/**
 *  Endpoint to initiate forgot password. JSON response.
 */

require_once("config.php");
require_once("janrain.php");

header('Content-Type: application/json');
ini_set('display_errors', False);
session_start();

if (!empty($_POST['emailAddress'])) {
    // Use the email address to initiate forgot password flow.
    trigger_error("Initiating forgot password flow for: {$_POST['emailAddress']}");
    $response = janrain_forgot_password($_POST['emailAddress']);
    echo json_encode(handle_response($response));

} else {
    echo json_encode(array(
        'status' => 'error',
        'message' => "Please enter an email address."
    ));
}

function handle_response($response) {
    if ($response['stat'] == "ok") {

        return array(
            'status' => 'success',
            'data' => $response
        );
    } else {
        
            return array(
                'status' => 'error',
                'message' => $response['error_description'],
                'code' => $response['code'],
                'data' => $response
            );
    }
}
?>