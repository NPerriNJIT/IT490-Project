<?php
require_once(__DIR__ . "/db.php");
require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');
//TODO: Add db stuff
function set_version_status($request) {
    $response = array();
    $response['status'] == 'failure';
    if(!isset($request['version']) || !isset($request['status'])) {
        return $response;
    }
    $db = getDB();
    $stmt = $db->prepare("Update Packages set status = :status where version = :version");
    try{
        $r = $stmt->execute([':status' => $request['status'], ':version' => $request['version']]);
        if($r) {
            $response['status'] == 'valid';
        }
    } catch (Exception $e) {
        echo("Error: ". $e);
    }
    return $response;
}