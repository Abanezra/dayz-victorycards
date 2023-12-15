<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Victorycards</title>

    <link rel="stylesheet" href="./victorycards.css">

    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background-color: #3e3546;
            color: #ffdb00;
        }
    </style>

</head>
<?php
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

function newVictorycard($mysqli, $player_id)
{

    $targettypes = getTargetTypes($mysqli);
    $targettype = $targettypes[array_rand($targettypes)];

    $targets = getTargets($mysqli, $targettype[1]);
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
        "player" => "Kill",
        "animal" => "Hunt",
        "infected" => "Dispatch",
        "location" => "Visit",
        "item" => "Get",
        "vehicle" => "Drive"
    ];

    foreach ($titles as $type => $title) {
        if ($type == $targettype[1]) {
            $VCTitle = $titles[$type];
            $VCCondition = $conditions[$type];
            if ($type == "animal" || $type == "infected" || $type == "item") {
                $amount = rand(1, 3);
            } else {
                $amount = "NULL";
            }
            $status = 2;
        }
    }

    $vcconditionid = insertVictoryCondition($mysqli, $VCTitle, $VCCondition, $targettype[0], $target[0], $amount);
    insertVictoryCard($mysqli, $player_id, $vcconditionid);

    @header('Location: victorycards.php');
}

function getTargetTypes($mysqli)
{
    $sql =
        "SELECT
            targettypes.id AS 'id',
            targettypes.name AS 'targettype'
        FROM
            targettypes
    ";

    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        $targettypes[$row["id"]] = [$row["id"], $row["targettype"]];
    }

    return $targettypes;
}

function getTargets($mysqli, $targettype)
{
    $sql =
        "SELECT
            targets.id AS 'id',
            targets.name AS 'target'
        FROM
            targets
            JOIN
            targettypes ON targettypes.id = targets.targettype_id
        WHERE
            targettypes.name = '$targettype'
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
        VALUES ( '$id', '$name', '$condition', $targettype_id, $target_id, $amount )
    ";
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
            victoryconditions.id = '$id'
    ";
    $result = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);

    return $result[0]["id"];
}

function insertVictoryCard($mysqli, $player_id, $victorycondition_id)
{
    $code = uniqid();
    $sql =
        "INSERT 
        INTO `dayz`.`victorycards` ( `code`, `player_id`, `victorycondition_id`, `state_id` ) 
        VALUES ( '$code', $player_id, '$victorycondition_id', 1 )
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
        print("<option name=$name class='bg-warning dropdown-item'>" . strtoupper($name) . "</option>");     # print the $name as an option in the dropdown
    }
}



if (isset($_POST["nVC"])) {

    $sql =
        "SELECT
            players.id AS player_id
        FROM
            players
        WHERE players.name = IF($adminaccess = 1, players.name, '$user')
    ";

    $result = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);     # get query result

    $player_id = $result[0]["player_id"];
    newVictorycard($mysqli, $player_id);
}

if (isset($_POST["done"])) {

    $victorycondition_id = $_POST["victorycondition_id"];

    $sql =
        "UPDATE 
            dayz.victorycards 
        SET state_id=2 
        WHERE  victorycondition_id = '$victorycondition_id';
    ";

    $mysqli->query($sql);

    @header('Location: victorycards.php');
}

if (isset($_POST["fail"])) {

    $victorycondition_id = $_POST["victorycondition_id"];

    $sql =
        "UPDATE 
            dayz.victorycards 
        SET state_id=0 
        WHERE  victorycondition_id = '$victorycondition_id';
    ";

    $mysqli->query($sql);

    @header('Location: victorycards.php');
}

?>

<body>

    <div class="d-flex">
        <div class="flex-fill border-end border-3 border-warning">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase m-3 text-warning">
                <span>Filter Settings</span>
            </h6>
            <ul class="nav flex-column m-3">
                <li class="nav-item m-1">
                    <form method="post">
                        <input type="submit" name="nVC" value="New Victory Card" class="btn btn-warning w-25">
                    </form>
                </li>
                <li class="nav-item m-1">
                    <form method="get">
                        <input type="submit" name="fVC" value="Filter Victory Cards" class="btn btn-warning w-25 border-0 border-warning">
                        <select name='state' class="dropdown-toggle w-25 bg-warning">
                            <option value=''>-</option>
                            <?php write_dropdown($mysqli, "states", "name"); ?>
                        </select>

                    </form>
                </li>
                <li class="nav-item m-1">
                    <form method="get">
                        <input type="submit" name="sVC" value="Sort Victory Cards" class="btn btn-warning w-25 border-0 border-warning">
                        <select name='sort' class="dropdown-toggle w-25 bg-warning">
                            <option value=''>-</option>

                        </select>
                    </form>
                </li>
            </ul>
        </div>
        <div class="flex-fill">
            <?php

            $sql =
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
        states.name = IF('$status' = '-', states.name, '$status')
        AND
        players.name = IF($adminaccess = 1, players.name, '$user')
    ";


            $result = $mysqli->query($sql);     # get query result
            $rows = $result->fetch_all(MYSQLI_ASSOC);       # fetches all rows of the query result table as an array

            print("<div class='container' style='overflow-y:scroll; height:1000px'>");

            $cardsperrow = 3;
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
                        <form method='post'class='mt-3 flex-shrink-1 me-3'>
                            <input type='hidden' name='victorycondition_id' value='$victorycondition_id'>
                            <div><input type='submit' name='done' class='btn btn-success m-1' value='✓'></div>
                            <div><input type='submit' name='fail' class='btn btn-danger m-1' value='✗'></div>
                        </form>
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
    <div class="position-fixed bottom-0 end-0 me-3">
        <p class="alert-link"><a href="http://127.0.0.1/dayz/login.php?end=true&user=<?php print($_SESSION["user"]); ?>">LOGOUT</p>
    </div>
</body>