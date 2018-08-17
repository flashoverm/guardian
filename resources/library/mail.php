<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once (realpath ( dirname ( __FILE__ ) . "/../config.php" ));

require_once "phpmailer/src/PHPMailer.php";
require_once "phpmailer/src/SMTP.php";

function init_mail() {
	global $config;

	$mail = new PHPMailer ( true );
	$mail->isSMTP ();
	$mail->Host = $config ["mail"] ["host"];
	$mail->SMTPAuth = true;
	$mail->Username = $config ["mail"] ["username"];
	$mail->Password = $config ["mail"] ["password"];
	$mail->SMTPSecure = $config ["mail"] ["secure"];
	$mail->Port = $config ["mail"] ["port"];
	$mail->setFrom ( $config ["mail"] ["fromaddress"], $config ["mail"] ["fromname"] );
	return $mail;
}

function send_mail($to, $subject, $body) {
	$mail = init_mail ();

	$mail->addAddress ( $to );
	$mail->Subject = $subject;
	$mail->Body = $body;

	$mail->send ();
	// echo 'Message has been sent';
}

function send_html_mail($to, $subject, $body) {
	$mail = init_mail ();

	$mail->isHTML ( true );
	$mail->addAddress ( ( string ) $to );
	$mail->Subject = $subject;
	$mail->Body = $body;

	$mail->send ();
	// echo 'HTML Message has been sent';
}

?>