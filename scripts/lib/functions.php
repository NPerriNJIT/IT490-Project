<?php
require_once(__DIR__ . "/db.php");
require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');

function set_version_status($request) {
    $response = array();
    $response['status'] = 'failure';
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
//Adds a new version
function add_version($destination_path, $version, $machine, $sender_name, $sender_ip) {
    
    $response = array();
    $response['status'] = 'failure';
    if(!isset($request['version']) || !isset($request['status'])) {
        return $response;
    }
    //Attempt to run bash script
    if(!receive_version($destination_path, $sender_ip, $sender_name)) {
        echo("Failed to receive version " . $version . PHP_EOL);
        return $response;
    }
    //If successful, update SQL information
    echo("Received version " . $version . PHP_EOL);
    $local_path = '/destination/to/packages/' . $version;
    $db = getDB();
    $stmt = $db->prepare("Insert into Packages (machine, destination_path, local_path, version) values (:machine, :destination_path, :local_path, :version)");
    try {
        $r = $stmt->execute([':machine' => $machine, ':destination_path' => $destination_path, ':local_path' => $local_path, ':version' => $version]);
        if($r) {
            $response['status'] = 'success';
        }
    } catch (Exception $e) {
        echo("Error: ". $e);
    }
    return $response;
}

//Used to call a bash script that will receive a new version file via sftp
function receive_version($destination_path, $sender_ip, $sender_name) {
    //Run bash script with path of file on sender, sender ip, deployment ip
    exec("receive_file.sh $sender_name $sender_ip $destination_path", $output, $return_code);
    echo "Receive path " . $destination_path . " output:\n";
    print_r($output);
    if($return_code === 0) {
        return true;
    }
    return false;
}