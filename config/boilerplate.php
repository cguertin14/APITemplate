<?php

return [

    // these options are related to the sign-up procedure
    'sign_up' => [

        // this option must be set to true if you want to release a token
        // when your user successfully terminates the sign-in procedure
        'release_token' => env('SIGN_UP_RELEASE_TOKEN', false),

        // here you can specify some validation rules for your sign-in request
        'validation_rules' => [
            'name' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'birthdate' => 'required|date_format:"Y-m-d"',
            'city' => 'required',
            'country' => 'required',
            'genre' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed|min:7|max:30|regex:/^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,30}$.*$/',
            'password_confirmation' => 'required|min:7|max:30'
        ]
    ],

    // these options are related to the login procedure
    'login' => [

        // here you can specify some validation rules for your login request
        'validation_rules' => [
            'email' => 'required|email',
            'password' => 'required'
        ]
    ],

    // these options are related to the edit password procedure
    'edit_password' => [
        'validation_rules' => [ //different:old_password
            'old_password' => 'required',
            'password' => 'required|confirmed|min:7|max:30|regex:/^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,30}$.*$/',
            'password_confirmation' => 'required|min:7|max:30'
        ]
    ],

    // these options are related to the edit email procedure
    'edit_email' => [
        'validation_rules' => [
            'email' => 'required|email',
            'password' => 'required'
        ]
    ],

    // these options are related to the edit profile pic procedure
    'edit_profile_pic' => [
        'validation_rules' => [
            'newpicture' => 'required|image'
        ]
    ],

    // these options are related to the password recovery procedure
    'forgot_password' => [

        // here you can specify some validation rules for your password recovery procedure
        'validation_rules' => [
            'email' => 'required|email'
        ]
    ],

    // these options are related to the password recovery procedure
    'reset_password' => [

        // this option must be set to true if you want to release a token
        // when your user successfully terminates the password reset procedure
        'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),

        // here you can specify some validation rules for your password recovery procedure
        'validation_rules' => [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]
    ]

];
