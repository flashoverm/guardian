<?php
require_once realpath ( dirname ( __FILE__ ) . "/../../resources/config.php" );
require_once TEMPLATES_PATH . "/template.php";
require_once LIBRARY_PATH . "/db_report.php";
require_once LIBRARY_PATH . "/db_engines.php";
require_once LIBRARY_PATH . "/db_eventtypes.php";

// Pass variables (as an array) to template
$variables = array (
    'title' => "Übersicht Wachberichte",
    'secured' => true,
);

if(isset($_SESSION ['guardian_userid'])){
    $user = $_SESSION ['guardian_userid'];
    $usersEngine = get_engine(get_engine_of_user($user));
    
    if (isset ( $_POST ['delete'] )) {
        $delete_report_uuid = trim ( $_POST ['delete'] );
        if(delete_report ( $delete_report_uuid )){
            $variables ['successMessage'] = "Bericht gelöscht";
        } else {
            $variables ['alertMessage'] = "Bericht konnte nicht gelöscht werden";
        }
    }
    
    if (isset ( $_POST ['emsEntry'] )) {
        if(set_ems_entry($_POST ['emsEntry'])){
            $variables['successMessage'] = "Bericht aktualisiert";
        } else {
            $variables['alertMessage'] = "Bericht konnte nicht aktualisiert werden";
        }
    }
        
    if($usersEngine->isadministration == true){
        $variables ['reports'] = get_reports();
    } else {
        $variables ['reports'] = get_filtered_reports($usersEngine->uuid);
        $variables ['infoMessage'] = "Es werden nur Wachberichte angezeigt, die Ihrem Zug zugewiesen wurden";
    }
}

renderLayoutWithContentFile ($config["apps"]["guardian"], "reportOverview_template.php", $variables );

?>