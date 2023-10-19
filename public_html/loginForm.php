<?php
if(is_logged_in()) {
    die(header("Location: sessionTestPage.php"));
}
?>
<html>

    <head>
        <meta charset="UTF-8">

    </head>

    <body>
        <form method="POST">
            <label for="username"> Username: </label><br>
            <input type="text" id="username" name="username"><br>

            <label for="password"> Password: </label><br>
            <input type="text" id="password" name="password"><br>

            <button type="submit"> Login </button>


        </form>



    </body>

</html>
<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
        $password = $_POST['password'];
        require_once(__DIR__ . '../scripts/path.inc');
        require_once(__DIR__ . '../scripts/get_host_info.inc');
        require_once(__DIR__ . '../scripts/rabbitMQLib.inc');
        $client = new rabbitMQClient(__DIR__ . "/../scripts/testRabbitMQ.ini", "testServer");

        $request = array();
        $request['type'] = "login";
        $request['username'] = $_POST['username'];
        $request['password'] = $_POST['password'];
        print_r($request);

        $client->publish($request);
        $server = new rabbitMQServer(__DIR__ . "/../scripts/testRabbitMQ2.ini", "testServer");
        $response = $server->process_requests();

        if(isset($response['type']) && $response['type'] === 'login_response') {
            if($response['login_status'] === 'success') {
                flash("Login accepted", "success");
                session_start();
                send_session(session_id(), $_POST['username']);
            } else {
                flash("Login denied, fuck off", "danger");
            }
        } else {
            //TODO:Log error
            echo "Error with response";
        }

} else {
    echo "Both username and password are required.";
}

?>
<?php
require(__DIR__ . "/../scripts/partials/flash.php");
?>