<?php
/**
 *  Endpoint to fetch session data that can be exposed client-side.
 *  JSON response.
 */

require_once("janrain.php");

header('Content-Type: application/json');
ini_set('display_errors', False);

session_start();

$accessToken = $_SESSION["janrain_access_token"];

if (!empty($_POST['emailAddress'])) {
    trigger_error("Saving profile with access token: {$accessToken}");
    $response = janrain_save_profile_data(
        $accessToken,
        $_POST['emailAddress'],
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['displayName'],
        $_POST['gender']
    );

    echo json_encode(handle_response($response));
}

function handle_response($response) {
    
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

