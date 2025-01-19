-- OUTDATED -- MUST UPDATE FOR PDO, HANDLER REQUEST PARAMS --

# General

General implementation notes for the MADAuth API and app.

## Method argument and return value typing

The following rules apply:

- Argument types shall always be specified.
- Return value type shall always be specified.
- Mixed type shall never be used.
- Composite types shall never be used.
- Nullable types shall be rigorously avoided.

An additional, non-mandatory rule, is that if a function returns an array then
that array shall be structured the same in all cases.

## Argument validation

Invalid arguments shall always throw an InvalidArgumentException. No code or
cause shall be specified. A message shall only be specified if necessary to
indicate which argument is invalid. If nullable types are avoided, arguments
need not be checked for not having been defined or null values.

# Data layer

Notes specific to the data layer implementation.

## General method structure

Methods in this module are generally structured as follows:

1. argument validation,
2. datasource access/updates,
3. additional, datasource-independent steps,
4. value return, if any.

## Datasource access/updates

Methods updating the datasource do not perform preemptive constraint checks,
e.g., a method for user registration does not check if the specified email
address or alias are available or already in the databse. If such constraints
fail during execution of the update then the method will simply fail. It is the
responsibility of client code to perform all checks required for proper UX
before calling such update methods.

# API endpoint handlers

Notes specific to the API endpoint handlers implementations.

These are the functions that connect API endpoints with actual backend
functionality. Additional functionality is exposed by defining a new endpoint
(usually a route of some kind) and connecting it to a new handler here.

## Security issues

Handlers for things such as user login, user registration, etc., are invoked by
public (aka anonymous) endpoints and, as such, are susceptible to DoS (and other
types of) attacks. However, a backend is not the place to handle neither on an
implementation for on a design level. A third-party protection layer is the
proper solution to such problems while this kind of endpoints, e.g., user login,
user registration, etc., should indeed be exposed as public.

Having said that, it might be worth to implement something like an execution
delay or rate restriction or something like that to mitigate, at a minimally-
basic level, the risk of maliciously-rapid public endpoint invovation.

## Handler function naming

All handlers shall be named as api_&lt;short_functionality_description&gt;.

## Handler function arguments

All handlers shall accept a single $vars argument in the form of an associative
array containing string/mixed tuples specific to the handlers operation (e.g., a
login handler shall expect an email and password identified by a string,
respectively, most likely extracted from an endpoint route).

## Handler function return values

A value shall only be returned in case of successful completion of a handler's
operation and shall always be a valid JSON document represented as an array and
structured as follows:
TODO
A value shall never be returned in case of unsuccessful handler operation,
either that be a caught error (an API error, see below) or an uncaught error
(most commonly a bug, see below).

## Handler API errors

In case a handler's operation failed for anticipated reasons (e.g., a user
account registration handler failed to register a new user account because the
specified email was already in use) the handler shall throw a
HTTPAPIErrorException with a valid error identifier string among those defined
in API_HTTP_ERROR_CODES.

## Other handler errors

Handlers shall make no attempt to catch and handle errors other than those
treated as HTTPAPIErrorExceptions. Any such error is most probably a bug since a
handler's implementation should reflect its entire business logic and throw
HTTPAPIErrorExceptions in all problematic cases that are an anticipated part of
that business logic. Any other error that may arise is, therefore, outside the
scope of the handler's normal operation and, as such, should be treated as a bug
(i.e. propagate to client code to either be reported in some way or cause a
fatal failure and, thus, be identified).
