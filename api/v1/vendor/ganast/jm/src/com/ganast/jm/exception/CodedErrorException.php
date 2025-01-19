<?php

/*
 */

namespace com\ganast\jm\exception {

    use Exception;

    /**
     * TODO
     *
     * @author ganast
     */    
    abstract class CodedErrorException extends Exception {

        protected abstract function getErrorData(string $error): array;

        protected function __construct(string $error) {
            $error_data = $this->getErrorData($error);
            parent::__construct($error_data[1], $error_data[0]);
        }
    }
}
