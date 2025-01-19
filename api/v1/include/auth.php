<?php

use PHPMailer\PHPMailer\PHPMailer;

/**
 * TODO
 * 
 */
function phpend_auth_login_user(string $email, string $password): string {

    try {

        // inactive accounts cannot login...
        if (!phpend_data_is_user_active($email)) {
            throw new AuthException(AuthException::ERROR_ACCOUNT_INACTIVE);
        }

		// TODO
        if (!phpend_data_auth_user($email, $password)) {
            throw new AuthException(AuthException::ERROR_INVALID_CREDENTIALS);
        }

		// TODO
		$jwt = generate_jwt(PHPEND_TOKEN_SECRET, PHPEND_TOKEN_ISSUER, PHPEND_AUTH_TOKEN_EXPIRATION,
			['user' => $email]);

		// TODO
		phpend_data_issue_item($jwt, 'token', $email);
    }
	catch (DataModelException $ex) {
		throw new AuthException(AuthException::ERROR_IN_DATAMODEL, $ex);
	}

	return $jwt;
}

/**
 * TODO
 * 
 */
function phpend_auth_logout_user(string $email) {

    try {

		// TODO
        phpend_data_revoke_items($email, 'token');
    }
    catch (DataModelException $ex) {
        throw new AuthException(AuthException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_auth_consume_action(string $token) {

    // no need to revoke an already invalid token...
    if (!validate_jwt($token, PHPEND_TOKEN_SECRET, PHPEND_TOKEN_ISSUER)) {
        return;
    }

    try {
        phpend_data_revoke_item($token, 'token');
    }
    catch (DataModelException $ex) {
        throw new AuthException(AuthException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_auth_send_action_email(string $action, array $params): void {

    if (!isset($action)) {
        throw new AuthException(AuthException::ERROR_INVALID_ARGUMENT);
    }

    switch ($action) {

        case 'verify':
            $webapp_path = PHPEND_WEBAPP_VERIFY_PATH;
            break;

        case 'reset':
            $webapp_path = PHPEND_WEBAPP_RESET_PATH;
            break;

        default:
            throw new AuthException(AuthException::ERROR_INVALID_ACTION);
    }

	$token = generate_jwt(PHPEND_TOKEN_SECRET, PHPEND_TOKEN_ISSUER, PHPEND_ACTION_TOKEN_EXPIRATION,
		['user' => 'SYSTEM', 'action' => $action] + $params);

	// TODO
	phpend_data_issue_item($token, 'token', $params['target']);
	
	$url = PHPEND_URL_ORIGIN . PHPEND_URL_SUBDIR . $webapp_path . '?' . PHPEND_WEBAPP_ACTION_PARAM . '=' . $token;

	if (PHPEND_DEBUG) {
		echo $url;
	}

	// send email...
	if (!phpend_util_send_email($params['target'], $action, ['URL' => $url])) {
		throw new AuthException(AuthException::ERROR_SENDING_EMAIL);
	}
}

/**
 * TODO
 * 
 */
function phpend_auth_validate_token(string $token, array $claims = null): array {

    try {

        // revoked accounts cannot be used at all...
        if (!phpend_data_is_issued($token, 'token')) {
            return [];
        }

		// invalid tokens cannot be used at all...
		// (note that, here, the term "invalid" is to be interpreted in the context of
		// the phpend platform, that is, also taking under account the issuer and
		// specified user, not just in terms of generic JWT validity)...
		try {
			$payload = validate_jwt($token, PHPEND_TOKEN_SECRET, PHPEND_TOKEN_ISSUER, $claims);
		}
		catch (Exception) {
			return [];
		}

		// all token types must specify a user...
		if (!isset($payload['user'])) {
			return [];
		}

		// check if this is an auth or an action token...
		if (isset($payload['action'])) {
			
			// this is an action token, no requirements at this level, the action handler
			// will do the rest (including checking for a valid action)...
			return ['user' => $payload['user'], 'action' => $payload['action']];
		}
		else {

			// this is an auth token (the default case), must specify an active user...
			if (!phpend_data_is_user_active($payload['user'])) {
				return [];
			}

			return ['user' => $payload['user']];
		}
    }
    catch (DataModelException $ex) {
        throw new AuthException(AuthException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * An exception type denoting errors in authentication and authorization
 * operations.
 */
class AuthException extends Exception {

    public const ERROR_INVALID_ARGUMENT = 1;
    public const ERROR_IN_DATAMODEL = 2;
    public const ERROR_INVALID_CREDENTIALS = 3;
    public const ERROR_ACCOUNT_INACTIVE = 4;
    public const ERROR_ACCOUNT_DOES_NOT_EXIST = 5;
    public const ERROR_INVALID_ACTION = 6;
    public const ERROR_SENDING_EMAIL = 7;

    private static $messages = [
        self::ERROR_INVALID_ARGUMENT => 'Invalid argument',
        self::ERROR_IN_DATAMODEL => 'Datamodel error',
        self::ERROR_INVALID_CREDENTIALS => 'Invalid credentials',
        self::ERROR_ACCOUNT_INACTIVE => 'Account inactive',
        self::ERROR_ACCOUNT_DOES_NOT_EXIST => 'Account does not exist',
        self::ERROR_INVALID_ACTION => 'Invalid action',
        self::ERROR_SENDING_EMAIL => 'Could not send email'
    ];

    public function __construct(int $code, Exception $previous = null) {
        $message = '';
        if (array_key_exists($code, self::$messages)) {
            $message = self::$messages[$code];
        }
        parent::__construct($message, $code, $previous);
    }
}
