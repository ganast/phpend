<?php

// add custom API endpoints, errors and HTTP error codes here...

// access levels: 0 = everyone, 1 = user, 2 = owner, 3 = admin...

/**
 * 
 */
$phpend_api_endpoints['GET'] += [
	'/magic/{num:\d+}'						=> ['api_do_some_magic',		['access' => 0]]
];

/**
 * 
 */
$phpend_api_errors += [
    'NEED_MORE_MANA'                    	=> [100, 'Need more mana'],
];

/**
 * 
 */
$phpend_api_http_error_codes += [
    'NEED_MORE_MANA'                    	=> 422
];
