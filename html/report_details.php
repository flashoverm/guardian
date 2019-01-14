<?php
require_once realpath(dirname(__FILE__) . "/../resources/config.php");
require_once LIBRARY_PATH . "/template.php";
require_once '../resources/library/db_report.php';

if (! isset($_GET['id'])) {
    
    // Pass variables (as an array) to template
    $variables = array(
        'title' => 'Bericht kann nicht angezeigt werden',
        'secured' => true,
        'showFormular' => false,
        'alertMessage' => "Bericht kann nicht angezeigt werden"
    );
} else {
    $uuid = trim($_GET['id']);
    $report = get_report($uuid);
    
    if($report){
        $variables = array(
            'title' => "Wachbericht",
            'secured' => true,
            'showFormular' => true,
            'report' => $report
        );
    } else {
        $variables = array(
            'title' => 'Bericht nicht gefunden',
            'secured' => true,
            'showFormular' => false,
            'alertMessage' => "Bericht nicht gefunden"
        );
    }
        
}

renderLayoutWithContentFile("reportDetails_template.php", $variables);
