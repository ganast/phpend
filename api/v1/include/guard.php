<?php

/**
 * TODO
 *
 * @param string $handler TODO
 * @param array $vars TODO
 * @param array $params TODO
 * @param array $options TODO
 *
 * @return array TODO
 */
function phpend_guard_invoke_handler(string $handler, array $vars, array $params, array $options): array {

	// only allow administrator access to handlers with unspecified access...
    $access = isset($options['access']) ? $options['access'] : 3;

	// public endpoints need no guarding...
    if ($access !== 0) {

		// retrieve auth token...
		if (null === $token = get_auth_token_from_request()) {
			throw new HTTPAPIErrorException('UNAUTHORIZED');
		}

		// token needs to be a valid user auth token...
		if ([] === $payload = phpend_auth_validate_token($token, ['user' => '*'])) {
			throw new HTTPAPIErrorException('UNAUTHORIZED');
		}

		// furthermore, if the user is the SYSTEM, the token must specify a specific
		// action and the handler must be properly configured for that action...
		if (isset($payload['action'])) {

			// this is a valid action token, therefore, it must be revoked upon first use by
			// definition, that is, right now, unless in dev mode...
			if (!PHPEND_DEBUG) {
				phpend_auth_consume_action($token);
			}

			// check if the handler is properly configured for the action in the token...
			if (!isset($options['action']) || $options['action'] !== $payload['action']) {
				throw new HTTPAPIErrorException('FORBIDDEN');
			}
		}

		// extract the operator...
		$bearer = $payload['user'];

		// admin bearers and system-issued tokens need no guarding...
		if (!phpend_is_admin($bearer) && $bearer !== 'SYSTEM') {

			switch ($access) {

				// shared resource, no special requirements other than a valid auth token...
				case 1:
					break;

				// private resource, user in token must be resource owner...
				case 2:

					// the user in the auth token must be the resource owner...
					if ($vars['email'] != $bearer) {
						throw new HTTPAPIErrorException('FORBIDDEN');
					}

					break;

				// admin-only resource, user in token must be administrator...
				case 3:

					// the user in the auth token must be an administrator...
					throw new HTTPAPIErrorException('FORBIDDEN');

					break;

				// invalid access level...
				default:

					// throw error to indicate a bad configuration...
					throw new HTTPAPIErrorException('OOPS');
			}
		}
	}

	// check if the handler is configured to force-logout the resource owner...
	if (isset($options['logout'])) {

		// force-logout should only be set-up for owned resources...
		if (isset($vars['email'])) {

			// mark the resource owner for logout...
			phpend_auth_logout_user($vars['email']);
		}
		else {
			// throw error to indicate a bad configuration...
			throw new HTTPAPIErrorException('OOPS');
		}
	}

    // handler is now, and only now, safe to invoke...

    return $handler($vars, $params, $options);
}
