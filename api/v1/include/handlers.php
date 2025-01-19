<?php

/*
 * TODO
 * 
 * TODO: Consider merging vars and params (or not). Also consider passing
 * options as well in order to be able to reuse functions in the API handler
 * definitions parameterizing their function through options, e.g., a single
 * handler for sending action emails parameterized by action name.
 */

/**
 * Performs a series of tests for the entire API in terms of callability,
 * request/response syntax validation, etc. Does not guarantee correct results.
 * Should be gone in production.
 * 
 * Expects no input.
 */
function phpend_api_test(array $vars, array $params, array $options): array {

	try {
		
		phpend_data_set_user_active('ganast@ganast.com', true);

		$token = phpend_auth_login_user('ganast@ganast.com', 'test');

		// phpend_data_revoke($token, 'token');

		$r = [

			'is_user_registered' => phpend_is_user_registered('ganast@ganast.com'),

			'is_user_alias_registered' => phpend_is_user_alias_registered('madanasta'),
			'is_user_active' => phpend_data_is_user_active('ganast@ganast.com'),
			'get_user_profile' => phpend_get_user_profile('ganast@ganast.com'),
			'auth_user' => phpend_data_auth_user('ganast@ganast.com', 'test'),
			'login_user' => $token,
			'is_user_logged_in' => phpend_auth_validate_token($token, ['user' => 'ganast@ganast.com']),
			'is_revoked' => phpend_data_is_issued($token, 'token'),
			'is_admin' => phpend_is_admin('ganast@ganast.com'),
			'vars' => $vars,
			'params' => $params,
			'options' => $options
		];
		return $r;
	}
	catch (Exception $ex) {
		echo '<pre>';
		echo get_class($ex);
		echo '<br>--------------------------------------------------------------------------------<br>';
		echo "Code: {$ex->getCode()}";
		echo '<br>--------------------------------------------------------------------------------<br>';
		echo "Message: {$ex->getMessage()}";
		echo '<br>--------------------------------------------------------------------------------<br>';
		echo $ex->getPrevious();
		echo '<br>--------------------------------------------------------------------------------<br>';
		echo '</pre>';
		exit();
	}
}

/**
 * Returns information about the API and the host environment. Can be used as
 * a ping or heartbeat response if it is ok, or desired, for the respective
 * request to also account for some minimal response content processing.
 *
 * Expects no input.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 * 
 * @return array
 */
function phpend_api_info(array $vars, array $params, array $options): array {

	$version = '1.0.1.20240710';

	$hostname = gethostname() ?? '';
	if (!$hostname) {
		$hostname = '';
	}

	$timeazone = date_default_timezone_get();

	$now = date("D M j G:i:s T Y");

	return ['ver' => $version, 'host' => $hostname, 'tz' => $timeazone, 'now' => $now];
}

/**
 * Returns user data for all matching users.
 *
 * Expects no vars.
 * Expects params: s (optional), must be a valid SQL WHERE clause without the
 * WHERE keyword and without a trailing semicolon.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 *
 * @return array
 * 
 * @throws HTTPAPIErrorException
 */
function phpend_api_get_users(array $vars, array $params, array $options): array {

	$s = $params['s'] ?? '';

	try {
		return phpend_get_users($s);
	}
	catch (BackendException $ex) {
        throw new HTTPAPIErrorException('OOPS');
	}

}

/**
 * Returns user data for the user identified by the specified email.
 * 
 * Expects vars: email.
 * Expects no params.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 * 
 * @return array
 * 
 * @throws HTTPAPIErrorException
 */
function phpend_api_get_user(array $vars, array $params, array $options): array {

	if (!$vars['email']) {
		throw new HTTPAPIErrorException('BAD_REQUEST');
	}

	$email = $vars['email'];

    try {
		return phpend_get_user_profile($email);
	}
	catch (BackendException $ex) {
        throw new HTTPAPIErrorException('OOPS');
    }
}

/**
 * Registers a new user with password.
 * 
 * Expects vars: email.
 * Expects params: password, alias, name_first, name_last, organization.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 * 
 * @return array
 * 
 * @throws HTTPAPIErrorException
 */
function phpend_api_register_user(array $vars, array $params, array $options): array {

	if (empty($params['password']) || empty($params['alias']) || empty($params['name_first']) ||
		empty($params['name_last']) || empty($params['organization'])) {

		throw new HTTPAPIErrorException('BAD_REQUEST');
	}

	$email = $vars['email'];

    $password = $params['password'];
	$alias = $params['alias'];
    $name_first = $params['name_first'];
    $name_last = $params['name_last'];
    $organization = $params['organization'];

    try {

		if (phpend_is_user_registered($email)) {
            throw new HTTPAPIErrorException('EMAIL_IN_USE');
        }

		if (phpend_is_user_alias_registered($alias)) {
            throw new HTTPAPIErrorException('ALIAS_IN_USE');
        }

		phpend_register_user($email, $password, $alias, $name_first, $name_last, $organization,
			PHPEND_SKIP_ACTIVATION);
	}
	catch (BackendException $ex) {
		throw new HTTPAPIErrorException('OOPS');
	}

	try {
		if (!PHPEND_SKIP_ACTIVATION) {
			phpend_auth_send_action_email('verify', ['target' => $email]);
		}
	}
	catch (AuthException $ex) {
		throw new HTTPAPIErrorException('OOPS');
	}

    return [];
}

/**
 * Performs a user login with password.
 * 
 * Expects vars: email.
 * Expects params: method, value.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 * 
 * @return array
 * 
 * @throws HTTPAPIErrorException
 */
function phpend_api_authenticate_user(array $vars, array $params, array $options): array {

	if (empty($params['password'])) {
		throw new HTTPAPIErrorException('BAD_REQUEST');
	}

	if (empty($params['method'])) {
		$method = "password";
	}

	$email = $vars['email'];

	$password = $params['password'];

	switch ($method) {

		case 'password':

			try {
				return ['auth' => phpend_auth_login_user($email, $password)];
			}
			catch (AuthException $ex) {

				switch ($ex->getCode()) {
	
					case AuthException::ERROR_INVALID_CREDENTIALS:
						throw new HTTPAPIErrorException(('INVALID_CREDENTIALS'));
	
					case AuthException::ERROR_ACCOUNT_DOES_NOT_EXIST:
						throw new HTTPAPIErrorException(('INVALID_CREDENTIALS'));

					case AuthException::ERROR_ACCOUNT_INACTIVE:
						throw new HTTPAPIErrorException(('ACCOUNT_INACTIVE'));

					default:
						throw new HTTPAPIErrorException('OOPS');
				}
			}

		default:
			throw new HTTPAPIErrorException('BAD_REQUEST');
	}
}

/**
 * Logs a user out.
 * 
 * Expects vars: email.
 * Expects no params.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 * 
 * @return array
 * 
 * @throws HTTPAPIErrorException
 */
function phpend_api_deauthenticate_user(array $vars, array $params, array $options): array {

	$email = $vars['email'];

	try {
		phpend_auth_logout_user($email);
	}
	catch (AuthException $ex) {
		// silence...
	}

	return [];
}

/**
 * Sends an email with an account verification link to a user.
 * 
 * Expects vars: email.
 * Expects no params.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 * 
 * @return array
 * 
 * @throws HTTPAPIErrorException
 */
function phpend_api_request_verify(array $vars, array $params, array $options): array {

	$email = $vars['email'];

    try {
		phpend_auth_send_action_email('verify', ['target' => $email]);
	}
	catch (AuthException $ex) {
		// silence...
	}
	
	return [];
}

/**
 * Sends an email with a password reset link to a user.
 * 
 * Expects vars: email.
 * Expects no params.
 * 
 * @param array $vars
 * @param array $params
 * @param array $options
 * 
 * @return array
 * 
 * @throws HTTPAPIErrorException
 */
function phpend_api_request_reset(array $vars, array $params, array $options): array {

	$email = $vars['email'];

	try {
		phpend_auth_send_action_email('reset', ['target' => $email]);
	}
	catch (AuthException $ex) {
		// silence...
	}
	
	return [];
}

/**
 * Updates a registered user's password.
 *
 * Expects vars: email.
 * Expects params: password.
 * 
 * @param array $vars TODO
 * @param array $params TODO
 * @param array $options
 * 
 * @return array TODO
 */
function phpend_api_upate_user_password(array $vars, array $params, array $options): array {

	$email = $vars['email'];

	if (empty($params['password'])) {
		throw new HTTPAPIErrorException('BAD_REQUEST');
	}

	$password = $params['password'];

    try {
		phpend_update_user_password($email, $password);
	}
	catch (BackendException $ex) {
		throw new HTTPAPIErrorException('OOPS');
	}
	
	return [];
}

/**
 * Activates a registered user.
 * 
 * Expects vars: email.
 * 
 * @param array $vars TODO
 * @param array $params TODO
 * @param array $options
 * 
 * @return array TODO
 */
function phpend_api_activate_user(array $vars, array $params, array $options): array {

	$email = $vars['email'];

	try {
		phpend_set_user_active($email, true);
	}
	catch (BackendException $ex) {
		throw new HTTPAPIErrorException('OOPS');
	}
	
	// TODO: make it so that a welcome email is only sent to a user after the first
	// activation of the user's account and not after subsequent activations that
	// may take place after forced deactivation, this will most probably require a
	// "welcomed" flag or something like that in the user's profile which will be
	// set after account registration and cleared after sending a welcome email for
	// the first time and only then...

	try {
		$profile = phpend_get_user_profile($email);
		phpend_util_send_email($email, 'welcome', $profile['alias']);
	}
	catch (Exception $ex) {
		throw new HTTPAPIErrorException('ERROR_SENDING_EMAIL');
	}

	return [];
}

/**
 * Deactivates a registered user.
 * 
 * Expects vars: email.
 * 
 * @param array $vars TODO
 * @param array $params TODO
 * @param array $options
 * 
 * @return array TODO
 */
function phpend_api_deactivate_user(array $vars, array $params, array $options): array {

	$email = $vars['email'];

	try {
		phpend_set_user_active($email, false);
	}
	catch (BackendException $ex) {
		throw new HTTPAPIErrorException('OOPS');
	}
	
	return [];
}

/**
 * TODO
 *
 * @param array $vars TODO
 * @param array $params TODO
 * @param array $options TODO
 *
 * @return array TODO
 */
function phpend_api_delete_user(array $vars, array $params, array $options): array {
	throw new HTTPAPIErrorException('OOPS');
}

/**
 * TODO
 *
 * @param array $vars TODO
 * @param array $params TODO
 * @param array $options TODO
 *
 * @return array TODO
 */
function phpend_api_update_user(array $vars, array $params, array $options): array {
	throw new HTTPAPIErrorException('OOPS');
}

/**
 * TODO
 *
 * @param array $vars TODO
 * @param array $params TODO
 * @param array $options TODO
 *
 * @return array TODO
 */
function phpend_api_update_user_status(array $vars, array $params, array $options): array {
	throw new HTTPAPIErrorException('OOPS');
}
