<?php

/*
 * A layer exposing operations on an abstract data model for base functionality
 * in phpend, namely, user authentication, registration and  account management.
 * 
 * @author ganast (ganast@ganast.com)
 */

/**
 * TODO
 *
 * @param string $filter TODO
 *
 * @return array TODO
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_ACCESS if the backing datastore could not be accessed
 */
function phpend_data_get_users(string $filter): array {

    try {
		$sql = "
			SELECT email, alias, access, name_first, name_last, organization, created_on, last_login_on,
				last_login_ip, active
			FROM user
		";

		if ($filter) {
			$sql .= "WHERE $filter";
		}

		$db = phpend_get_pdo();
		return jm_db_pdo_fetch_all_rows($db, $sql);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 * Returns user data for a user identified by an email address.
 * 
 * @param string $email a valid email address
 * 
 * @return array user data for the user with the specified email address or an
 * empty array if no such user exists
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_ACCESS if the backing datastore could not be accessed
 */
function phpend_data_get_user_data(string $email): array {

	if (!$email) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

    try {
		$db = phpend_get_pdo();
		return jm_db_pdo_fetch_single_row($db, "
			SELECT email, alias, access, name_first, name_last, organization, created_on, last_login_on,
				last_login_ip, active
			FROM user
			WHERE email=?
		", [$email]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 * TODO
 *
 * @param string $email TODO
 * @param array $changes TODO
 *
 * @return void TODO
 */
function phpend_data_update_user_data(string $email, array $changes): void {

	if (!$changes) {
		return;
	}

	if (!$email) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

	// todo: move restricted user data fields to another table so that system-
	// managed user data and user-managed profile data are managed separately,
	// user can update entire user profile but cannot directly modify system-
	// managed data such as auth hash, status, etc., keep check to restrict
	// modification of email in user profile table, though...
	$restricted = [
		'EMAIL',
		'ACCESS',
		'CREATED_ON',
		'LAST_LOGIN_ON',
		'AUTH_HASH',
		'ACIVE'
	];

	foreach ($changes as $key => $value) {
		if (in_array(strtoupper($key), $restricted, false)) {
			throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
		}
	}

	$sql = "
	UPDATE user
	SET ";

	$params = array();
	$i = 0;
	foreach ($changes as $key => $value) {
		if ($i > 0) {
			$sql .= ",\n";
		}
		$sql .= "{$key}=:{$key}";
		$params[":{$key}"] = $value;
		$i++;
	}

	$sql .= "
	WHERE email=:email";
	$params[':email'] = $email;

    try {

		$db = phpend_get_pdo();

		$st = $db->prepare($sql);

		// todo: check return value of following call and decide how to report data
		// model update failure after a nonetheless successfull call...
		$st->execute($params);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_UPDATE, $ex);
	}

}


/**
 * Checks if a user has admin-level access rights.
 * 
 * @param string $email a valid email address
 * 
 * @return bool true if the user has admin-level access rights, false if not
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_ACCESS if the backing datastore could not be accessed
 */
function phpend_data_is_admin(string $email): bool {

	if (!$email) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

    try {
		$db = phpend_get_pdo();
		$rc = jm_db_pdo_fetch_single_cell($db, "
			SELECT access
			FROM user
			WHERE email=?
		", [$email]);
		return !empty($rc) && $rc[0] & 0b1;
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 * Check if a user identified by an email address is registered.
 * 
 * @param string $email a valid email address
 * 
 * @return bool true if a user with the specified email address is registered,
 * false if not
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_ACCESS if the backing datastore could not be accessed
 */
function phpend_data_is_user_registered(string $email): bool {

	if (!$email) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

    try {
		$db = phpend_get_pdo();
		return jm_db_pdo_is_non_zero($db, "
			SELECT COUNT()
			FROM user
			WHERE email=?
		", [$email]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 *  Check if a user with an alias is registered.
 * 
 * @param string $alias a valid alias
 * 
 * @return bool true if a user with the specified alias is registered, false if
 * not
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_ACCESS if the backing datastore could not be accessed
 */
function phpend_data_is_user_alias_registered(string $alias): bool {

	if (!$alias) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

    try {
		$db = phpend_get_pdo();
		return jm_db_pdo_is_non_zero($db, "
			SELECT COUNT(*)
			FROM user
			WHERE alias=?
		", [$alias]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 * Register a new user with the specified credentials and account data.
 * 
 * @param string $email a valid email address, used to uniquely identify a user
 * 
 * @param string $password a valid password
 * 
 * @param string $alias a valid alias, used to uniquely present a user to others
 *  
 * @param string $name_first a valid first name
 *  
 * @param string $name_last a valid last name
 *  
 * @param string $organization a valid organization title
 *  
 * @param bool $activate the new user account will be active after registration
 * if true, inactive if false
 *  
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email, password, alias, first name
 *   or last name were invalid
 * - ERROR_DATASTORE_UPDATE if the backing datastore could not be updated
 */
function phpend_data_register_user(string $email, string $password, string $alias, string $name_first,
	string $name_last, string $organization, bool $activate = false): void {
    
	if (!$email || !$password || !$alias || !$name_first || !$name_last) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

    try {

		$db = phpend_get_pdo();

		$st = $db->prepare("
			INSERT INTO user(email, alias, name_first, name_last, organization, created_on, auth_hash, active)
			VALUES(:email, :alias, :name_first, :name_last, :organization, :created_on, :auth_hash, :active)
		");

		$st->bindValue(':email', $email);
		$st->bindValue(':alias', $alias);
		$st->bindValue(':name_first', $name_first);
		$st->bindValue(':name_last', $name_last);
		$st->bindValue(':organization', $organization);
		$st->bindValue(':created_on', time());
		$st->bindValue(':auth_hash', password_hash($password, PASSWORD_BCRYPT));
		$st->bindValue(':active', $activate ? 1 : 0);

		// todo: check return value of following call and decide how to report data
		// model update failure after a nonetheless successfull call...
		$st->execute();
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_UPDATE, $ex);
	}
}

/**
 * Authenticate a user with email and password.
 * 
 * @param string $email a valid email address
 * 
 * @param string $password a valid password
 * 
 * @return bool if the user exists and is successfully authenticated, false if
 * not, i.e., if no user account identified by the specified email address is
 * found or if one is found but cannot be authenticated
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_ACCESS if the backing datastore could not be accessed
 */
function phpend_data_auth_user(string $email, string $password): bool {
 
	if (!$email || !$password) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

    try {

		$db = phpend_get_pdo();
		$rc = jm_db_pdo_fetch_single_cell($db, "
			SELECT auth_hash
			FROM user
			WHERE email=?
		", [$email]);
		return !empty($rc) && password_verify($password, $rc[0]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 * Check if a user account identified by an email address is active.
 * 
 * @param string $email a valid email address
 * 
 * @return bool true if a user account identified by the specified email address
 * is found and active, false otherwise, i.e., if no account is found or if one
 * is found but is inactive
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_ACCESS if the backing datastore could not be accessed
 */
function phpend_data_is_user_active(string $email): bool {

	if (!$email) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

	try {
		$db = phpend_get_pdo();
		return jm_db_pdo_is_non_zero($db, "
			SELECT count(*)
			FROM user
			WHERE email=?
			AND active='1'
		", [$email]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 * Set a user account as active or inactive.
 * 
 * @param string $email a valid email address
 * 
 * @param bool $active the user account identified by the specified email shall
 * be set as active if true, inactive if false
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_UPDATE if the backing datastore could not be updated
 */
function phpend_data_set_user_active(string $email, bool $active): void {

	if (!$email) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

	try {

		$db = phpend_get_pdo();

		$st = $db->prepare("
			UPDATE user
			SET active=?
			WHERE email=?
		");

		// todo: check return value of following call and decide how to report data
		// model update failure after a nonetheless successfull call...
		$st->execute([$active ? 1 : 0, $email]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_UPDATE, $ex);
	}
}

/**
 * Issue an item.
 * 
 * @param string $item the item to issue
 * 
 * @param string $type the type of the item to issue, token or apikey
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_UPDATE if the backing datastore could not be updated
 */
function phpend_data_issue_item(string $item, string $type, string $stakeholder): void {

	if (!$item || ($type !== 'token' && $type !== 'apikey')) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

	try {

		$db = phpend_get_pdo();

		$st = $db->prepare("
			INSERT INTO issued(item, type, stakeholder, issued_on)
			VALUES (?, ?, ?, ?)
		");

		// todo: check return value of following call and decide how to report data
		// model update failure after a nonetheless successfull call...
		$st->execute([$item, $type, $stakeholder, time()]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_UPDATE, $ex);
	}
}

/**
 * Revoke an item.
 * 
 * @param string $item the item to revoke
 * 
 * @param string $type the type of the item to revoke, token or apikey
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_UPDATE if the backing datastore could not be updated
 */
function phpend_data_revoke_item(string $item, string $type): void {

	if (!$item || ($type !== 'token' && $type !== 'apikey')) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

	try {

		$db = phpend_get_pdo();

		$st = $db->prepare("
			DELETE FROM issued
			WHERE item=?
			AND type=?
		");

		// todo: check return value of following call and decide how to report data
		// model update failure after a nonetheless successfull call...
		$st->execute([$item, $type]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_UPDATE, $ex);
	}
}

/**
 * Revoke all items associated with a specifid stakeholder.
 * 
 * @param string $stakeholder the stakeholder whose items to revoke
 * 
 * @param string $type the type of the items to revoke, token or apikey
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_UPDATE if the backing datastore could not be updated
 */
function phpend_data_revoke_items(string $stakeholder, string $type): void {

	if (!$stakeholder || ($type !== 'token' && $type !== 'apikey')) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

	try {

		$db = phpend_get_pdo();

		$st = $db->prepare("
			DELETE FROM issued
			WHERE stakeholder=?
			AND type=?
		");

		// todo: check return value of following call and decide how to report data
		// model update failure after a nonetheless successfull call...
		$st->execute([$stakeholder, $type]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_UPDATE, $ex);
	}
}

/**
 * Check if an item is issued.
 * 
 * @param string $item the item to check
 * 
 * @param string $type the type of the item to check, token or apikey
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more
 * information about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_UPDATE if the backing datastore could not be updated
 */
function phpend_data_is_issued(string $item, $type): bool {

	if (!$item || ($type !== 'token' && $type !== 'apikey')) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

	try {
		$db = phpend_get_pdo();
		return jm_db_pdo_is_non_zero($db, "
			SELECT count(*)
			FROM issued
			WHERE item=?
			AND type=?
		", [$item, $type]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_ACCESS, $ex);
	}
}

/**
 * Set a user's password.
 * 
 * @param string $email a valid email address
 * 
 * @param string $password a valid password
 * 
 * @throws DataModelException if the requested operation could not be completed
 * for any reason. The exception's error code and message provide more information
 * about the error:
 * - ERROR_INVALID_ARGUMENT if the specified email was invalid
 * - ERROR_DATASTORE_UPDATE if the backing datastore could not be updated
 */
function phpend_data_set_user_password(string $email, string $password): void {

	if (!$email || !$password) {
		throw new DataModelException(DataModelException::ERROR_INVALID_ARGUMENT);
	}

    try {

		$auth_hash = password_hash($password, PASSWORD_BCRYPT);

		$db = phpend_get_pdo();

		$st = $db->prepare("
			UPDATE user
			SET auth_hash=?
			WHERE email=?
		");

		// todo: check return value of following call and decide how to report data
		// model update failure after a nonetheless successfull call...
		$st->execute([$auth_hash, $email]);
	}
	catch (PDOException $ex) {
		throw new DataModelException(DataModelException::ERROR_DATASTORE_UPDATE, $ex);
	}
}

/**
 * TODO
 * 
 * @throws PDOException
 */
function phpend_get_pdo() {
	return new PDO(PHPEND_DSN);
}

/**
 * An exception type denoting errors in accessing, querying or updating a data
 * model.
 */
class DataModelException extends Exception {

	public const ERROR_INVALID_ARGUMENT = 1;
	public const ERROR_DATASTORE_ACCESS = 2;
	public const ERROR_DATASTORE_UPDATE = 3;

	private static $messages = [
		DataModelException::ERROR_INVALID_ARGUMENT => 'Invalid argument',
		DataModelException::ERROR_DATASTORE_ACCESS => 'Could not access datastore',
		DataModelException::ERROR_DATASTORE_UPDATE => 'Could not update datastore'
	];

	public function __construct(int $code, Exception $previous = null) {
		$message = '';
		if (array_key_exists($code, DataModelException::$messages)) {
			$message = DataModelException::$messages[$code];
		}
		parent::__construct($message, $code, $previous);
	}
}
