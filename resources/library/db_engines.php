<?php
require_once 'db_connect.php';

create_table_engines ();

function insert_engine($name) {
	global $db;
	$guid = getGUID ();
	$query = "INSERT INTO engines (uuid, name)
		VALUES ('" . $guid . "', '" . $name . "')";

	$result = $db->query ( $query );

	if ($result) {
		// echo "New record created successfully<br>";
	} else {
		// echo "Error: " . $query . "<br>" . $db->error . "<br><br>";
	}
	return $result;
}

function get_engine($uuid) {
	global $db;
	$query = "SELECT * FROM engines WHERE uuid = '" . $uuid . "'";
	$result = $db->query ( $query );
	if ($result) {
		if (mysqli_num_rows ( $result )) {
			$data = $result->fetch_object ();
			$result->free ();
			return $data;
		}
	}
	return FALSE;
}

function get_engines() {
	global $db;
	$data = array ();
	$query = "SELECT * FROM engines ORDER BY name";
	$result = $db->query ( $query );
	if ($result) {
		if (mysqli_num_rows ( $result )) {
			while ( $date = $result->fetch_object () ) {
				$data [] = $date;
			}
			$result->free ();
		}
	}
	return $data;
}

function create_table_engines() {
	global $db;
	$query = "CREATE TABLE engines (
                          uuid CHARACTER(32) NOT NULL,
						  name VARCHAR(32) NOT NULL,
                          PRIMARY KEY  (uuid)
                          )";

	$result = $db->query ( $query );

	if ($result) {
		// echo "Table created<br>";
		insert_engine ( "Löschzug 1/2" );
		insert_engine ( "Löschzug 3" );
		insert_engine ( "Löschzug 4" );
		insert_engine ( "Löschzug 5" );
		insert_engine ( "Löschzug 6" );
		insert_engine ( "Löschzug 7" );
		insert_engine ( "Löschzug 8" );
		insert_engine ( "Löschzug 9" );
	} else {
		// echo "Error: " . $db->error . "<br><br>";
	}
}