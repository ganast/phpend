<?php

use com\ganast\jm\http\json\JSONHTTPHelpers;

/**
 * Formulates and echoes a response according to API standards and then exits.
 */
function phpend_util_respond_with(array $data, int $code = 0, string $message = "", int $http_code = 200): void {
	$r = array();
	$r['code'] = $code;
	$r['message'] = $message;
	if (!empty($data)) {
		$r['data'] = $data;
	}
	JSONHTTPHelpers::returnJSONHTTPResponse($http_code, $r);
}

/**
 * Formulates and echoes a plaintext response describing an error and then exits.
 *
 * @param [type] $e TODO
 *
 * @return void TODO
 */
function phpend_util_dump_error_details(Throwable $e, int $http_error_code = 500): void {

	reset_request($http_error_code, 'text/html; charset=utf-8');

	echo nl2br(get_class($e) . ': ' . $e->getMessage());
	echo '<hr><br>';
	echo nl2br($e->getTraceAsString());

	exit();
}

/**
 * Echoes a plaintext error message and then exits.
 *
 * @param [type] $e TODO
 *
 * @return void TODO
 */
function phpend_util_dump_error_message(string $error_message, int $error_code, int $http_error_code = 500): void {

	reset_request($http_error_code, 'text/html; charset=utf-8');

	echo nl2br($error_message . ' (' . $error_code . ')');

	exit();
}

/**
 * TODO
 *
 * @param string $template TODO
 * @param array $values TODO
 *
 * @return string TODO
 */
function phpend_util_inflate_template(string $template, array $values): string {

	foreach($values as $key => $value) {
		if (!is_array($value)) {
			$template = str_replace('{'.strtoupper($key).'}', (string) $value, $template);
		}
	}

	return $template;
}

/**
 * TODO
 *
 * @param string $path TODO
 * @param array $values TODO
 *
 * @return string TODO
 */
function phpend_util_inflate_template_from_file(string $path, array $values): string {

	if (!$template = file_get_contents($path)) {
		throw new Exception();
	}

	return phpend_util_inflate_template($template, $values);
}

/**
 * TODO
 *
 * @param string $to_address TODO
 * @param string $to_label TODO
 * @param string $message_type TODO
 *
 * @return void TODO
 */
function phpend_util_send_email(string $to_address, string $message_type, array $values): bool {

	try {

		$profile = phpend_data_get_user_data($to_address);

		$to_label = "{$profile['name_first']} {$profile['name_last']}";

		$subject = phpend_util_inflate_template(PHPEND_MAIL_TEMPLATES[$message_type][0], get_defined_constants());

		$body = phpend_util_inflate_template_from_file(PHPEND_MAIL_TEMPLATES_DIR . '/' . $message_type,
			get_defined_constants() + $profile + $values);

		$alt_body = strip_tags($body);

		jm_send_email(
			[$to_address => $to_label],
			[],
			[PHPEND_MAIL_FROM[0] => PHPEND_MAIL_FROM[1]],
			$subject,
			$body,
			$alt_body,
			PHPEND_MAIL_SMTP_CREDENTIALS[0],
			PHPEND_MAIL_SMTP_CREDENTIALS[1],
			PHPEND_MAIL_FROM[0],
			PHPEND_MAIL_FROM[1],
			PHPEND_MAIL_SMTP_SERVER[0],
			PHPEND_MAIL_SMTP_SERVER[1]
		);
	}
	catch (Exception $ex) {
		return false;
	}

	return true;
}