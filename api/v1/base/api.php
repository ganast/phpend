<?php

// access levels: 0 = everyone, 1 = user, 2 = owner, 3 = admin...

/**
 * 
 */
$phpend_api_endpoints = [

	// fetch or request stuff...
	'GET' => [

		// test...
		'/test'										=> ['phpend_api_test',					['access' => 0]],

		// fetches backend and API info, can be used for pinging the backend...
		'/info'										=> ['phpend_api_info',					['access' => 0]],

		// fetches data for all registered users...
		'/users'									=> ['phpend_api_get_users',				['access' => 3]],

		// fetches data for a single registered user...
		'/user/{email}'								=> ['phpend_api_get_user',				['access' => 2]],

		// requests that an account activation email be sent to a registered user's
		// email...
		'/user/{email}/status'						=> ['phpend_api_request_verify',		['access' => 0]],

		// requests that a password reset email be sent to a registered user's email...
		'/user/{email}/auth/pass'					=> ['phpend_api_request_reset',			['access' => 0]]
	],

	// add or activate stuff...
	'POST' => [

		// registers a new user with password...
		'/user/{email}'								=> ['phpend_api_register_user',			['access' => 0]],

		// activates a registered user...
		'/user/{email}/status'						=> ['phpend_api_activate_user',			['access' => 3, 'action' => 'verify']],

		// authenticates a registered user using some supported authentication method...
		'/user/{email}/auth'						=> ['phpend_api_authenticate_user',		['access' => 0]]
	],

	// modify stuff...
	'PUT' => [

		// Updates a registered user's profile data...
		'/user/{email}'								=> ['phpend_api_update_user',			['access' => 2]],

		// Updates a registered user's status...
		'/user/{email}/status'						=> ['phpend_api_update_user_status',	['access' => 3]],

		// updates a registered user's password...
		'/user/{email}/auth/pass'					=> ['phpend_api_upate_user_password',	['access' => 2, 'action' => 'reset', 'logout' => true]]
	],

	// remove or deactivate stuff...
	'DELETE' => [

		// deletes a registered user...
		'/user/{email}'								=> ['phpend_api_delete_user',			['access' => 2, 'logout' => true]],

		// deactivates a registered user...
		'/user/{email}/status'						=> ['phpend_api_deactivate_user',		['access' => 3, 'logout' => true]],

		// deauthenticates a registered user...
		'/user/{email}/auth'						=> ['phpend_api_deauthenticate_user',	['access' => 2, 'logout' => true]]
	]
];

/**
 * 
 */
$phpend_api_errors = [

    // this is to report errors from which the backend might actually recover (i.e.,
	// later, e.g., temporary loss ofconnectivity with a database server or bad
	// configuration), not uncaught errors that are most likely bugs, those should
	// be propagated to client code and handled appropriately (e.g., be reported,
	// trigger a graceful failure, etc.) or not handled at all (i.e., lead to a
	// fatal failure) so that they can be identified andfixed...
    'OOPS'											=> [99, 'Something went wrong, please try again later'],

	'INVALID_CREDENTIALS'							=> [11, 'Invalid credentials'],

    'EMAIL_IN_USE'									=> [12, 'Email address in use'],

    'ACCOUNT_INACTIVE'								=> [13, 'Account inactive'],

    'ALIAS_IN_USE'									=> [14, 'Alias already taken'],

	'BAD_REQUEST'									=> [90, 'Bad request'],

    'UNAUTHORIZED'									=> [91, 'Unauthorized'],

    'FORBIDDEN'										=> [93, 'Forbidden'],
];

/**
 * 
 */
$phpend_api_http_error_codes = [

    // only need to specify codes for errors not meant to be reported as 500 - Internal Server Errors...

    'OOPS'											=> 503,

	// 'INVALID_CREDENTIALS'						=> 401,
    'INVALID_CREDENTIALS'							=> 200,

	// 'EMAIL_IN_USE'								=> 409,
    'EMAIL_IN_USE'									=> 200,

    'ACCOUNT_INACTIVE'								=> 200,

    // 'ALIAS_IN_USE'								=> 409,
    'ALIAS_IN_USE'									=> 200,

	'BAD_REQUEST'									=> 400,

	'UNAUTHORIZED'									=> 401,

	'FORBIDDEN'										=> 403
];
