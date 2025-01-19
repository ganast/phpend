<?php

/* 
 * API endpoint handlers. These are the functions that connect API endpoints with actual backend functionality.
 * Additional functionality is exposed by defining a new endpoint (usually a route of some kind) and connecting
 * it to a new handler here.
 * 
 * Handler function naming.
 * All handlers shall be named as api_<short_functionality_description>.
 * 
 * Handler function arguments.
 * All handlers shall accept a single $vars argument in the form of an associative array containing string/mixed
 * tuples specific to the handlers operation (e.g., a login handler shall expect an email and password identified
 * by a string, respectively, most likely extracted from an endpoint route).
 * 
 * Handler return values.
 * A value shall only be returned in case of successful completion of a handler's operation and shall always be a
 * valid JSON document encoded as an array and structured as follows:
 * - TODO
 * A value shall never be returned in case of unsuccessful handler operation, either that be a caught error (an API   
 * error, see below) or an uncaught error (most commonly a bug, see below).
 * 
 * Handler API errors.
 * In case a handler's operation failed for anticipated reasons (e.g., a user account registration handler failed
 * to register a new user account because the specified email was already in use) the handler shall throw a
 * HTTPAPIErrorException with a valid error identifier string among those defined in API_HTTP_ERROR_CODES.
 * 
 * Other handler errors.
 * Handlers shall make no attempt to catch and handle errors other than those treated as HTTPAPIErrorExceptions.
 * Any such error is most probably a bug since a handler's implementation should reflect its entire business logic
 * and throw HTTPAPIErrorExceptions in all problematic cases that are an anticipated part of that business logic.
 * Any other error that may arise is, therefore, outside the scope of the handler's normal operation and, as such,
 * should be treated as a bug (i.e. propagate to client code to either be reported in some way or cause a fatal
 * failure and, thus, be identified).
 */

// add custom API handlers here...

function api_do_some_magic($vars) {

    $num = $vars['num'];

    if (get_mana() < MIN_MANA) {
        throw new HTTPAPIErrorException('NEED_MORE_MANA');
    }

    return [
        'success' => [
            'result' => $num * 2
        ]
    ];
}
