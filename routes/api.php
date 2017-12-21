<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth','namespace' => 'App\Api\V1\Controllers'], function(Router $api) {
        /*
         * Normal Auth Login / SignUp
         */
        $api->post('/signup', 'SignUpController@signUp');
        $api->post('/login', 'LoginController@login');

        /*
         * Facebook Login / SignUp
         */
        $api->post('/loginfacebook', 'FacebookAuthController@login');

        /*
         * Forget/Reset Password
         */
        $api->post('/recovery', 'ForgotPasswordController@sendResetEmail');
        $api->post('/reset', 'ResetPasswordController@resetPassword');

        /**
         * User Stuff
         */
        $api->post('/logout', 'LogoutController@logout');
        $api->post('/refresh', 'RefreshController@refresh');
        $api->get('/me', 'UserController@me');
        $api->post('/validatetoken','RefreshController@validateToken');
    });

    $api->group(['namespace' => 'App\Api\V1\Controllers'], function(Router $api) {
        /**
         * Verify stuff for user
         */
        $api->post('/verifyemail', 'UserController@verifyEmail');
        $api->post('/verifyphone', 'UserController@verifyPhone');
        $api->post('/verifyusername', 'UserController@verifyUsername');
    });

    $api->group(['prefix' => 'auth','namespace' => 'App\Api\V1\Controllers','middleware' => 'auth:api'], function (Router $api) {
        /*
         * EditUserController
         */
        $api->put('/edit/password','EditUserController@modifyPassword');
        $api->put('/edit/email','EditUserController@modifyEmail');
        $api->put('/edit/profilepic','EditUserController@modifyProfilePicture');
        $api->put('/edit/coverpic','EditUserController@modifyCoverPicture');
        $api->put('/edit/user','EditUserController@edit');
    });

    $api->group(['namespace' => 'App\Http\Controllers','middleware' => 'auth:api'], function (Router $api) {
        /**
         * Conversation Controller
         */
        $api->get('/conversations/search/{keyword}','ConversationController@search');
        $api->resource('/conversations','ConversationController');

        /**
         * Notification Controller
         */
        $api->get('/notifications','NotificationController@index');
        $api->post('/notifications','NotificationController@create');
        $api->delete('/notification/{id}/delete','NotificationController@delete');
        $api->delete('/notifications/clear','NotificationController@clear');
    });
});