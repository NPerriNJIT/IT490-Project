<?php
require_once 'rabbitMQLib.inc'; // Include the rabbitMQLib.inc file

// Create a new rabbitMQServer instance with the machine-specific .ini file
$mqServer = new rabbitMQServer("dLoggerRabbitMQ.ini","testServer");

// Callback function to process incoming log messages
$callback = function ($message) {
    // Get the message body (the log message in JSON format)
    $logMessageJson = $message->getBody();

    // Decode the JSON log message
    $logMessage = json_decode($logMessageJson, true);

    if ($logMessage === null) {
        // Handle invalid JSON format, if needed
        echo " [x] Received an invalid log message: '$logMessageJson'\n";
        return;
    }

    // Check if the 'machine' field is present in the log message
    if (isset($logMessage['machine'])) {
        $machineIdentifier = $logMessage['machine'];
        // Now you know which machine generated the log
        echo " [x] Received log message from '$machineIdentifier': '{$logMessage['message']}'\n";

        // Add your specific processing logic here based on the machine identifier
        // For example, you can store logs in separate files or databases based on the machine

        // Here, we'll just display the log message, but you can customize this part.
        // You can write the log to a file, database, or perform any other desired action.

        // Example: Store logs in separate files based on the machine
        #$logFilePath = "/var/logs/$machineIdentifier.log";
        #file_put_contents($logFilePath, $logMessageJson . PHP_EOL, FILE_APPEND);

    } else {
        // Handle log messages without a machine identifier, if needed
        echo " [x] Received a log message without a machine identifier: '{$logMessage['message']}'\n";
    }
};

// Create a connection to RabbitMQ and consume log messages
try {
    $mqServer->process_requests($callback);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
