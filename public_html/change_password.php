<?php
require_once '../resources/templates/header.php';
require_once '../resources/library/secured_page.php';
require_once '../resources/library/db_user.php';
?>
<div class="jumbotron text-center">
	<h1>Passwort �ndern</h1>
</div>
<div class="container">
<?php
if (isset ( $_POST ['password_old'] ) && isset ( $_POST ['password'] ) && isset ( $_POST ['password2'] ) && isset ( $_SESSION ['userid'] )) {

	$uuid = $_SESSION ['userid'];
	$password_old = trim ( $_POST ['password_old'] );
	$password = trim ( $_POST ['password'] );
	$password2 = trim ( $_POST ['password2'] );

	$error = false;
	if (strlen ( $password_old ) == 0) {
		showAlert ( 'Bitte aktuelles Passwort eingeben' );
		$error = true;
	}
	if (strlen ( $password ) == 0) {
		showAlert ( 'Bitte neues Passwort eingeben' );
		$error = true;
	}
	if ($password != $password2) {
		showAlert ( 'Die Passw�rter m�ssen �bereinstimmen' );
		$error = true;
	}

	if (! $error) {
		$uuid = change_password ( $uuid, $password_old, $password );
		showSuccess ( "Password erfolgreich ge�ndert" );
	}
}

?>

	<form action="" method="post">
		Aktuelles Passwort:<br> <input type="password" size="40"
			required="required" maxlength="250" name="password_old"><br> <br>
		Neues Passwort:<br> <input type="password" size="40"
			required="required" maxlength="250" name="password"><br> <br>
		Passwort wiederholen:<br> <input type="password" size="40"
			required="required" maxlength="250" name="password2"><br> <br> <input
			type="submit" value="Passwort ändern" class="btn btn-primary">
	</form>
</div>
<footer>
	<div class="container">
		<a href='event_overview.php' class="btn btn-outline-primary">Zur�ck
			zur Wachen�bersicht</a> <a href='manager_overview.php'
			class="btn btn-outline-primary">Zur�ck zur Beauftragten-�bersicht</a>
	</div>
	
<?php require_once '../resources/templates/footer.php'; ?>