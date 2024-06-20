<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Victorycards</title>

    <link rel="stylesheet" href="./victorycards.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Charts.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background-color: #3e3546;
            color: #ffdb00;
        }

        .tab {
            top: 0;
        }

        .tab-filter {
            background-color: #3e3546;
        }

        .tab-victorycards {
            background-color: #3e3546;
        }
    </style>

    <script>
        $(document).ready(() => {
            $("#amountNewVictoryCardsOutput").val() = "Amount of new Cards:" + $("#amountNewVictoryCards").val();
        });





        var xValues = ["Done", "Open", "Failed", "???"];
        var yValues = [];
        var labels = [];
        var doneCards = [];
        var openCards = [];
        var failedCards = [];

        function countStatistics(statcount) {
            console.log(statcount);

            const obj = JSON.parse(statcount);

            console.log(obj);

            yValues[0] = obj.Done;
            yValues[1] = obj.Open;
            yValues[2] = obj.Failed;
            yValues[3] = obj.Invalid;
        }

        function countCardStatus(statcount) {
            console.log(statcount);

            const obj = JSON.parse(statcount);

            console.log(obj);

            labels = obj.labels;
            doneCards = obj.doneCards;
            openCards = obj.openCards;
            failedCards = obj.failedCards;
        }

        var barColors = [
            "#198754",
            "#ffc107",
            "#dc3545",
            "#6c757d"
        ];
    </script>

</head>
<?php

$location = "Location: victorycards.php?sortVictoryCards=Sort+Victory+Cards&sort=status";

session_start();
if (!isset($_SESSION["user"])) {
    header('Location: http://127.0.0.1/dayz/login.php?end=true&user=');
}
if ($_SESSION["user"] == "admin") {
    $adminaccess = 1;
} else {
    $adminaccess = 0;
}

$user = $_SESSION["user"];
$status = @getstate();
function getstate()
{
    if ($_GET["state"] == "" or !isset($_GET["state"])) {
        $status = "-";
    } else {
        $status = $_GET["state"];
    }
    return $status;
}


console_log("console active");
function console_log($output, $with_script_tags = true)
{         #a function that can log things to the console
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

function newVictorycard($mysqli, $player_id, $location, $target)
{

    if ($target == "") {
        $targettypes = getTargetTypes($mysqli);
        $targettype = $targettypes[array_rand($targettypes)];
    } else {
        $target = strtolower($target);
        $sql = "SELECT
                    targettypes.id AS \"id\",
                    targettypes.name AS \"targettype\"
                FROM
                    targettypes
                WHERE
                    targettypes.name = \"$target\"
            ";

        $result = $mysqli->query($sql);
        $targettype = $result->fetch_all(MYSQLI_ASSOC)[0];
    }

    if (isset($targettype["targettype"])) {
        $targets = getTargets($mysqli, $targettype["targettype"]);
    } else {
        $targets = getTargets($mysqli, $targettype[1]);
    }
    $target = $targets[array_rand($targets)];


    $titles = [
        "player" => "Assassination",
        "animal" => "Hunting Trip",
        "infected" => "Fight the Virus",
        "location" => "Recon",
        "item" => "Lootbrain",
        "vehicle" => "Driving Lesson"
    ];

    $conditions = [
        "player" => "Assassinate",
        "animal" => "Hunt",
        "infected" => "Dispatch",
        "location" => "Visit",
        "item" => "Get",
        "vehicle" => "Drive"
    ];

    $playeristarget = false;

    foreach ($titles as $type => $title) {
        if (isset($targettype["targettype"])) {
            $targettype[1] = $targettype["targettype"];
        }
        if (isset($targettype["id"])) {
            $targettype[0] = $targettype["id"];
        }
        if ($type == $targettype[1]) {
            $VCTitle = $title;
            $VCCondition = $conditions[$type];
            if ($type == "animal" || $type == "infected" || $type == "item") {
                $amount = rand(1, 3);
            } else {
                $amount = "NULL";
            }
            if ($type == "player") {
                if ($_SESSION["user"] != $target[1]) {
                    console_log("User and target are different");
                } else {
                    console_log("User and target are the same, trying again");
                    $playeristarget = true;
                    newVictorycard($mysqli, $player_id, $location, "player");
                }
            }
        }
    }


    if ($_SESSION["user"] != $target[1]) {
        console_log("AAAAA");
    } else {
        console_log("BBBBB");
    }

    if (!$playeristarget) {
        $vcconditionid = insertVictoryCondition($mysqli, $VCTitle, $VCCondition, $targettype[0], $target[0], $amount);
        insertVictoryCard($mysqli, $player_id, $vcconditionid);

        @header($location);
    }
}

function getTargetTypes($mysqli)
{
    $sql =
        "SELECT
            targettypes.id AS \"id\",
            targettypes.name AS \"targettype\"
        FROM
            targettypes
    ";

    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        if ($row["id"] == 7) {
        } else {
            $targettypes[$row["id"]] = [$row["id"], $row["targettype"]];
        }
    }

    return $targettypes;
}

function getTargets($mysqli, $targettype)
{
    $sql =
        "SELECT
            targets.id AS \"id\",
            targets.name AS \"target\"
        FROM
            targets
            JOIN
            targettypes ON targettypes.id = targets.targettype_id
        WHERE
            targettypes.name = \"$targettype\"
    ";

    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        $targets[$row["id"]] = [$row["id"], $row["target"]];
    }

    return $targets;
}

function insertVictoryCondition($mysqli, $name, $condition, $targettype_id, $target_id, $amount)
{
    $id = uniqid();
    $sql =
        "INSERT 
        INTO `dayz`.`victoryconditions` ( `id`, `name`, `condition`, `targettype_id`, `target_id`, `amount` ) 
        VALUES ( \"$id\", \"$name\", \"$condition\", $targettype_id, $target_id, $amount )
    ";
    console_log($sql);
    $mysqli->query($sql);
    $sql =
        "SELECT
            victoryconditions.id,
            victoryconditions.name,
            victoryconditions.condition,
            victoryconditions.targettype_id,
            victoryconditions.target_id,
            victoryconditions.amount
        FROM
            victoryconditions
        WHERE
            victoryconditions.id = \"$id\"
    ";
    $result = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);

    return $result[0]["id"];
}

function insertVictoryCard($mysqli, $player_id, $victorycondition_id)
{
    $code = uniqid();
    $CURRENT_TIMESTAMP = date("Y-m-d H:i:s");
    $sql =
        "INSERT 
        INTO `dayz`.`victorycards` ( `code`, `player_id`, `victorycondition_id`, `state_id`, `creation_timestamp` ) 
        VALUES ( \"$code\", $player_id, \"$victorycondition_id\", 1, \"$CURRENT_TIMESTAMP\" )
    ";
    $mysqli->query($sql);
}

function write_dropdown($mysqli, $table, $item)      # function to write a dropdown using a database table and item as input
{
    $sql = # get ids
        "SELECT
                $table.id 
            FROM
                $table
            WHERE
                1
            ";

    $result = $mysqli->query($sql);     # get query results
    $ids = $result->fetch_all(MYSQLI_ASSOC);        # fetches all rows of the query result table as an array

    foreach ($ids as $id) {         # for each id, the corresponding is printed into the dropdown
        $id = $id["id"];        # read the id
        $sql =      # select the item with $id as id
            "SELECT
                    $table.$item 
                FROM
                    $table
                WHERE
                    $table.id = $id
                ";
        $result = $mysqli->query($sql);     # get query results
        $name = $result->fetch_all(MYSQLI_ASSOC);       # fetches all rows of the query result table as an array
        $name = $name[0]["$item"];      # get the item and save it into $name
        if ($name != "undefined") {     # if the $name is not undefined
            print("<option name=$name class='bg-warning dropdown-item'>" . strtoupper($name) . "</option>");     # print the $name as an option in the dropdown
        }
    }
}

function countStatistics($mysqli, $statcount, $user, $adminaccess)
{
    $sql =
        "SELECT
            states.name AS 'state',
            COUNT(victorycards.state_id) AS 'count'
        FROM
            victorycards
            JOIN
            states ON states.id = victorycards.state_id
            JOIN
            players ON players.id = victorycards.player_id
        WHERE
            players.name = IF($adminaccess = 1, players.name, '$user')
        GROUP BY
            victorycards.state_id
    ";

    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        $statcount[$row["state"]] =  intval($row["count"]);
    }

    return $statcount;
}

function countCardStatus($mysqli, $cardstatus, $user, $adminaccess)
{

    $sql = "SELECT
                DATE_FORMAT(victorycards.creation_timestamp, '%Y-%m-%d %H:%i:%s') AS 'creation_timestamp',
                DATE_FORMAT(victorycards.completion_timestamp, '%Y-%m-%d %H:%i:%s') AS 'completion_timestamp',
                states.name AS 'state'
            FROM
                victorycards
                JOIN
                states ON states.id = victorycards.state_id
                JOIN
                players ON players.id = victorycards.player_id
            WHERE
                players.name = IF($adminaccess = 1, players.name, '$user')
    
    ";

    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    $cardstatus["labels"] = [];
    $cardstatus["doneCards"] = [];
    $cardstatus["openCards"] = [];
    $cardstatus["failedCards"] = [];

    foreach ($rows as $row) {
        if (!in_array($row["creation_timestamp"], $cardstatus["labels"])) {
            array_push($cardstatus["labels"], $row["creation_timestamp"]);
        }
        if (!in_array($row["completion_timestamp"], $cardstatus["labels"])) {
            array_push($cardstatus["labels"], $row["completion_timestamp"]);
        }
    }

    sort($cardstatus["labels"]);
    if ($cardstatus["labels"][0] == null) {
        array_shift($cardstatus["labels"]);
    }

    foreach ($cardstatus["labels"] as $label) {
        $cardstatus["doneCards"][$label] = 0;
        $cardstatus["openCards"][$label] = 0;
        $cardstatus["failedCards"][$label] = 0;
    }

    $firstTimestamp = $cardstatus["labels"][0];
    $sql = "SELECT COUNT(victorycards.state_id) AS 'count' FROM victorycards WHERE creation_timestamp = \"$firstTimestamp\"";
    $result = $mysqli->query($sql);
    $firstCardCount = intval($result->fetch_all(MYSQLI_ASSOC)[0]["count"]);
    foreach ($cardstatus["openCards"] as $timestamp => $count) {
        $cardstatus["openCards"][$timestamp] = $firstCardCount;
    }

    $sql = "SELECT
                DATE_FORMAT(victorycards.creation_timestamp, '%Y-%m-%d %H:%i:%s') AS 'creation_timestamp',
                DATE_FORMAT(victorycards.completion_timestamp, '%Y-%m-%d %H:%i:%s') AS 'completion_timestamp',
                states.name AS 'state'
            FROM
                victorycards
                JOIN
                states ON states.id = victorycards.state_id
                JOIN
                players ON players.id = victorycards.player_id
            WHERE
                players.name = IF($adminaccess = 1, players.name, '$user')
        ";

    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        $creationTimestamp = $row["creation_timestamp"];
        $completionTimestamp = $row["completion_timestamp"];
        $state = $row["state"];

        if ($state == "Done") {
            $reachedCompletionTimestamp = false;
            $reachCycle = 0;
            foreach ($cardstatus["doneCards"] as $timestamp => $count) {
                if ($timestamp == $completionTimestamp) {
                    $reachedCompletionTimestamp = true;
                }
                if ($reachedCompletionTimestamp) {
                    $reachCycle++;
                    $cardstatus["doneCards"][$timestamp] += 1;
                    $cardstatus["openCards"][$timestamp] -= 1;
                }
            }
        }
        if ($state == "Failed") {
            $reachedCompletionTimestamp = false;
            $reachCycle = 0;
            foreach ($cardstatus["failedCards"] as $timestamp => $count) {
                if ($timestamp == $completionTimestamp) {
                    $reachedCompletionTimestamp = true;
                }
                if ($reachedCompletionTimestamp) {
                    $reachCycle++;
                    $cardstatus["failedCards"][$timestamp] += 1;
                    $cardstatus["openCards"][$timestamp] -= 1;
                }
            }
        }
        if ($creationTimestamp != $firstTimestamp) {
            $reachedCompletionTimestamp = false;
            foreach ($cardstatus["openCards"] as $timestamp => $count) {
                if ($timestamp == $creationTimestamp) {
                    $reachedCompletionTimestamp = true;
                }
                if ($reachedCompletionTimestamp) {
                    $cardstatus["openCards"][$timestamp] += 1;
                }
            }
        }
    }


    return $cardstatus;
}

if (isset($_GET["sortVictoryCards"])) {
    $sort = $_GET["sort"];

    switch ($sort) {
        case "status":
            $order = "ORDER BY victorycards.state_id DESC, victoryconditions.name ASC";
            break;
        case "type":
            $order = "ORDER BY victoryconditions.name ASC, victorycards.state_id DESC";
            break;
    }
}

if (isset($_GET["deathVictoryCards"])) {

    @$sql =
        "SELECT
            players.id
        FROM
            players
        WHERE
            players.name = '$user'

    ";

    $player_id = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC)[0]["id"];
    $CURRENT_TIMESTAMP = date("Y-m-d H:i:s");

    @$sql =
        "UPDATE 
            dayz.victorycards 
        SET 
            victorycards.state_id = 0,
            victorycards.completion_timestamp = \"$CURRENT_TIMESTAMP\"
        WHERE  
            victorycards.player_id = $player_id
            AND
            victorycards.state_id = 1
    ";

    $mysqli->query($sql);     # get query result
}

if (isset($_POST["done"])) {

    $victorycondition_id = $_POST["victorycondition_id"];
    $CURRENT_TIMESTAMP = date("Y-m-d H:i:s");

    $sql =
        "UPDATE 
            dayz.victorycards 
        SET 
            state_id = 2,
            completion_timestamp = \"$CURRENT_TIMESTAMP\"
        WHERE  
            victorycondition_id = '$victorycondition_id';
    ";

    $mysqli->query($sql);

    @header($location);
}

if (isset($_POST["open"])) {

    $victorycondition_id = $_POST["victorycondition_id"];

    $sql =
        "UPDATE 
            dayz.victorycards 
        SET 
            state_id = 1,
            completion_timestamp = NULL
        WHERE  
            victorycondition_id = '$victorycondition_id';
    ";

    $mysqli->query($sql);

    @header($location);
}

if (isset($_POST["fail"])) {

    $victorycondition_id = $_POST["victorycondition_id"];
    $CURRENT_TIMESTAMP = date("Y-m-d H:i:s");

    $sql =
        "UPDATE 
            dayz.victorycards 
        SET 
            state_id = 0,
            completion_timestamp = \"$CURRENT_TIMESTAMP\"
        WHERE  
            victorycondition_id = '$victorycondition_id';
    ";

    $mysqli->query($sql);

    @header($location);
}

if (isset($_POST["newVC"])) {
    $amount = $_POST["amount"];
    $target = $_POST["target"];

    $sql =
        "SELECT
            players.id AS player_id
        FROM
            players
        WHERE players.name = IF($adminaccess = 1, players.name, '$user')
    ";

    $result = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);     # get query result

    $player_id = $result[0]["player_id"];

    for ($i = 0; $i < $amount; $i++) {
        newVictorycard($mysqli, $player_id, $location, $target);
    }
}

?>

<body>
    <h1 class="h1 text-center mx-auto">Eigene Victorycards</h1>
    <div class="d-flex">
        <div class="flex-fill">
            <div class="tab tab-filter">
                <ul class="nav flex-column m-3">
                    <li class="nav-item m-1" style="height:40px;">
                        <button class="btn btn-warning w-50 border-0 border-warning" data-bs-toggle="modal" data-bs-target="#newVictoryCardModal">
                            New Victory Card
                        </button>
                    </li>
                    <li class="nav-item m-1" style="height:40px;">
                        <button class="btn btn-danger w-50 border-0 border-dark" data-bs-toggle="modal" data-bs-target="#deathModal">
                            Oops I died
                        </button>
                    </li>
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase m-3 text-warning">
                        <span>Filter Settings</span>
                    </h6>
                    <li class="nav-item m-1">
                        <form method="get">
                            <input type="submit" name="fVC" value="Filter Victory Cards" class="btn btn-warning w-50 border-0 border-warning">
                            <select name='state' class="dropdown-toggle border-warning w-25 bg-warning">
                                <option value=''>-</option>
                                <?php write_dropdown($mysqli, "states", "name"); ?>
                            </select>

                        </form>
                    </li>
                    <li class="nav-item m-1">
                        <form method="get">
                            <input type="submit" name="sortVictoryCards" value="Sort Victory Cards" class="btn btn-warning w-50 border-0 border-warning">
                            <select name='sort' class="dropdown-toggle border-warning w-25 bg-warning">
                                <option value="status">STATUS</option>
                                <option value="type">TYPE</option>
                            </select>
                        </form>
                    </li>

                    <p></p>
                    <p></p>
                    <li>
                        <canvas id="ratioPieChart" style="background-color:#3e3546; width:100%; max-width:600px;"></canvas>
                    </li>
                    <li>
                        <canvas id="cardTimeChart" style="background-color:#3e3546; width:100%; max-width:600px;"></canvas>
                    </li>
                </ul>
            </div>
        </div>
        <div class="flex-fill">
            <div class="tab tab-victorycards">
                <?php

                if (!isset($order)) {
                    $order = "ORDER BY victorycards.state_id DESC, victoryconditions.name ASC";
                }

                @$sql =
                    "SELECT
                players.id as 'player_id',
                players.name AS 'player',
                victoryconditions.id AS 'victorycondition_id',
                victoryconditions.name AS 'name',
                victoryconditions.condition AS 'condition',
                victoryconditions.amount AS 'amount',
                targets.name AS 'target',
                targettypes.name AS 'type',
                victorycards.state_id AS 'status'
            FROM
                players 
                JOIN 
                victorycards ON players.id = victorycards.player_id
                JOIN
                victoryconditions ON victoryconditions.id = victorycards.victorycondition_id
                JOIN
                targets ON targets.id = victoryconditions.target_id
                JOIN
                targettypes ON targettypes.id = victoryconditions.targettype_id
                JOIN
                states ON states.id = victorycards.state_id
            WHERE
                states.name = IF('$status' = '-', states.name, '$status')
                AND
                players.name = IF($adminaccess = 1, players.name, '$user')
            $order
            ";


                $result = $mysqli->query($sql);     # get query result
                $rows = $result->fetch_all(MYSQLI_ASSOC);       # fetches all rows of the query result table as an array

                print("<div class='container flex-fill' style='overflow-y:scroll; height:1000px'>");

                $cardsperrow = 3;
                $card = 1;

                $statcount = [
                    "Done" => 0,
                    "Open" => 0,
                    "Failed" => 0,
                    "Invalid" => 0
                ];


                foreach ($rows as $row) {

                    $player_id = $row["player_id"];
                    $player = $row["player"];
                    $title = $row["name"];
                    $condition = $row["condition"];
                    $amount = $row["amount"];
                    $target = $row["target"];
                    $type = $row["type"];
                    $victorycondition_id = $row["victorycondition_id"];

                    if ($row["status"] == "2") {
                        $textclass = "success";
                        $status = "Done";
                    } else if ($row["status"] == "1") {
                        $textclass = "warning";
                        $status = "Open";
                    } else if ($row["status"] == "0") {
                        $textclass = "danger";
                        $status = "Failed";
                    } else {
                        $textclass = "secondary";
                        $status = "???";
                    }

                    if ($adminaccess == 1) {
                        $admincontent = "<p class='card-text'><small class='text-$textclass'>Player: $player</small></p>";
                    } else {
                        $admincontent = "";
                    }

                    if ($card == 1) {
                        print("<div class='card-group'>");
                    }

                    print("
                <div class='card m-3 bg-dark text-white border border-2 border-$textclass rounded-4'>
                    <div class='d-flex justify-content-between'>
                        <div class='card-body flex-grow-1'>
                            <h5 class='card-title'>$title</h5>
                            <p class='card-text'>$condition $amount $target</p>
                            <p class='card-text'><small class='text-$textclass'>Status: $status</small></p>
                            $admincontent
                        </div>
                        <form method='post'class='mt-3 flex-shrink-1 me-3'>
                        <input type='hidden' name='victorycondition_id' value='$victorycondition_id'>");

                    if ($status == "Open") {
                        print("<div><input type='submit' name='done' class='btn btn-success m-1' value='✓'></div>");
                    }
                    if ($status == "Done") {
                        print("<div><input type='submit' name='open' class='btn btn-warning m-1' value='-'></div>");
                    }

                    print("
                        </form>
                    </div>
                </div>
            ");

                    // Einfügen, um Fail-Butten z haben
                    // <div><input type='submit' name='fail' class='btn btn-danger m-1' value='✗'></div> 


                    if ($card == $cardsperrow) {
                        print("</div>");
                        $card = 1;
                    } else {
                        $card++;
                    }
                }

                print("</div>");
                print("</div>");

                $statcount = countStatistics($mysqli, $statcount, $user, $adminaccess);


                $json = "'" . json_encode($statcount) . "'";

                echo "<script type='text/javascript'>",
                "var statcount = $json;",
                "countStatistics(statcount);",
                "</script>";

                @$cardstatus = countCardStatus($mysqli, $cardstatus, $user, $adminaccess);

                $json = "'" . json_encode($cardstatus) . "'";

                echo "<script type='text/javascript'>",
                "var cardStatus = $json;",
                "countCardStatus(cardStatus);",
                "</script>";


                ?>
            </div>
        </div>

    </div>
    <div class="flex-fill">
        <div class="tab tab-othercards border-2 border-top border-warning">
            <h2 class="h2 text-center mx-auto">Andere Victorycards</h2>
            <?php

            if (!isset($order)) {
                $order = "ORDER BY victorycards.state_id DESC, victoryconditions.name ASC";
            }

            @$sql =
                "SELECT
                    players.id as 'player_id',
                    victoryconditions.id AS 'victorycondition_id',
                    victoryconditions.name AS 'name',
                    victoryconditions.condition AS 'condition',
                    victoryconditions.amount AS 'amount',
                    targets.name AS 'target',
                    targettypes.name AS 'type',
                    victorycards.state_id AS 'status'
                FROM
                    players 
                    JOIN 
                    victorycards ON players.id = victorycards.player_id
                    JOIN
                    victoryconditions ON victoryconditions.id = victorycards.victorycondition_id
                    JOIN
                    targets ON targets.id = victoryconditions.target_id
                    JOIN
                    targettypes ON targettypes.id = victoryconditions.targettype_id
                    JOIN
                    states ON states.id = victorycards.state_id
                WHERE
                    states.id = 1
                    AND
                    players.name != '$user'
                $order
                ";


            $result = $mysqli->query($sql);     # get query result
            $rows = $result->fetch_all(MYSQLI_ASSOC);       # fetches all rows of the query result table as an array

            print("<div class='container flex-fill' style='overflow-y:scroll; height:1000px'>");

            $cardsperrow = 5;
            $card = 1;


            foreach ($rows as $row) {

                $player_id = $row["player_id"];
                $title = $row["name"];
                $condition = $row["condition"];
                $amount = $row["amount"];
                $target = $row["target"];
                $type = $row["type"];
                $victorycondition_id = $row["victorycondition_id"];

                if ($row["status"] == "2") {
                    $textclass = "success";
                    $status = "Done";
                } else if ($row["status"] == "1") {
                    $textclass = "warning";
                    $status = "Open";
                } else if ($row["status"] == "0") {
                    $textclass = "danger";
                    $status = "Failed";
                } else {
                    $textclass = "secondary";
                    $status = "???";
                }

                if ($card == 1) {
                    print("<div class='card-group'>");
                }

                print("
                <div class='card m-3 bg-dark text-white border border-2 border-$textclass rounded-4'>
                    <div class='d-flex justify-content-between'>
                        <div class='card-body flex-grow-1'>
                            <h5 class='card-title'>$title</h5>
                            <p class='card-text'>$condition $amount $target</p>
                            <p class='card-text'><small class='text-$textclass'>Status: $status</small></p>
                        </div>
                    </div>
                </div>
            ");

                if ($card == $cardsperrow) {
                    print("</div>");
                    $card = 1;
                } else {
                    $card++;
                }
            }

            print("</div>");
            print("</div>");
            ?>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 mb-4 me-3">
        <p>You are currently logged in as <?php print("<kbd>" . $_SESSION["user"] . "</kbd>"); ?></p>
    </div>
    <div class="position-fixed bottom-0 end-0 me-3" id="logoutbutton">

        <!-- <p class="alert-link"><a href="http://127.0.0.1/dayz/login.php?end=true&user=<?php print($_SESSION["user"]); ?>">LOGOUT</p> -->

    </div>

    <div class="modal hide fade" id="newVictoryCardModal">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">New Victory Card</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post">
                        <div class="form-floating mt-3">
                            <p class="text-light">Select Target:</p>
                            <select name='target' class="dropdown-toggle w-25 bg-warning border-warning mb-3">
                                <option value=''>-</option>
                                <?php write_dropdown($mysqli, "targettypes", "name"); ?>
                            </select>
                            <input type="range" min="1" max="20" name="amount" id="amountNewVictoryCards" class="form-range" oninput="this.nextElementSibling.value = 'Amount of new Cards: ' + this.value">
                            <output class="text-light" id="amountNewVictoryCardsOutput">Amount of new Cards:</output>
                        </div>
                        <input type="submit" name="newVC" id="" value="Create" class="btn btn-primary text-dark text-weight-bold w-100 py-2 mt-3" style="background-color:#ffdb00; border:#ffdb00">
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <div class="modal hide fade" id="deathModal">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title text-danger">Please confirm your Death!</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body ">
                    <form method="get">
                        <input type="submit" name="deathVictoryCards" value="Yes, I died!" class="btn btn-danger w-50 border-0 border-warning">
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    new Chart("ratioPieChart", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "Meine Statistik",
                fontColor: "#ffdb00",
                fontSize: 20
            },
            legend: {
                labels: {
                    fontColor: "#ffffff",
                    fontSize: 14
                }
            }
        }
    });

    function transformData(data) {
        return Object.entries(data).map(([key, value]) => {
            return {
                x: key,
                y: value
            };
        });
    }

    let transformedOpenCards = transformData(openCards);
    let transformedDoneCards = transformData(doneCards);
    let transformedFailedCards = transformData(failedCards);

    let data = {
        labels: labels,
        datasets: [{
            label: "Done Cards",
            data: transformedDoneCards,
            borderColor: "#198754",
            backgroundColor: "#19875450",
            fill: false
        }, {
            label: "Open Cards",
            data: transformedOpenCards,
            borderColor: "#ffc107",
            backgroundColor: "#ffc10750",
            fill: false
        }, {
            label: "Failed Cards",
            data: transformedFailedCards,
            borderColor: "#dc3545",
            backgroundColor: "#dc354550",
            fill: false
        }]
    }

    new Chart("cardTimeChart", {
        type: "line",
        data: data,
        options: {
            title: {
                display: true,
                text: "Card Status over time",
                fontColor: "#ffdb00",
                fontSize: 20
            },
            legend: {
                labels: {
                    fontColor: "#ffffff",
                    fontSize: 14
                }
            },
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        suggestedMin: 0, // minimum will be 0, unless there is a lower value.
                        // OR //
                        beginAtZero: true // minimum value will be 0.
                    }
                }],
            }
        }
    });
    if (!$("#newVictoryCardModal").hasClass("show")) {
        $("#logoutbutton").html('<p class="alert-link"><a href="http://127.0.0.1/dayz/login.php?end=true&user=<?php print($_SESSION["user"]); ?>">LOGOUT</p>');
    } else {
        $("#logoutbutton").html('');
    }
</script>