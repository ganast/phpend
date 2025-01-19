<?php

// bootstrap...

require __DIR__ . '/vendor/autoload.php';

// todo: do some autoload ffs...
require __DIR__ . '/vendor/ganast/jm/src/com/ganast/jm/db/DBPDOHelpers.php';
require __DIR__ . '/vendor/ganast/jm/src/com/ganast/jm/exception/CodedErrorException.php';
require __DIR__ . '/vendor/ganast/jm/src/com/ganast/jm/exception/InvalidStateException.php';
require __DIR__ . '/vendor/ganast/jm/src/com/ganast/jm/http/HTTPHelpers.php';
require __DIR__ . '/vendor/ganast/jm/src/com/ganast/jm/http/json/JSONHTTPHelpers.php';
require __DIR__ . '/vendor/ganast/jm/src/com/ganast/jm/jwt/JWTHelpers.php';
require __DIR__ . '/vendor/ganast/jm/src/com/ganast/jm/mail/MailHelpers.php';

require 'include/config.php';
require 'include/error.php';
require 'include/util.php';
require 'include/datalayer.php';
require 'include/handlers.php';
require 'include/backend.php';
require 'include/api.php';
require 'include/auth.php';
require 'include/guard.php';

include 'api/config.php';
include 'api/datalayer.php';
include 'api/handlers.php';
include 'api/backend.php';
require 'api/api.php';

require PHPEND_LOCAL_CONFIG;

// setup dev/prod environment...

ini_set('display_errors', PHPEND_DEBUG);

// dispatcher setup...

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\ConfigureRoutes $routes) {

    $routes->addGroup(PHPEND_URL_SUBDIR . PHPEND_API_PATH, function (FastRoute\ConfigureRoutes $routes) {

        foreach ($GLOBALS['phpend_api_endpoints'] as $method => $map) {
            foreach ($map as $pattern => $handler) {
                $routes->addRoute($method, $pattern, $handler[0], $handler[1]);
            }
        }
    });
});

// get method and URI...

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// strip query string (?foo=bar) and decode URI...

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// identify endpoint for URI and method...

$routeInfo = $dispatcher->dispatch($method, $uri);

switch ($routeInfo[0]) {

    // endpoint not found...
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        break;

    // endpoint found but illegal method...
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        break;

    // endpoint found...
    case FastRoute\Dispatcher::FOUND:

        $handler = $routeInfo->handler;

        $vars = $routeInfo->variables;

        $params = $routeInfo->extraParameters;

        try {

			$data = phpend_guard_invoke_handler($handler, $vars, $_REQUEST, $params);

			phpend_util_respond_with($data, 0, "OK", 200);
        }
        
		// these are meant to contain information actually appropriate for end-users...
		catch (HTTPAPIErrorException $e) {

			phpend_util_respond_with([], $e->getCode(), $e->getMessage(), $e->getHTTPCode());
        }

		// exceptions are most likely due to bugs...
		catch (Exception $e) {

			if (PHPEND_DEBUG) {
				phpend_util_dump_error_details($e);
			}
			else {
				phpend_util_dump_error_message("Guru meditation", $e->getCode(), 500);
			}
		}

		// errors are most likely due to external issues at runtime...
		catch (Error $e) {

			if (PHPEND_DEBUG) {
				phpend_util_dump_error_details($e);
			}
			else {
				phpend_util_dump_error_message("Guru meditation", $e->getCode(), 500);
			}
		}

        break;
}
