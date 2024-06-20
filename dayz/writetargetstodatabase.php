<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php

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
    $params = [
        "action" => "query",
        "format" => "json",
        "list" => "allpages",
        "aplimit" => "500",
        "apfrom" => ""
    ];

    function getAPIResult($params)
    {
        $endPoint = "http://dayz.wiki/api.php";

        $url = $endPoint . "?" . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

    $result = getAPIResult($params);

    $entries = $result["query"]["allpages"];

    $params["apfrom"] = $result["query-continue"]["allpages"]["apcontinue"];

    $result = getAPIResult($params);

    $entries = array_merge($entries, $result["query"]["allpages"]);

    $mysqli = new mysqli("localhost", "root", "", "dayz"); # get database
    if ($mysqli->connect_errno) {
        die("Verbindung fehlgeschlagen" . $mysqli->connect_error);
    }


    foreach ($entries as $entry) {
        $sql = "INSERT INTO `dayz`.`targets` (`name`, `targettype_id`) VALUES (\"" . $entry["title"] . "\", 7)";
        console_log($sql); #log the sql query to the console (for debugging purposes
        $mysqli->query($sql);
    }

    console_log($entries);
    ?>
</body>

</html>