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
         * Event Controller
         */
        $api->get('/events/newsfeed','EventController@newsfeed');
        $api->get('/events/search/{keyword}','EventController@search');
        $api->post('/event/invite','EventController@inviteUser');
        $api->post('/event/respond','EventController@answerToInvite');
        $api->resource('/events','EventController');

        /**
         * Friends Controller
         */
        $api->get('/friends','FriendsController@index');
        $api->get('/friends/{id}','FriendsController@show');
        $api->post('/friends/add','FriendsController@addFriend');
        $api->put('/friends/accept','FriendsController@acceptFriend');
        $api->put('/friends/refuse','FriendsController@refuseFriend');
        $api->delete('/friends/remove','FriendsController@removeFriend');
        $api->put('/friends/block','FriendsController@blockFriend');

        /**
         * UserStats Controller
         */
        $api->get('/stats/user','UserStatsController@userStats');
        $api->get('/stats/user/login','UserStatsController@loginStats');

        /**
         * Conversation Controller
         */
        $api->get('/conversations/search/{keyword}','ConversationController@search');
        $api->resource('/conversations','ConversationController');
    });
});