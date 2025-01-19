<?php

use PHPMailer\PHPMailer\PHPMailer;

function jm_send_email(array $to, array $cc, array $bcc, string $subject, string $body, string $alt_body,
	string $username, string $password, string $sender_address, string $sender_label, string $host,
	int $port): void {

	$mail = new PHPMailer(true);

	// send email...
	try {
		//Server settings
		$mail->isSMTP();
		$mail->Host       = $host;
		$mail->SMTPAuth   = true;
		$mail->Username   = $username;
		$mail->Password   = $password;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port       = $port;

		//Recipients
		$mail->setFrom($sender_address, $sender_label);

		foreach ($to as $address => $label) {
			$mail->addAddress($address, $label);
		}

		foreach ($cc as $address => $label) {
			$mail->addCC($address, $label);
		}		

		foreach ($bcc as $address => $label) {
			$mail->addBCC($address, $label);
		}		

		//Content
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = $alt_body;

		$mail->send();
	}
	catch (Exception $ex) {
		throw new Exception($mail->ErrorInfo, 0, $ex);
	}
}
