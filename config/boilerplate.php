<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Signup Fields
    |--------------------------------------------------------------------------
    |
    | Here, you can specify what fields you want to store for your user. The
    | AuthController@signup method will automatically search for current
    | request data fields using names that are contained in this array.
    |
    */
    'signup_fields' => [
        'name', 'email', 'password','role','role_id'
    ],

    /*
    |--------------------------------------------------------------------------
    | Signup Fields Rules
    |--------------------------------------------------------------------------
    |
    | Here you can put the rules you want to use for the validator instance
    | in the signup method.
    |
    */
    'signup_fields_rules' => [
        'name' => 'required|max:24',
        'email' => 'required|email|unique:cgl_api_users|max:50',
        'password' => 'required|min:6',
        'role' => 'required|min:5|max:20',
        'role_id' => 'required|numeric'
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles Rules
    |--------------------------------------------------------------------------
    |
    | Here you can put the roles you want to use for the user instance
    | in the signup method.
    |
    */
    'user_roles' => [
        'admin' => 'administrator',
        'warehouse' => 'warehouse',
        'supplier' => 'supplier'
    ],

    /*
    |--------------------------------------------------------------------------
    | Signup Token Release
    |--------------------------------------------------------------------------
    |
    | If this field is "true", an authentication token will be automatically
    | released after signup. Otherwise, the signup method will return a simple
    | success message.
    |
    */
    'signup_token_release' => env('API_SIGNUP_TOKEN_RELEASE', false),

    /*
    |--------------------------------------------------------------------------
    | Password Reset Token Release
    |--------------------------------------------------------------------------
    |
    | If this field is "true", an authentication token will be automatically
    | released after password reset. Otherwise, the signup method will return a
    | simple success message.
    |
    */
    'reset_token_release' => env('API_RESET_TOKEN_RELEASE', false),

    /*
    |--------------------------------------------------------------------------
    | Recovery Email Subject
    |--------------------------------------------------------------------------
    |
    | The email address you want use to send the recovery email.
    |
    */
    'recovery_email_subject' => env('API_RECOVERY_EMAIL_SUBJECT', true),

    /*
    |--------------------------------------------------------------------------
    | Reset link to redirect to reset password
    |--------------------------------------------------------------------------
    |
    | The link you want use to redirect to show the form to reset password.
    |
    */
    'recovery_reset_link' => env('API_RECOVERY_RESET_LINK', ''),

    /*
    |--------------------------------------------------------------------------
    | Reset link to redirect to login after successful password reset
    |--------------------------------------------------------------------------
    |
    | The link you want use to redirect to show the login form of the website.
    |
    */
    'login_link' => env('API_LOGIN_LINK', ''),
];
