<?php
require_once 'get_host_info.inc';
require_once 'rabbitMQLib.inc';

// Create a new rabbitMQClient instance with the machine-specific .ini file
$mqClient = new rabbitMQClient("dLoggerRabbitMQ.ini", "testServer");
$machineIdentifier = "Joe's Machine";

// Log file paths (with sudo)
$logFilePaths = [
    '/var/log/rabbitmq/rabbitmq@jheans-VirtualBox.log',
    '/var/log/apache2/error.log',
    '/var/log/mysql/error.log',
];

// Read and send log messages from specified log files
foreach ($logFilePaths as $logFilePath) {
    // Execute the 'sudo cat' command to read the log file with elevated privileges
    $logContents = shell_exec('sudo cat ' . escapeshellarg($logFilePath));

    if ($logContents !== null) {
        // Create a log message
        $logMessage = [
            'timestamp' => time(),
            'level' => 'ERROR',
            'message' => $logContents, // Use the log file contents as the message
            'machine' => $machineIdentifier, // Add the machine identifier to the log
            'log_file' => $logFilePath, // Include the log file path in the log message
        ];

        // Convert log message to JSON format
        $logMessageJson = json_encode($logMessage);

        // Send the log message to the RabbitMQ server
        $mqClient->publish($logMessageJson);

        echo " [x] Sent log message from '$logFilePath'\n";
    } else {
        echo " [x] Error reading log file '$logFilePath'\n";
    }
}
?>
