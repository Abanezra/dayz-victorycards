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
            background-image: url('./assets/backdrop.png');
            background-repeat: no-repeat;
            background-size: 100%;
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
    players.id as 'playerid',
    victoryconditions.name AS 'name',
    victoryconditions.condition AS 'condition',
    victoryconditions.amount AS 'amount',
    targets.name AS 'target',
    targettypes.name AS 'type',
    victorycards.done AS 'done'
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
WHERE
    players.name = IF($adminaccess = 1, players.name, '$user')
";

$result = $mysqli->query($sql);     # get query result
$rows = $result->fetch_all(MYSQLI_ASSOC);       # fetches all rows of the query result table as an array


$cardsperrow = 3;
$card = 1;
foreach ($rows as $row) {

    $player_id = $row["playerid"];
    $title = $row["name"];
    $condition = $row["condition"];
    $amount = $row["amount"];
    $target = $row["target"];
    $type = $row["type"];

    if ($row["done"] == "2") {
        $textclass = "success";
        $done = "Done";
    } else if ($row["done"] == "1") {
        $textclass = "warning";
        $done = "Open";
    } else if ($row["done"] == "0") {
        $textclass = "danger";
        $done = "Failed";
    } else {
        $textclass = "muted";
        $done = "???";
    }

    if ($card == 1) {
        print("<div class='card-group'>");
    }

    print("
        <div class='card m-3'>
            <div class='card-body'>
                <h5 class='card-title'>$title</h5>
                <p class='card-text'>$condition $amount $target</p>
                <p class='card-text'><small class='text-$textclass'>Status: $done</small></p>
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
            }else{
                $amount = "NULL";
            }
            $status = 2;
        }
    }

    $vcconditionid = insertVictoryCondition($mysqli, $VCTitle, $VCCondition, $targettype[0], $target[0], $amount);
    insertVictoryCard($mysqli, $player_id, $vcconditionid);
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
        INTO `dayz`.`victorycards` ( `code`, `player_id`, `victorycondition_id`, `done` ) 
        VALUES ( '$code', $player_id, '$victorycondition_id', 1 )
    ";
    $mysqli->query($sql);
}

if (isset($_POST["nVC"])) {
    newVictorycard($mysqli, $player_id);
}

?>

<body>

    <form method="post">
        <input type="submit" name="nVC" value="New Victory Card">
    </form>

    <div class="position-fixed bottom-0 end-0 mb-4 me-3">
        <p>You are currently logged in as <?php print("<kbd>" . $_SESSION["user"] . "</kbd>"); ?></p>
    </div>
    <div class="position-fixed bottom-0 end-0 me-3">
        <p class="alert-link"><a href="http://127.0.0.1/dayz/login.php?end=true&user=<?php print($_SESSION["user"]); ?>">LOGOUT</p>
    </div>
</body>