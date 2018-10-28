<?php
require_once realpath ( dirname ( __FILE__ ) . "/../resources/config.php" );
require_once LIBRARY_PATH . "/template.php";
require_once '../resources/library/db_engines.php';
require_once '../resources/library/db_user.php';

// Pass variables (as an array) to template
$variables = array (
		'title' => "Als Wachbeauftragter registrieren",
		'secured' => false
);

if ($config ["settings"] ["selfregistration"]) {
	$variables ['showFormular'] = true;
	
	$engines = get_engines ();
	$variables ['engines'] = $engines;
} else {
	$variables ['showFormular'] = false;
	$variables ['alertMessage'] = "Selbstregistrierung deaktiviert - <a href=\"login.php\" class=\"alert-link\">Zum Login</a>";
}

if (isset ( $_POST ['email'] ) && isset ( $_POST ['password'] ) && isset ( $_POST ['password2'] ) && isset ( $_POST ['engine'] ) && isset ( $_POST ['firstname'] ) && isset ( $_POST ['lastname'] )) {

	$firstname = trim($_POST ['firstname']);
	$lastname = trim($_POST ['lastname']);
	$email = trim($_POST ['email']);
	$password = trim($_POST ['password']);
	$password2 = trim($_POST ['password2']);
	$engine = trim($_POST ['engine']);

	$error = false;
	if ($password != $password2) {
	    $variables ['alertMessage'] = 'Die Passwörter müssen übereinstimmen';
		$error = true;
	}
	if (! $error) {
		if (email_in_use ( $email )) {
			$variables ['alertMessage'] = "Diese E-Mail-Adresse ist bereits vergeben";
			$error = true;
		}
	}
	if (! $error) {
		if ($config ["settings"] ["autoadmin"]) {
			$result = insert_admin ( $firstname, $lastname, $email, $password, $engine );
		} else {
			$result = insert_manager ( $firstname, $lastname, $email, $password, $engine );
		}

		if ($result) {
			header ( "Location: login.php" ); // redirects
		} else {
			$variables ['alertMessage'] = "Ein unbekannter Fehler ist aufgetreten";
		}
	}
}

renderLayoutWithContentFile ( "register_template.php", $variables );
?>