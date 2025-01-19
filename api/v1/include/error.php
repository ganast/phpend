<?php

use com\ganast\jm\exception\CodedErrorException;

/**
 * TODO
 * 
 * TODO: Consider also propagating a cause exception/error...
 */
class APIErrorException extends CodedErrorException {
    
    public function __construct(string $error) {
        parent::__construct($error);
    }

    #[\Override]
    protected function getErrorData(string $error): array {

        if (!isset($GLOBALS['phpend_api_errors'][$error])) {
            return [0, 'Unspecified error'];
        }

        return $GLOBALS['phpend_api_errors'][$error];
    }
}

/**
 * TODO
 * 
 * TODO: Consider also propagating a cause exception/error...
 */
class HTTPAPIErrorException extends APIErrorException {

	/**
	 * TODO
	 */
    private int $httpCode;

	/**
	 * TODO
	 *
	 * @param string $error TODO
	 */
    public function __construct(string $error) {
        $this->httpCode = $this->getErrorCode($error);
        parent::__construct($error);
    }

    public function getHTTPCode(): int {
        return $this->httpCode;
    }

    protected function getErrorCode(string $error): int {

        if (!isset($GLOBALS['phpend_api_http_error_codes'][$error])) {
            return 500;
        }

        return $GLOBALS['phpend_api_http_error_codes'][$error];
    }
}
