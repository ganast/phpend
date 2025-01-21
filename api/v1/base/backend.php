<?php

/*
 * A layer exposing the entire set of operations available by phpend. These
 * consist of the following:
 * 
 * - get_users: returns a list of all matching user profiles
 * - get_user_profile: returns a user's profile data
 * - is_admin: checks if a user has admin-level access rights
 * - register_user: registers a user
 * - is_user_registered: checks if a user is already registerd
 * - is_user_alias_registered: checks if a user alias is already registered
 * - is_user_active: checks if a user is active
 * - set_user_active: activates or deactivates a user
 * - (TODO) ban_user: stores a user in the list of banned users and logs them
 *   out
 * - (TODO) set_user_access: sets a user's access rights mask
 * - (TODO) update_user: updates a user's profile information
 * - (TODO) delete_user: deletes a user account and logs them out
 * 
 * Note that not all of the above are necessarily exposed through the phpend
 * API, i.e., some of the above may only by available through local admin tools,
 * check the relevant documentation for more information.
 * 
 * Both the password reset and account activation emails contain links to a web
 * app that retrieves the one-time token from the URL, possibly gathers more
 * information via a web UI (e.g., a new password) and issues the appropriate
 * API call bearing the one-time token for authorization. The web app is also
 * responsible for presenting the results in a proper fashion as in all cases of
 * API consumption by web or other frontends.
 * 
 * This layer does not provide arbitrary credential validation functionality as
 * such functionality only has a point on a backend level as part of other, more
 * complex functionality supporting the backend's intended usage workflow, such
 * as user login, logout and authorization (note that even the latter is not
 * actually handled by the backend itself but by the various gateways to it,
 * such as the HTTP API layer and, potentially in the future, a range of local
 * admin tools).
 * 
 * TODO
 */

/**
 * TODO
 * 
 */
function phpend_get_users(string $filter): array {
    try {
        return phpend_data_get_users($filter);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

 /**
 * TODO
 * 
 */
function phpend_get_user_profile(string $email): array {
    try {
        return phpend_data_get_user_data($email);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

 /**
 * TODO
 * 
 */
function phpend_update_user_profile(string $email, array $changes) {
    try {
        return phpend_data_update_user_data($email, $changes);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_is_admin(string $email): bool {
    try {
        return phpend_data_is_admin($email);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_register_user(string $email, string $password, string $alias, string $name_first,
    string $name_last, string $organization, bool $activate = false): void {

    try {
        phpend_data_register_user($email, $password, $alias, $name_first, $name_last, $organization, $activate);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_delete_user(string $email): void {

    try {
        phpend_data_delete_user($email);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_is_user_registered(string $email): bool {
    try {
        return phpend_data_is_user_registered($email);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_is_user_alias_registered(string $alias): bool {
    try {
        return phpend_data_is_user_alias_registered($alias);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_is_user_active(string $email): bool {
    try {
        return phpend_data_is_user_active($email);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * TODO
 * 
 */
function phpend_set_user_active(string $email, bool $active) {
    try {
        phpend_data_set_user_active($email, $active);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
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
		throw new BackendException(BackendException::ERROR_SENDING_EMAIL, $ex);
	}
}

/**
 * TODO
 *
 * @param [type] $email TODO
 * @param [type] $password TODO
 *
 * @return void TODO
 */
function phpend_update_user_password(string $email, string $password) {

    try {
        phpend_data_set_user_password($email, $password);
    }
    catch (DataModelException $ex) {
        throw new BackendException(BackendException::ERROR_IN_DATAMODEL, $ex);
    }
}

/**
 * An exception type denoting errors in backend operations.
 */
class BackendException extends Exception {

    public const ERROR_IN_DATAMODEL = 1;
    public const ERROR_SENDING_EMAIL = 2;

    private static $messages = [
        BackendException::ERROR_IN_DATAMODEL => 'Datamodel error',
        BackendException::ERROR_SENDING_EMAIL => 'Could not send email'
    ];

    public function __construct(int $code, Exception $previous = null) {
        $message = '';
        if (array_key_exists($code, BackendException::$messages)) {
            $message = BackendException::$messages[$code];
        }
        parent::__construct($message, $code, $previous);
    }
}
