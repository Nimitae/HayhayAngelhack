<?php
include("settings.php");
include("dbconfig.php");
/*
$_POST["title"] = "New Thread!";
$_POST["longitude"] = 123123;
$_POST["latitude"] = 12300.52;
$_POST["username"] = "test4";
*/

$return = array();
if (isset($_POST["title"]) && isset($_POST["longitude"]) && isset($_POST["latitude"]) && isset($_POST["username"])) {
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);

    $sql = "INSERT INTO thread VALUES (NULL, ?, ?, ?, ?, NULL);";
    $sqlParams[] = $_POST["longitude"];
    $sqlParams[] = $_POST["latitude"];
    $sqlParams[] = $_POST["title"];
    $sqlParams[] = $_POST["username"];
    $stmt = $dbh->prepare($sql);
    if ($stmt->execute($sqlParams)) {
        $return['result'] = "Success";
        $return['threadID'] = $dbh->lastInsertId();
    } else {
        $return['result'] = "Failed";
    }
}
echo json_encode($return);
