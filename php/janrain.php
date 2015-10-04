<?php
/**
 *  Functions for interfacing with Janrain APIs and managing the user's session.
 */
require_once("config.php");

/**
 *  Perform a Janrain social authentication.
 *
 *  @param string $auth_token returned from Janrain Social Login widget
 *
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_social_auth($auth_token) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'locale' => 'en-US',
        'response_type' => 'code',
        'redirect_uri' => 'https://localhost',
        'token' => $auth_token,
        'registration_form' => 'socialRegistrationForm'
    );

    return janrain_api('/oauth/auth_native', $params);
}


/**
 *  Perform a Janrain traditional authentication.
 *
 *  @param string $email     user's email address
 *  @param string $password  user's password
 *
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_traditional_auth($email, $password) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'locale' => 'en-US',
        'response_type' => 'code',
        'redirect_uri' => 'https://localhost',
        'form' => 'signInForm',
        'signInEmailAddress' => $email,
        'currentPassword' => $password
    );

    return janrain_api('/oauth/auth_native_traditional', $params);
}

/**
 *  Perform a Janrain social registration.
 *
 *  @param string $token        returned from Janrain Social Login widget
 *  @param string $email        user's email address
 *  @param string $firstName    user's first name
 *  @param string $lastName     user's last name
 *  @param string $displayName  user's display name
 *
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_social_registration($token, $email, $firstName, $lastName, $displayName) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'locale' => 'en-US',
        'response_type' => 'code',
        'redirect_uri' => 'https://localhost',
        'form' => 'socialRegistrationForm',
        'firstName' => $firstName,
        'lastName' => $lastName,
        'displayName' => $displayName,
        'emailAddress' => $email,
        'token' => $token
    );

    return janrain_api('/oauth/register_native', $params);
}

/**
 *  Perform a Janrain traditional registration.
 *
 *  @param string $email            user's email address
 *  @param string $password         user's password
 *  @param string $passwordConfirm  user's password confirm
 *  @param string $firstName        user's first name
 *  @param string $lastName         user's last name
 *  @param string $displayName      user's display name
 *
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_traditional_registration($email, $password, $passwordConfirm, 
    $firstName, $lastName, $displayName) {

    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'locale' => 'en-US',
        'response_type' => 'code',
        'redirect_uri' => 'https://localhost',
        'form' => 'registrationForm',
        'firstName' => $firstName,
        'lastName' => $lastName,
        'displayName' => $displayName,
        'emailAddress' => $email,
        'newPassword' => $password,
        'newPasswordConfirm' => $passwordConfirm
    );

    return janrain_api('/oauth/register_native_traditional', $params);
}

/**
 *  Exchange authorization code for access token and refresh token
 *
 *  @param string $authorization_code authorization code returned from one of
 *                                    the Janrain auth endpoints
 *
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_exchange_authorization_code($authorization_code) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'client_secret' => JANRAIN_LOGIN_CLIENT_SECRET,
        'grant_type' => 'authorization_code',
        'code' => $authorization_code,
        'redirect_uri' => 'https://localhost'
    );

    return janrain_api('/oauth/token', $params);
}

/**
 *  Exchange password reset code for access token and refresh token
 *
 *  @param string $code authorization code returned from forgot password flow
 *
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_exchange_password_reset_code($code) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'client_secret' => JANRAIN_LOGIN_CLIENT_SECRET,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => JANRAIN_PASSWORD_RECOVER_URL
    );

    return janrain_api('/oauth/token', $params);
}


/**
 *  Save information about the authenticated Janrain user in the PHP session.
 *
 *  @param string $tokens   an array of Janrain tokens as returned from calling
 *                          janrain_exchange_authorization_code()
 *  @param array  $profile  an array representing a Janrain user profile object
 */
function janrain_set_session_data($tokens, $profile) {
    // The Janrain UUID is the unique identifier of the user profile in Janrain
    $_SESSION['janrain_uuid'] = $profile['uuid'];

    // Saving the access token allows for additional API calls to be made to the
    // Janrain API. This access token is scoped for this user and is typically
    // used with calls to /entity and /entity.update.
    $_SESSION['janrain_access_token'] = $tokens['access_token'];

    // Saving the refresh token allows the access token to be refreshed if the
    // PHP session lasts longer than the access token which is stored in the
    // 'expires' variable (default 1 hour).
    $expires = strtotime("+{$tokens['expires_in']} seconds");
    $_SESSION['janrain_refresh_token'] = $tokens['refresh_token'];
    $_SESSION['janrain_token_expires'] = $expires;

    // Any data in the Janrain user profile that is needed by the application
    // can also be saved in the PHP session. In this case, saving the display
    // name will allow client-side code to present a personalized experience.
    $_SESSION['janrain_displayName'] = $profile['displayName'];
}

/**
 *  Get data about the Janrain user which can be exposed client-side. Secure
 *  information (such as the refresh token) should not be exposed client-side.
 *
 *  @return array associative array containing key => value pairs
 */
function janrain_get_client_side_session_data() {
    if (!empty($_SESSION['janrain_uuid'])) {
        return array(
            'uuid' => $_SESSION['janrain_uuid'],
            'token' => $_SESSION['janrain_access_token'],
            'displayName' => $_SESSION['janrain_displayName']
        );
    } else {
        return array(
            'uuid' => null,
            'token' => null,
            'displayName' => null
        );
    }
}

/**
 *  Retrieve an authenticated user's profile data using the access
 *
 *  @param string @token Janrain access token to use when retrieving profile data
 * 
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_get_profile_data($token) {
    $params = array(
        'access_token' => $token,
        'type_name' => 'user'
    );

    return janrain_api('/entity', $params);
}

/**
 *  Save the user's profile data 
 *
 *  @param string $accessToken      user's access token
 *  @param string $email            user's email address
 *  @param string $firstName        user's first name
 *  @param string $lastName         user's last name
 *  @param string $displayName      user's display name
 *  @param string $gender           user's gender
 * 
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_save_profile_data($accessToken, $email, $firstName, $lastName, $displayName, $gender) {
    $params = array(
            'client_id' => JANRAIN_LOGIN_CLIENT_ID,
            'locale' => 'en-US',
            'access_token' => $accessToken,
            'form' => 'editProfileForm',
            'firstName' => $firstName,
            'lastName' => $lastName,
            'displayName' => $displayName,
            'emailAddress' => $email,
            'gender' => $gender
        );

    trigger_error(json_encode($params));

    return janrain_api('/oauth/update_profile_native', $params);
}

/**
 *  Initiate a forgot password flow 
 *
 *  @param string $email           user's email address
 * 
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_forgot_password($email) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'redirect_uri' => JANRAIN_PASSWORD_RECOVER_URL,
        'locale' => 'en-US',
        'response_type' => 'code',
        'form' => 'forgotPasswordForm',
        'signInEmailAddress' => $email
    );

    trigger_error(json_encode($params));

    return janrain_api('/oauth/forgot_password_native', $params);
}

/**
 *  Change a user's password
 *
 *  @param string $accessToken           user's access token
 *  @param string $newPassword            user's new password
 *  @param string $newPasswordConfirm     user's new password confirmed
 * 
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_change_password($accessToken, $newPassword, $newPasswordConfirm) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'access_token' => $accessToken,
        'locale' => 'en-US',
        'form' => 'changePasswordFormNoAuth',
        'newPassword' => $newPassword,
        'newPasswordConfirm' => $newPasswordConfirm
    );

    trigger_error(json_encode($params));

    return janrain_api('/oauth/update_profile_native', $params);
}

/**
 *  Make a call to the Janrain API
 *
 *  @param string $call    the relative endpoint of the API call. Eg. "/entity"
 *  @param array  $params  parameters to pass to the Janrain API
 *
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_api($call, $params) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, JANRAIN_CAPTURE_API_URL.$call);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}
?>