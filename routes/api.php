<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    // Auth routes which need api:auth middleware implement it inside their controller.
    $api->group(['prefix' => 'auth','namespace' => 'App\Api\V1\Controllers'], function(Router $api) {
        /*
         *  Normal Auth Login / SignUp
         */
        $api->post('signup', 'SignUpController@signUp');
        $api->post('login', 'LoginController@login');

        /*
         * Facebook Login / SignUp
         */
        $api->get('/loginfacebook', 'FacebookAuthController@redirect');
        $api->get('/callbackfb', 'FacebookAuthController@callback');

        /*
         * Forget/Reset Password
         */
        $api->post('recovery', 'ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'ResetPasswordController@resetPassword');

        /**
         * User Stuff
         */
        $api->post('logout', 'LogoutController@logout');
        $api->post('refresh', 'RefreshController@refresh');
        $api->get('me', 'UserController@me');
        $api->post('validatetoken','RefreshController@validateToken');
    });

    // Edit User Routes
    $api->group(['prefix' => 'auth','namespace' => 'App\Api\V1\Controllers','middleware' => 'auth:api'], function (Router $api) {
        /*
         * EditUserController
         */
        $api->put('edit/password','EditUserController@modifyPassword');
        $api->put('edit/email','EditUserController@modifyEmail');
        $api->put('edit/profilepic','EditUserController@modifyProfilePic');
    });
});