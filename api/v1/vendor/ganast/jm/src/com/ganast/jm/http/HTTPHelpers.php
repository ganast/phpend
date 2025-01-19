<?php

/**
 * 
 */
function get_auth_token_from_request(): string|null {

	if (!$headers = getallheaders()) {
		return null;
	}

	if (!isset($headers['Authorization'])) {
		return null;
	}

	return preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches) === 1 ? $matches[1] : null;
}

/**
 * TODO
 *
 * @param integer $error_code TODO
 * @param string $content_type TODO
 *
 * @return void TODO
 */
function reset_request(int $error_code = 500, string $content_type = 'text/plain; charset=utf-8') {

	ob_start();
	ob_clean();

	header_remove();

	header('Content-type: ' . $content_type);

	http_response_code($error_code);
}
