<?php
require_once realpath ( dirname ( __FILE__ ) . "/../resources/config.php" );
require_once LIBRARY_PATH . "/template.php";

// Pass variables (as an array) to template
$variables = array (
		'title' => 'Impressum',
		'secured' => false,
);

renderLayoutWithContentFile ( "impressum_template.php", $variables );
