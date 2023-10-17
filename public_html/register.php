
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" required maxlength="30" />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success
        let email = form.email.value;
        let username = form.username.value;
        let password = form.password.value;
        let confirm = form.confirm.value;
        let isValid = true;
        if (email == "")
        {
            console.log("Email Cannot Be Blank");
            isValid = false;
        }
        if (username == "")
        {
            console.log("Username Cannot Be Blank");
            isValid = false;
        }
        if (password.value !== confirm.value)
        {
            console.log("Password and confirm password must match");
            isValid = false;
        }
        if (password.length < 8)
        {
            console.log("Password must be at least 8 characters");
            isValid = false;
        }
        return isValid;
    }
</script>
<?php
function sanitize_email($email = "")
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "")
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
function users_check_duplicate($errorInfo)
{
    if ($errorInfo[1] === 1062) {
        //https://www.php.net/manual/en/function.preg-match.php
        preg_match("/Users.(\w+)/", $errorInfo[2], $matches);
        if (isset($matches[1])) {
            echo ("The username " . $matches[1] . " is already taken. Try again.");
        } else {
            //TODO come up with a nice error message
            echo "<pre>" . var_export($errorInfo, true) . "</pre>";
        }
    } else {
        //TODO come up with a nice error message
        echo "<pre>" . var_export($errorInfo, true) . "</pre>";
    }
}
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["username"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];
    $username = $_POST["username"];
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        echo("Email must not be empty");
        $hasError = true;
    }
    //sanitize
    #$email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        echo("Invalid email address");
        $hasError = true;
    }
    if (!preg_match('/^[a-z0-9_-]{3,16}$/i', $username)) {
        echo("Username must only contain 3-16 characters a-z, 0-9, _, or -");
        $hasError = true;
    }
    if (empty($password)) {
        echo "password must not be empty";
        $hasError = true;
    }
    if (empty($confirm)) {
        echo "Confirm password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        echo "Passwords must match";
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
        $client = new rabbitMQClient("../scripts/testRabbitMQ.ini", "testServer");
        $request = array();
        $request['type'] = 'registration';
        $request['username'] = $username;
        $request['password'] = $hash;
        $client->publish($request);
        $server = new rabbitMQServer("../scripts/testRabbitMQ2.ini", "testServer");
        $response = $server->process_requests();
        if()
        if(isset($response['type']) && $response['type'] === 'registration_response') {
            if($response['registration_status'] === 'success') {
                flash("Registration successful", "success");
                //Session shenanigans
            } else {
                flash("Registration denied, fuck off", "danger");
            }
        } else {
            //TODO:Log error
            echo "Error with response";
        }
        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>