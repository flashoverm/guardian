<?php
require_once LIBRARY_PATH . "/db_connect.php";

require_once LIBRARY_PATH . '/class/EventReport.php';
require_once LIBRARY_PATH . '/class/ReportUnit.php';
require_once LIBRARY_PATH . '/class/ReportUnitStaff.php';

create_table_report();
create_table_reportUnit();
create_table_reportStaff();

function insert_report_short($date, $start, $end, $type_uuid, $type_other, $title, $engine_uuid, $creator, $noIncidents, $ilsEntry, $report) {
    global $db;
    
    $uuid = getGUID ();
    
    if($noIncidents){
        
        $statement = $db->prepare("INSERT INTO report (uuid, date, start_time, end_time, type, type_other, title, engine, creator, noIncidents, ilsEntry, report)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, ?, ?)");
        $statement->bind_param('sssssssssis', $uuid, $date, $start, $end, $type_uuid, $type_other, $title, $engine_uuid, $creator, $ilsEntry, $report);
        
    } else {
        
        $statement = $db->prepare("INSERT INTO report (uuid, date, start_time, end_time, type, type_other, title, engine, creator, noIncidents, ilsEntry, report)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, FALSE, ?, ?)");
        $statement->bind_param('sssssssssis', $uuid, $date, $start, $end, $type_uuid, $type_other, $title, $engine_uuid, $creator, $ilsEntry, $report);
        
    }
    
    $result = $statement->execute();
    
    if ($result) {
        // echo "New event record created successfully";
        return $uuid;
    } else {
        //echo "Error: " . "<br>" . $db->error;
        return false;
    }
}

function insert_report_detail($report_uuid, EventReport $report_object){
	$units = $report_object->units;
	
	foreach($units as $unit){
		$unit_uuid = insert_report_unit(
				$unit->date, 
				$unit->beginn, 
				$unit->end,
				(isset($unit->unit) ? $unit->unit : null),
				(isset($unit->km) ? $unit->km : null),
				$report_uuid);
		
		$staff = $unit->staffList;
		
		foreach($staff as $entry){
			insert_report_staff(
					$entry->position,
					$entry->name,
					$entry->engine,
					$unit_uuid);
		}
	}
}

function insert_report_unit($date, $beginn, $end, $unit, $km, $report_uuid){
	global $db;
	
	$uuid = getGUID ();
		
	$statement = $db->prepare("INSERT INTO report_unit (uuid, date, start_time, end_time, unit, km, report)
		VALUES (?, ?, ?, ?, ?, ?, ?)");	
	$statement->bind_param('sssssis', $uuid, $date, $beginn, $end, $unit, $km, $report_uuid);
		
	$result = $statement->execute();
	
	if ($result) {
		// echo "New event record created successfully";
		return $uuid;
	} else {
		//echo "Error: " . "<br>" . $db->error;
		return false;
	}
}

function insert_report_staff($position, $name, $engine, $unit_uuid){
	global $db;
	
	$uuid = getGUID ();
	
	$statement = $db->prepare("INSERT INTO report_staff (uuid, position, name, engine, unit)
		VALUES (?,
				(SELECT uuid FROM staffposition WHERE position = ?), 
				?, 
				(SELECT uuid FROM engine WHERE name = ?), 
				?
		)");	
	$statement->bind_param('sssss', $uuid, $position, $name, $engine, $unit_uuid);
	
	$result = $statement->execute();
	
	if ($result) {
		// echo "New event record created successfully";
		return $uuid;
	} else {
		//echo "Error: " . "<br>" . $db->error;
		return false;
	}
}

function get_filtered_reports($engine_uuid) {
    global $db;
    $data = array ();
    
    $statement = $db->prepare("SELECT * FROM report WHERE engine = '" . $engine_uuid . "' ORDER BY date DESC");
    
    if ($statement->execute()) {
        $result = $statement->get_result();
        
        if (mysqli_num_rows ( $result )) {
            while ( $date = $result->fetch_object () ) {
                $data [] = $date;
            }
            $result->free ();
        }
    }
    return $data;
}

function get_reports() {
    global $db;
    $data = array ();
    
    $statement = $db->prepare("SELECT * FROM report ORDER BY date DESC");
    
    if ($statement->execute()) {
        $result = $statement->get_result();
        
        if (mysqli_num_rows ( $result )) {
            while ( $date = $result->fetch_object () ) {
                $data [] = $date;
            }
            $result->free ();
        }
    }
    return $data;
}

function get_report($report_uuid) {
    global $db;
    
    $statement = $db->prepare("SELECT * FROM report WHERE uuid = ?");
    $statement->bind_param('s', $report_uuid);
    
    $result = $statement->execute();
    
    if ($result) {
        return $statement->get_result()->fetch_object ();
    } else {
        // echo "UUID not found";
    }
}

function get_report_units($report_uuid) {
	global $db;
	$data = array ();
	
	$statement = $db->prepare("SELECT * FROM report_unit WHERE report = ?");
	$statement->bind_param('s', $report_uuid);

	if ($statement->execute()) {
		$result = $statement->get_result();
		
		if (mysqli_num_rows ( $result )) {
			while ( $date = $result->fetch_object () ) {
			
				$reportUnit = new ReportUnit($date->unit, $date->date, $date->start_time, $date->end_time);
				$reportUnit->setKM($date->km);
				
				$staff = get_report_staff($date->uuid);
				foreach($staff as $entry){
					$reportUnit->addStaff(new ReportUnitStaff($entry->position, $entry->name, $entry->engine));
				}
				
				$data [] = $reportUnit;
			}
			$result->free ();
		}
	}
	return $data;
}

function get_report_staff($unit_uuid){
	global $db;
	$data = array ();
	
	$statement = $db->prepare("SELECT * FROM report_staff WHERE unit = ?");
	$statement->bind_param('s', $unit_uuid);
	
	if ($statement->execute()) {
		$result = $statement->get_result();
		
		if (mysqli_num_rows ( $result )) {
			while ( $date = $result->fetch_object () ) {
				$data [] = $date;
			}
			$result->free ();
		}
	}
	return $data;
}

function set_ems_entry($uuid){
	global $db;
	
	$statement = $db->prepare("UPDATE report SET emsEntry = TRUE WHERE uuid = ?");
	$statement->bind_param('s', $uuid);
	
	$result = $statement->execute();
	
	if ($result) {
		return true;
	} else {
		//echo "Error: " . $query . "<br>" . $db->error;
		return false;
	}
}

function delete_ems_entry($uuid){
    global $db;
    
    $statement = $db->prepare("UPDATE report SET emsEntry = FALSE WHERE uuid = ?");
    $statement->bind_param('s', $uuid);
    
    $result = $statement->execute();
    
    if ($result) {
        return true;
    } else {
        //echo "Error: " . $query . "<br>" . $db->error;
        return false;
    }
}

function delete_report($uuid) {
    global $db;
    
    $statement = $db->prepare("DELETE FROM report_staff WHERE unit IN (SELECT uuid FROM report_unit WHERE report = ?)");
    $statement->bind_param('s', $uuid);
    
    $result = $statement->execute();
    
    $statement = $db->prepare("DELETE FROM report_unit WHERE report = ?");
    $statement->bind_param('s', $uuid);
    
    $result = $statement->execute();
    
    $statement = $db->prepare("DELETE FROM report WHERE uuid= ?");
    $statement->bind_param('s', $uuid);
    
    $result = $statement->execute();
        
    if ($result) {
        // echo "Record ".$uuid." removed successfully";
        return true;
    } else {
        // echo "Error: " . $query . "<br>" . $db->error;
        return false;
    }
}

function create_table_report() {
    global $db;
    
    $statement = $db->prepare("CREATE TABLE report (
                          uuid CHARACTER(36) NOT NULL,
						  date DATE NOT NULL,
                          start_time TIME NOT NULL,
                          end_time TIME NOT NULL,
                          type CHARACTER(36) NOT NULL,
                          type_other VARCHAR(96),
						  title VARCHAR(96),
                          engine CHARACTER(36) NOT NULL,
						  creator VARCHAR(128) NOT NULL,
                          noIncidents BOOLEAN NOT NULL,
                          ilsEntry BOOLEAN NOT NULL,
						  emsEntry BOOLEAN NOT NULL,
                          report TEXT,
                          PRIMARY KEY  (uuid),
						  FOREIGN KEY (type) REFERENCES eventtype(uuid),
						  FOREIGN KEY (engine) REFERENCES engine(uuid)
                          )");
    
    $result = $statement->execute();
    
    if ($result) {
        // echo "Table created<br>";
        return true;
    } else {
        // echo "Error: " . $db->error . "<br><br>";
        return false;
    }
}

function create_table_reportUnit(){
	global $db;
	
	$statement = $db->prepare("CREATE TABLE report_unit (
                          uuid CHARACTER(36) NOT NULL,
						  date DATE NOT NULL,
                          start_time TIME NOT NULL,
                          end_time TIME NOT NULL,
						  unit VARCHAR(96),
						  km SMALLINT,
						  report CHARACTER(36) NOT NULL,
                          PRIMARY KEY  (uuid),
						  FOREIGN KEY (report) REFERENCES report(uuid)
                          )");
	
	$result = $statement->execute();
	
	if ($result) {
		// echo "Table created<br>";
		return true;
	} else {
		// echo "Error: " . $db->error . "<br><br>";
		return false;
	}
}

function create_table_reportStaff(){
	global $db;
	
	$statement = $db->prepare("CREATE TABLE report_staff (
                          uuid CHARACTER(36) NOT NULL,
						  position CHARACTER(36) NOT NULL,
						  name VARCHAR(96) NOT NULL,
                          engine CHARACTER(36) NOT NULL,
						  unit CHARACTER(36) NOT NULL,
                          PRIMARY KEY  (uuid),
						  FOREIGN KEY (unit) REFERENCES report_unit(uuid),
						  FOREIGN KEY (engine) REFERENCES engine(uuid)
                          )");
	
	$result = $statement->execute();
	
	if ($result) {
		// echo "Table created<br>";
		return true;
	} else {
		// echo "Error: " . $db->error . "<br><br>";
		return false;
	}
}