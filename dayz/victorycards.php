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

    $title = $row["name"];
    $condition = $row["condition"];
    $amount = $row["amount"];
    $target = $row["target"];
    $type = $row["type"];

    if($row["done"] == "2"){
        $textclass = "success";
        $done = "Done";
    }else if($row["done"] == "1"){
        $textclass = "warning";
        $done = "Open";
    }else if($row["done"] == "0"){
        $textclass = "danger";
        $done = "Failed";
    }else{
        $textclass = "muted";
        $done = "???";
    }

    if($card == 1){
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

    if($card == $cardsperrow){
        print("</div>");
        $card = 1;
    }else{
        $card++;
    }
}
?>

<body>



    <div class="position-fixed bottom-0 end-0 mb-4 me-3">
        <p>You are currently logged in as <?php print("<kbd>" . $_SESSION["user"] . "</kbd>"); ?></p>
    </div>
    <div class="position-fixed bottom-0 end-0 me-3">
        <p class="alert-link"><a href="http://127.0.0.1/dayz/login.php?end=true&user=<?php print($_SESSION["user"]); ?>">LOGOUT</p>
    </div>
</body>