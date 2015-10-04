(function() {
    if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};

    // token action must be 'event' so that the token can be handled via AJAX
    // rather than a redirect to a token URL.
    janrain.settings.tokenAction = 'event';

    // These settings are visual aspects to make the Social Login widget
    // fit better in the default Bootstrap theme.
    janrain.settings.width = '370';
    janrain.settings.borderColor = '#FFFFFF';
    janrain.settings.actionText = ' ';
    janrain.settings.showAttribution = false;

    function isReady() { janrain.ready = true; };
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
      window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainAuthWidget';

    if (document.location.protocol === 'https:') {
      e.src = 'https://rpxnow.com/js/lib/eshrine/engage.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/eshrine/engage.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();

var JanrainBootstrap = (function($, janrain) {

    var signInUrl = 'php/sign_in.php';
    var signOutUrl = 'php/sign_out.php';
    var registerUrl = 'php/register.php';
    var sessionDataUrl = 'php/session_data.php';
    var profileDataUrl = 'php/profile_data.php';
    var saveProfileUrl = 'php/save_profile.php';
    var forgotPasswordUrl = 'php/forgot_password.php';
    var resetPasswordUrl = 'php/reset_password.php';

    var addBootstrapEventHandlers = function() {
        // Hide all errors in the modal dialogs when the modal is hidden
        $('.janrain-modal').on('hidden.bs.modal', function () {
            $('.janrain-error').hide();
        });

        // Bind any element with .janrain-sign-out to the signOut() method
        $('.janrain-sign-out').on('click', function(e) {
            signOut();
        });

        // Bind traditional sign in forms to signIn() method
        $('.janrain-sign-in-form').submit(function(e) {
            // TODO: this becomes signIn()
            console.log($(this).serialize());
            $.post(signInUrl, $(this).serialize(), function(response) {
                console.log(response);
                if (response.status == "success") {
                    refreshSessionState(response.data);
                    $('.janrain-modal').modal('hide');
                } else if (response.status == "error") {
                    var alert = $('#janrainSignInScreen .janrain-form-error:first');
                    alert.text(response.message);
                    alert.show();
                }
            });

            e.preventDefault();
        });

        // Bind reigstration forms to register() method
        $('.janrain-registration-form').submit(function(e) {
            // TODO: this becomes register()
            console.log($(this).serialize());
            $.post(registerUrl, $(this).serialize(), function(response) {
                console.log(response);
                if (response.status == "success") {
                    refreshSessionState(response.data);
                    $('.janrain-modal').modal('hide');
                } else if (response.status == "error") {
                    var alert = $('#janrainSignInScreen .janrain-form-error:first');
                    alert.text(response.message);
                    alert.show();
                }
            });

            e.preventDefault();
        });
    };

    // Bind profile form to saveProfile() method
        $('.janrain-profile-form').submit(function(e) {
            // TODO: this becomes saveProfile()
            console.log($(this).serialize());
            $.post(saveProfileUrl, $(this).serialize(), function(response) {
                console.log("saveProfile", response);
                if (response.status == "success") {
                    $('#alert_placeholder').html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Success!</strong> Your profile has been successfully updated.</div>');
                    $('html, body').animate({ scrollTop: 0 }, 0);
                } else if (response.status == "error") {
                    var alert = $('#janrainEditProfileScreen .janrain-form-error:first');
                    alert.text(response.message);
                    alert.show();
                }
            });

            e.preventDefault();
        });

        // Bind forgotPassword form to forgotPassword() method
        $('.janrain-forgot-password').submit(function(e) {
            // TODO: this becomes forgotPassword()
            console.log($(this).serialize());
            $.post(forgotPasswordUrl, $(this).serialize(), function(response) {
                console.log("forgotPassword", response);
                if (response.status == "success") {
                    $('#janrainForgotPassword').modal('hide');
                    $('#janrainForgotPasswordSuccess').modal('show');
                } else if (response.status == "error") {
                    var alert = $('#janrainForgotPasswordScreen .janrain-form-error:first');
                    alert.text(response.message);
                    alert.show();
                }
            });

            e.preventDefault();
        });

        // Bind resetPassword form to resetPassword() method
        $('.janrain-reset-password-form').submit(function(e) {
            // TODO: this becomes forgotPassword()
            console.log($(this).serialize());
            $.post(resetPasswordUrl, $(this).serialize(), function(response) {
                console.log("resetPassword", response);
                if (response.status == "success") {
                    $('#janrainResetPassword').modal('hide');
                    $('#janrainResetPasswordSuccess').modal('show');
                } else if (response.status == "error") {
                    var alert = $('#janrainResetPassword .janrain-form-error:first');
                    alert.text(response.message);
                    alert.show();
                }
            });

            e.preventDefault();
        });

        // Bind resetPasswordRequestCode form to resetPasswordRequestCode() method
        $('.janrain-reset-password-request-code').submit(function(e) {
            // TODO: this becomes forgotPassword()
            console.log($(this).serialize());
            $.post(forgotPasswordUrl, $(this).serialize(), function(response) {
                console.log("forgotPassword", response);
                if (response.status == "success") {
                    $('#janrainResetPasswordRequestCode').modal('hide');
                    $('#janrainResetPasswordRequestCodeSuccess').modal('show');
                } else if (response.status == "error") {
                    var alert = $('#janrainResetPasswordRequestCode .janrain-form-error:first');
                    alert.text(response.message);
                    alert.show();
                }
            });

            e.preventDefault();
        });

    /*
    Add Janrain Event Handlers

    Connect event handlers to the Janrain Social Login widget for processing
    social authentication related events.
    */
    var addJanrainEventHandlers = function() {
        // An authentication event which, if successfull, includes the one-time
        // engage token that needs to be passed to the token URL on the server.
        janrain.events.onProviderLoginToken.addHandler(function(janrainResponse) {
            console.log("onProviderLoginToken", janrainResponse);
            $.post(signInUrl, {
                'token': janrainResponse.token
            }, function(response) {
                console.log('signIn', response);
                if (response.status == "success") {
                    refreshSessionState(response.data);
                    $('.janrain-modal').modal('hide');
                } else if (response.status == "error") {
                    if (response.data) {
                        for (var i=0; response.data.length; i++) {
                            console.log
                        }
                        //Response code 310 indicates the user does not exist in the database
                        //and the social registration form must be completed.
                        if (response.code == 310) {
                            $('.janrain-modal').modal('hide');
                            $('#socialRegFirstName').val(response.data.prereg_fields.firstName);
                            $('#socialRegLastName').val(response.data.prereg_fields.lastName);
                            $('#socialRegDisplayName').val(response.data.prereg_fields.displayName);
                            $('#socialRegEmail').val(response.data.prereg_fields.emailAddress);
                            $('#socialRegToken').val(janrainResponse.token);
                            $('#janrainSocialRegistrationScreen').modal('show');
                        }    
                    } else {
                        $('#janrainEngageError').text(response.message);
                        $('#janrainEngageError').show();
                    }
                }
                janrain.engage.signin.cancelLogin();
            });
        });
    };

    /*
    Initialize
    */
    var initialize = function() {
        // The 'hide' class is used on the initial page load but is replaced
        // with jQuery show()/hide() functionality here.
        $('.janrain-show-if-session,.janrain-error').hide().removeClass('hide');

    
        addBootstrapEventHandlers();
        addJanrainEventHandlers();
        refreshSession();

        //TODO: this becomes getParams()
        var params = window.location.search.replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this}.bind({}))[0];;
        
        if(params['code'] != null && params['code'] != undefined){
            $.post(resetPasswordUrl, {'code': params['code']}, function(response) {
                    console.log("verifyCode", response);
                    if (response.status == "success") {
                        $('#resetPasswordToken').val(response.token);
                        $('#janrainResetPassword').modal('show');
                    } else if (response.status == "error") {
                       $('#janrainResetPasswordRequestCode').modal('show');
                    }
            });
        }
        
        

        //
    };

    /*
    Refresh Session

    Request session data from server and refresh UI state.
    */
    var refreshSession = function() {
        $.get(sessionDataUrl, function(response) {
            console.log('refreshSession', response)
            refreshSessionState(response.data);
        });
    };

    /*
    Refresh Session State

    Update the Bootstrap UI elements which indicate the state of the user's
    signed in/signed out state.
    */
    var refreshSessionState = function(sessionData) {
        if (sessionData && sessionData.uuid) {
            $('.janrain-display-name').text(sessionData.displayName);
            $('.janrain-hide-if-session').hide();
            $('.janrain-show-if-session').show();

            if(window.location.pathname == "/bootstrap-php/editProfile.html"){
                getProfile(sessionData.token);
            }
        } else {
            $('.janrain-display-name').text("User");
            $('.janrain-hide-if-session').show();
            $('.janrain-show-if-session').hide();
        }
    };

    /*
    Load Profile

    Request profile data from the server
    */
    var getProfile = function(token) {
        $.post(profileDataUrl, {
            'token': token
        }, function(response) {
            console.log('getProfile', response)
            if(response.status == 'success'){
                $('#profileFirstName').val(response.data.result.givenName);
                $('#profileLastName').val(response.data.result.familyName);
                $('#profileDisplayName').val(response.data.result.displayName);
                $('#profileEmail').val(response.data.result.email);
                $('#profileGender').val(response.data.result.gender);
                $('#cover').fadeOut(1000);
            }
        });
    };

    /*
    Refresh Session

    Tell server to end session and update UI state.
    */
    var signOut = function() {
        $.get(signOutUrl, function(response) {
            console.log(response);
            refreshSessionState(null);
        });
    };

    return {
        // public properties
        signInUrl: signInUrl,
        signOutUrl: signOutUrl,
        sessionDataUrl: sessionDataUrl,

        // public methods
        initialize: initialize,
        refreshSession: refreshSession,
        signOut: signOut
    };

})(jQuery, janrain);

function janrainWidgetOnload() {
    console.log("janrainWidgetOnload", janrain.settings);
    JanrainBootstrap.initialize();
}