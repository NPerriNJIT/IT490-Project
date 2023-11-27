<?php
//TODO: Add db stuff
function set_version_status($request) {
    $response = array();
    $response['status'] == 'failure';
    if(!isset($request['version']) || !isset($request['status'])) {
        return $response;
    }
    $db = getDB();
    $stmt = "Update Packages set status = :status where version = :version";
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