<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * 
 */
 function generate_jwt(string $secret, string $iss, string $expmod, array $claims): string {

	$now = new DateTimeImmutable();

	$iat = $nbf = $now->getTimestamp();
	$exp = $now->modify($expmod)->getTimestamp();

	$payload = [
		'iss' => $iss,
		'iat' => $iat,
		'nbf' => $nbf,
		'exp' => $exp,
	];

	foreach ($claims as $key => $value) {
		if ($key !== null && $value != null && !array_key_exists($key, $payload)) {
			$payload[$key] = $value;
		}
	}

	return JWT::encode($payload, $secret, 'HS256');
}

/**
 * 
 */
function validate_jwt(string $jwt, string $key, string $iss, array $claims = null): array {

	// var_dump($jwt);
	// var_dump(JWT::decode($jwt, new Key($key, 'HS256')));
	// var_dump(JWT::decode($jwt . 'garbage', new Key($key, 'HS256')));
	$jwt = JWT::decode($jwt, new Key($key, 'HS256'));

	if ($jwt->iss !== $iss) {
		throw new Exception("Invalid issuer");
	}
	
	if ($jwt->iat > $jwt->nbf || $jwt->iat > $jwt->exp || $jwt->nbf > $jwt->exp) {
		throw new Exception("Invalid times");
	}

	$now = (new DateTimeImmutable())->getTimestamp();

	if ($jwt->iat > $now || $jwt->nbf > $now || $jwt->exp < $now) {
		throw new Exception("Cannot be used at this time");
	}

	if ($claims) {
		foreach ($claims as $key => $value) {
			if ($value !== null && (!array_key_exists($key, $claims) || !isset($jwt->$key) || $jwt->$key === '' || ($value !== '*' && $jwt->$key !== $value))) {
				throw new Exception("Unmet claim");
			}
		}
	}

	return (array) $jwt;
}
