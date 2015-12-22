Eric's WIP Widget Project Notes 
===================
1. Built using SE Demo site Capture app using 'standard_newer' flow with version '4b1c146f-b22d-4f79-a08b-01702b883715'
2. The API client 'Bootstrap Demo Client' is configured and ready for use if the project is located at localhost/bootstrap-php.'
3. If you want to install the project in another location, the following changes must be made:
    a. The following line in janrain-bootstrap.js needs to updated with the proper path: if(window.location.pathname == "/bootstrap-php/editProfile.html"){
    b. config.php file needs to be updated with the correct forgot password URL
    c. Create a new API client, and add a client setting to specify the correct forgot password URL
4. Code is set to pull in Eric's demo Engage app 'eshrine'
    a. If you want to use your own Engage app, you will need to create a new API client as described below and set the API client settings to use your Engage app.
    b. The janrain-bootstrap.js file will also be updated to source the correct load files from rpxnow.com.




Janrain PHP Bootstrap Example
=============================

This example project uses [Janrain's](http://janrain.com) Social Login widget
and [Bootstrap](http://getbootstrap.com) to create a sign-in and registration UI
powered by PHP and Janrain APIs on the back-end.

![Screenshot](https://github.com/JanrainMicah/janrain-php-bootstrap-example/blob/master/screenshot.png)

Prerequisites
-------------

* Janrain Social Login (aka Engage) application
* Janrain Registration (aka Capture) application
* PHP web server or local environment (eg. LAMP,
  [MAMP](https://www.mamp.info/en/), [WAMP](http://www.wampserver.com/en/), etc.)


Getting Started
---------------

1. Create a new Janrain *Login* API client using the
   [`/clients/add`](http://developers.janrain.com/rest-api/methods/api-client-configuration/clients/add-3/)
   API call:

        curl -X POST \
        -d client_id=APPLICATION_OWNER_CLIENT_ID \
        -d client_secret=APPLICATION_OWNER_CLIENT_SECRET \
        -d description='Native API examples login client' \
        -d features='["login_client"]' \
        https://YOUR_APP.janraincapture.com/clients/add

2. Add the `default_flow_name` setting to the login client you created in step
   1 (The flow name is usually "standard" but check with your Janrain
   representative if in doubt):

        curl -X POST \
        -d client_id=APPLICATION_OWNER_CLIENT_ID \
        -d client_secret=APPLICATION_OWNER_CLIENT_SECRET \
        -d for_client_id=LOGIN_CLIENT_ID_FROM_STEP_1 \
        -d key=default_flow_name \
        -d value=standard \
        https://YOUR_APP.janraincapture.com/settings/set

3. Add the `default_flow_version` setting to the login client you created in
   in step 1 (The flow version must be provided by your Janrain representative):

        curl -X POST \
        -d client_id=APPLICATION_OWNER_CLIENT_ID \
        -d client_secret=APPLICATION_OWNER_CLIENT_SECRET \
        -d for_client_id=LOGIN_CLIENT_ID_FROM_STEP_1 \
        -d key=default_flow_version \
        -d value=497f2277-a8ca-418e-a6dd-e7d30fabe7df \
        https://YOUR_APP.janraincapture.com/settings/set

4. [Download the source code](https://github.com/JanrainMicah/janrain-php-bootstrap-example/archive/master.zip)
   or fork and clone this repository.

5. Unzip the files into your web server root. For example:

        unzip janrain-php-bootstrap-example-master.zip -d /var/www

6. Rename `config.example.php` to `config.php`.

7. Add the client ID and secret for login client you created in step 1 to the
   `config.php` file.

8. Navigate to the project in your web browser. Eg.
   [`http://localhost/janrain-php-bootstrap-example-master/`](http://localhost/janrain-php-bootstrap-example-master/)