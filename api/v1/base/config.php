<?php

use PHPMailer\PHPMailer\PHPMailer;

/*
 * Base phpend configuration.
 */

/**
 * 
 * NOTE: Must include a leading slash, not include a trailing slash.
 */
const PHPEND_API_PATH = '/api/v1';

/**
 * 
 * NOTE: Must include a leading slash, not include a trailing slash.
 */
const PHPEND_WEBAPP_RESET_PATH = '/reset';

/**
 * 
 * NOTE: Must include a leading slash, not include a trailing slash.
 */
const PHPEND_WEBAPP_VERIFY_PATH = '/verify';

/**
 * 
 */
const PHPEND_WEBAPP_ACTION_PARAM = 'a';

/**
 * 
 */
const PHPEND_LOCAL_CONFIG = 'config-local.php';

/**
 * 
 */
const PHPEND_MAIL_SMTP_SECURITY = [PHPMailer::ENCRYPTION_SMTPS];

/**
 * 
 */
const PHPEND_MAIL_TEMPLATES_DIR = 'templates';

/**
 * 
 */
const PHPEND_MAIL_TEMPLATES = [

	'verify' => [
		'[{PHPEND_TITLE}] Activate your account',
		'verify'
	],

	'reset' => [
		'[{PHPEND_TITLE}] Password reset',
		'reset'
	]
];
