<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background-image: url('./assets/backdrop.png');
            background-repeat: no-repeat;
            background-size: 100%;
        }
    </style>

</head>
<?php

session_start();

if ($_GET["end"] == true) {
    unset($_SESSION["user"]);
}
if (isset($_SESSION["user"])) {
    header('Location: victorycards.php');
}

console_log("console active");
function console_log($output, $with_script_tags = true)         #a function that can log things to the console
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

$mysqli = new mysqli("localhost", "root", "", "dayz"); # get database
if ($mysqli->connect_errno) {
    die("Verbindung fehlgeschlagen" . $mysqli->connect_error);
}

$sql =
    "SELECT
        players.name AS username,
        players.password as 'password'
    FROM
        players
";

$result = $mysqli->query($sql);     # get query result
$rows = $result->fetch_all(MYSQLI_ASSOC);       # fetches all rows of the query result table as an array

$users = [];

foreach ($rows as $user => $pass) {
    $users[$pass["username"]] = $pass["password"];
}

$alarmmessage = null;

if (isset($_POST["sin"])) { // user has pressed the submit button
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    $sql =
        "SELECT
            players.name AS username,
            players.password as 'password'
        FROM
            players
        WHERE
            players.name = '$user'
        ";

    $result = $mysqli->query($sql);     # get query result
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $hash = $rows[0]["password"];
    $verify = password_verify($pass, $hash);

    //go through user-list
    $found = false;
    if ($verify) {
        print("User $key has logged in successfully.");
        $found = true;
        $_SESSION["user"] = $user;
        header('Location: http://127.0.0.1/dayz/victorycards.php');
        exit;
    }
    if (!$found) {
        $alarmmessage = date("Y/m/d h:i:s") . ": user and/or password are incorrect. please try again";
    }
}

if (isset($_POST["sup"])) {
    $user = $_POST["user"];
    $pass = $_POST["pass"];
    $repass = $_POST["repass"];

    $error = false;


    foreach ($users as $key => $value) {
        if ($user == "") {
            $alarmmessage = "username cannot be empty";
            $error = true;
            break;
        }
        if ($pass == "") {
            $alarmmessage = "password cannot be empty";
            $error = true;
            break;
        }
        if ($user == $key) {
            $alarmmessage = "user already exists";
            $error = true;
            break;
        }
    }

    if ($pass != $repass) {
        $alarmmessage = "passwords do not match";
        $error = true;
    }


    if (!$error) {
        $pass = password_hash($_POST["pass"], PASSWORD_DEFAULT);

        $sql =
            "INSERT 
            INTO `dayz`.`players` (`name`, `password`) 
            VALUES ('$user', '$pass');
            ";

        $mysqli->query($sql);

        $sql =
        "INSERT 
        INTO `dayz`.`targets` (`name`, `targettype_id`) 
        VALUES ('$user', 1);
        ";

    $mysqli->query($sql);

        $alarmmessage = "Success";
    }
}



?>

<body class="align-items-center py-4">
    <div class="row">
        <div class="form-signin m-auto w-25 mt-5">

            <h1 class="h1 text-center" style="color:#ffdb00;;">DAYZ-VICTORYCARDS</h1>
            <form method="post" class="content-align-center d-felx">
                <div class="form-floating w-100">
                    <input type="text" name="user" id="inputuser" placeholder="user" class="form-control bg-light mt-1" style="border:#ffdb00">
                    <label for="inputuser">username</label>
                </div>
                <div class="form-floating w-100">
                    <input type="password" name="pass" id="inputpassword" placeholder="password" class="form-control bg-light mt-1" style="border:#ffdb00">
                    <label for="inputpassword">password</label>
                </div>
                <input type="submit" name="sin" id="" value="Login" class="btn btn-primary w-100 py-2 mt-3" style="background-color:#ffdb00; color:black; border:#ffdb00">

            </form>
            <!-- Button to Open the Modal -->
            <button type="button" class="btn btn-primary w-100 py-2 mt-1" style="background-color:#ffdb00;  color:black; border:#ffdb00" data-bs-toggle="modal" data-bs-target="#signupmodal">
                Sign Up
            </button>

            <!-- The Modal -->
            <div class="modal hide fade" id="signupmodal">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Sign Up</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                            <form method="post">
                                <div class="form-floating">
                                    <input type="text" name="user" id="inputuser" placeholder="user" class="form-control bg-light mt-1" style="border:#ffdb00">
                                    <label for="inputuser">username</label>
                                </div>
                                <div class="form-floating">
                                    <input type="password" name="pass" id="inputpassword" placeholder="password" class="form-control bg-light mt-1" style="border:#ffdb00">
                                    <label for="inputpassword">password</label>
                                </div>
                                <div class="form-floating">
                                    <input type="password" name="repass" id="reinputpassword" placeholder="password" class="form-control bg-light mt-1" style="border:#ffdb00">
                                    <label for="reinputpassword">repeat password</label>
                                </div>
                                <input type="submit" name="sup" id="" value="Sign Up" class="btn btn-primary w-100 py-2 mt-3" style="background-color:#ffdb00; border:#ffdb00">
                            </form>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container w-25">

        <?php
        if ($alarmmessage == "") {
        } else if ($alarmmessage == "Success") {
            print("
            <div class='alert alert-success alert-dismissible fade show m-5' role='alert'>
                Success
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>");
        } else {
            print("
            <div class='alert alert-danger alert-dismissible fade show m-5' role='alert'>
                $alarmmessage
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>");
        }
        ?>
    </div>

</body>