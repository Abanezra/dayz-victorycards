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

if (isset($_GET["end"])) {
    if ($_GET["end"] == "true") {
        unset($_SESSION["user"]);
    }
}
if (isset($_SESSION["user"])) {
    @header('Location: victorycards.php?sVC=Sort+Victory+Cards&sort=status');
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
    @$hash = $rows[0]["password"];
    $verify = password_verify($pass, $hash);

    //go through user-list
    $found = false;
    if ($verify) {
        print("User $key has logged in successfully.");
        $found = true;
        $_SESSION["user"] = $user;
        header('Location: victorycards.php?sVC=Sort+Victory+Cards&sort=status');
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
    <div class="row align-items-center py-4">
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
        </div>



        <!-- The Modal -->
        <div class="modal hide fade" id="signupmodal">
            <div class="modal-dialog">
                <div class="modal-content bg-dark">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title text-warning">Sign Up</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-floating">
                                <input type="text" name="user" id="inputuser" placeholder="user" class="form-control bg-dark text-light mt-1" style="border:#ffdb00">
                                <label for="inputuser" class="text-light">username</label>
                            </div>
                            <div class="form-floating">
                                <input type="password" name="pass" id="inputpassword" placeholder="password" class="form-control bg-dark text-light mt-1" style="border:#ffdb00">
                                <label for="inputpassword" class="text-light">password</label>
                            </div>
                            <div class="form-floating">
                                <input type="password" name="repass" id="reinputpassword" placeholder="password" class="form-control bg-dark text-light mt-1" style="border:#ffdb00">
                                <label for="reinputpassword" class="text-light">repeat password</label>
                            </div>
                            <input type="submit" name="sup" id="" value="Sign Up" class="btn btn-primary text-dark w-100 py-2 mt-3" style="background-color:#ffdb00; border:#ffdb00">
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
    <div class="container w-75">
        <div class="row align-items-center py-4">
            <div class="card-group mt-5">
                <div class="h3 text-center card bg-transparent">
                    <p class="my-2 h2 text-warning">Leaderboard:</p>
                    <?php
                    $sql =
                        "SELECT 
                        players.name AS username,
                        states.name AS statename
                    FROM
                        players
                        JOIN
                        victorycards ON players.id = victorycards.player_id
                        JOIN
                        states ON victorycards.state_id = states.id
                    WHERE
                        1
                ";

                    $rows = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);

                    foreach ($rows as $row) {
                        @$statcount[$row["username"]][$row["statename"]] += 1;
                        if (isset($statcount[$row["username"]]["Failed"])) {
                            @$statcount[$row["username"]]["Ratio"] = $statcount[$row["username"]]["Done"] / $statcount[$row["username"]]["Failed"];
                        } else {
                            @$statcount[$row["username"]]["Ratio"] = $statcount[$row["username"]]["Done"] / 1;
                        }
                    }

                    $userwithmostfails = "";
                    $mostfails = 0;
                    $userwithmostdone = "";
                    $mostdone = 0;
                    $userwithbestratio = "";
                    $bestratio = 0;

                    if (isset($statcount)) {
                        foreach ($statcount as $userstat) {
                            if (isset($userstat["Failed"])) {
                                if ($userstat["Failed"] > $mostfails) {
                                    $mostfails = $userstat["Failed"];
                                    $userwithmostfails = array_search($userstat, $statcount);
                                }
                            }
                            if (isset($userstat["Done"])) {
                                if ($userstat["Done"] > $mostdone) {
                                    $mostdone = $userstat["Done"];
                                    $userwithmostdone = array_search($userstat, $statcount);
                                }
                            }
                        }
                        $userwithbestratio = "";
                        $bestratio = 0;

                        foreach ($statcount as $userstat) {
                            if ($userstat["Ratio"] > $bestratio) {
                                $bestratio = $userstat["Ratio"];
                                $userwithbestratio = array_search($userstat, $statcount);
                            }
                        }
                    }
                    print("
                        <table class='table table-striped table-hover table-warning'>
                            <thead>
                                <tr>
                                    <th scope='col' style='background-color: #ffdb00'>Category</th>
                                    <th scope='col' style='background-color: #ffdb00'>Victorist</th>
                                    <th scope='col' style='background-color: #ffdb00'>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Most Fails</td>
                                    <td>$userwithmostfails</td>
                                    <td>$mostfails</td>
                                </tr>
                                <tr>
                                    <td>Most Done</td>
                                    <td>$userwithmostdone</td>
                                    <td>$mostdone</td>
                                </tr>
                                <tr>
                                    <td>Best Ratio</td>
                                    <td>$userwithbestratio</td>
                                    <td>$bestratio</td>
                                </tr>
                            </tbody>
                        </table>
                        ");
                    ?>
                </div>
                <div class="h3 text-center card bg-transparent">
                    <p class="my-2 h2 text-warning">Titles:</p>
                    <?php
                    $userwithmostplayerkills = "";
                    $mostplayerkills = 0;
                    $userwithmosthunts = "";
                    $mosthunts = 0;
                    $userwithmostinfectedkills = "";
                    $mostinfectedkills = 0;
                    $userwithmostvisits = "";
                    $mostvisits = 0;
                    $userwithmostcollected = "";
                    $mostcollected = 0;
                    $userwithmostcars = "";
                    $mostcars = 0;

                    $players = array();

                    $sql = "SELECT players.name FROM players WHERE 1";

                    $rows = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);

                    foreach ($rows as $row) {
                        array_push($players, array(
                            "name" => $row["name"],
                            "playerkills" => 0,
                            "hunts" => 0,
                            "infectedkills" => 0,
                            "visits" => 0,
                            "collected" => 0,
                            "cars" => 0
                        ));
                    }

                    console_log($players);

                    $sql =
                        "SELECT 
                        players.name AS username,
                        victoryconditions.targettype_id AS target
                    FROM
                        players
                        JOIN
                        victorycards ON players.id = victorycards.player_id
                        JOIN
                        victoryconditions ON victorycards.victorycondition_id = victoryconditions.id
                    WHERE
                        victorycards.state_id = 2
                ";

                    $rows = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);

                    foreach ($rows as $row) {
                        switch ($row["target"]) {
                            case 1:
                                $players[array_search($row["username"], array_column($players, "name"))]["playerkills"] += 1;
                                break;
                            case 2:
                                $players[array_search($row["username"], array_column($players, "name"))]["hunts"] += 1;
                                break;
                            case 3:
                                $players[array_search($row["username"], array_column($players, "name"))]["infectedkills"] += 1;
                                break;
                            case 4:
                                $players[array_search($row["username"], array_column($players, "name"))]["visits"] += 1;
                                break;
                            case 5:
                                $players[array_search($row["username"], array_column($players, "name"))]["collected"] += 1;
                                break;
                            case 6:
                                $players[array_search($row["username"], array_column($players, "name"))]["cars"] += 1;
                                break;
                        }
                    }

                    foreach ($players as $player) {
                        if ($player["playerkills"] > $mostplayerkills) {
                            $mostplayerkills = $player["playerkills"];
                            $userwithmostplayerkills = $player["name"];
                        } else if ($player["playerkills"] == $mostplayerkills && $mostplayerkills != 0) {
                            $userwithmostplayerkills = $userwithmostplayerkills . ", " . $player["name"];
                        }
                        if ($player["hunts"] > $mosthunts) {
                            $mosthunts = $player["hunts"];
                            $userwithmosthunts = $player["name"];
                        } else if ($player["hunts"] == $mosthunts && $mosthunts != 0) {
                            $userwithmosthunts = $userwithmosthunts . ", " . $player["name"];
                        }
                        if ($player["infectedkills"] > $mostinfectedkills) {
                            $mostinfectedkills = $player["infectedkills"];
                            $userwithmostinfectedkills = $player["name"];
                        } else if ($player["infectedkills"] == $mostinfectedkills && $mostinfectedkills != 0) {
                            $userwithmostinfectedkills = $userwithmostinfectedkills . ", " . $player["name"];
                        }
                        if ($player["visits"] > $mostvisits) {
                            $mostvisits = $player["visits"];
                            $userwithmostvisits = $player["name"];
                        } else if ($player["visits"] == $mostvisits && $mostvisits != 0) {
                            $userwithmostvisits = $userwithmostvisits . ", " . $player["name"];
                        }
                        if ($player["collected"] > $mostcollected) {
                            $mostcollected = $player["collected"];
                            $userwithmostcollected = $player["name"];
                        } else if ($player["collected"] == $mostcollected && $mostcollected != 0) {
                            $userwithmostcollected = $userwithmostcollected . ", " . $player["name"];
                        }
                        if ($player["cars"] > $mostcars) {
                            $mostcars = $player["cars"];
                            $userwithmostcars = $player["name"];
                        } else if ($player["cars"] == $mostcars && $mostcars != 0) {
                            $userwithmostcars = $userwithmostcars . ", " . $player["name"];
                        }
                    }

                    print("
                        <table class='table table-striped table-hover table-warning'>
                            <thead>
                                <tr>
                                    <th scope='col' style='background-color: #ffdb00'>Title</th>
                                    <th scope='col' style='background-color: #ffdb00'>Current Holder/s</th>
                                    <th scope='col' style='background-color: #ffdb00'>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Assassin</td>
                                    <td>$userwithmostplayerkills</td>
                                    <td>$mostplayerkills</td>
                                </tr>
                                <tr>
                                    <td>Hunter</td>
                                    <td>$userwithmosthunts</td>
                                    <td>$mosthunts</td>
                                </tr>
                                <tr>
                                    <td>Disinfecter</td>
                                    <td>$userwithmostinfectedkills</td>
                                    <td>$mostinfectedkills</td>
                                </tr>
                                <tr>
                                    <td>Wanderer</td>
                                    <td>$userwithmostvisits</td>
                                    <td>$mostvisits</td>
                                </tr>
                                <tr>
                                    <td>Lootbrain</td>
                                    <td>$userwithmostcollected</td>
                                    <td>$mostcollected</td>
                                </tr>
                                <tr>
                                    <td>Drifter</td>
                                    <td>$userwithmostcars</td>
                                    <td>$mostcars</td>
                                </tr>
                            </tbody>
                        </table>
                        ");
                    ?>
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