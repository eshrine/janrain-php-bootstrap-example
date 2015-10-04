<?php
/**
 *  Endpoint for the reset password flow. JSON response.
 */

require_once("config.php");
require_once("janrain.php");

header('Content-Type: application/json');
ini_set('display_errors', False);
session_start();

if (!empty($_POST['code'])) {
    // Exchange the auth code that was sent to the user's email for an access token
    trigger_error("Exchanging authorization code: {$_POST['code']}");
    $response = janrain_exchange_password_reset_code($_POST['code']);
    echo json_encode(handle_code_response($response));

} elseif (!empty($_POST['token']) && !empty($_POST['newPassword'])) {
    // Use a traditional set of user credentials to sign in. In this case the
    // credentials are an email and password pair.
    trigger_error("Authenticating user with traditional credentials");
    $response = janrain_change_password($_POST['token'], $_POST['newPassword'], $_POST['newPasswordConfirm']);
    echo json_encode(handle_change_response($response));

} else {
    echo json_encode(array(
        'status' => 'error',
        'message' => "Invalid parameters"
    ));
}

function handle_code_response($response) {
    if ($response['stat'] == "ok") {

        return array(
            'status' => 'success',
            'token' => $response['access_token'],
            'data' => $response
        );
    } else {
        if ($response['error'] == 'invalid_request') {
            return array(
                'status' => 'error',
                'message' => 'invalid_request',
                'data' => $response
            );
        } else {
            return array(
                'status' => 'error',
                'message' => $response['error'],
                'data' => $response
            );
        }
    }
}

function handle_change_response($response) {
    if ($response['stat'] == "ok") {
        return array(
            'status' => 'success',
        );
    } else {
        if ($response['error'] == "invalid_form_fields") {
            return array(
                'status' => 'error',
                'message' => $response['invalid_fields'],
                'code' => $response['code'],
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
}
?>