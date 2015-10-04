<?php
/**
 *  Endpoint to register an end-user. JSON response.
 */

require_once("config.php");
require_once("janrain.php");

header('Content-Type: application/json');
ini_set('display_errors', False);
session_start();

if (!empty($_POST['token'])) {
    // Use the auth token from Janrain's Social Login widget and the data
    // provided in the social registration form to register the user with social credentials.
    trigger_error("Registering user with Social Login token: {$_POST['token']}");
    $response = janrain_social_registration(
        $_POST['token'],
        $_POST['emailAddress'],
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['displayName']
        );
    echo json_encode(handle_auth_response($response));

} elseif (!empty($_POST['emailAddress']) && !empty($_POST['newPassword'])) {
    // Use the data provided in the registration form to create a new account 
    // with traditional credentials.
    trigger_error("Registering user with traditional credentials");
    $response = janrain_traditional_registration(
        $_POST['emailAddress'], 
        $_POST['newPassword'],
        $_POST['newPasswordConfirm'],
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['displayName']);
    echo json_encode(handle_auth_response($response));

} else {
    echo json_encode(array(
        'status' => 'error',
        'message' => "Please enter your credentials."
    ));
}

function handle_auth_response($response) {
    if ($response['stat'] == "ok") {
        trigger_error("Authenticated user. UUID: {$response['capture_user']['uuid']}");
        trigger_error("Exchanging authorization code: {$response['authorization_code']}");
        $tokens = janrain_exchange_authorization_code($response['authorization_code']);
        janrain_set_session_data($tokens, $response['capture_user']);

        return array(
            'status' => 'success',
            'data' => janrain_get_client_side_session_data()
        );
    } else {
        if ($response['error'] == 'invalid_credentials') {
            return array(
                'status' => 'error',
                'message' => $response['invalid_fields']['signInForm'][0],
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