<?php
include("settings.php");
include("dbconfig.php");

/*
$_POST["username"] = "test4";
$_POST["threadID"] = "13";
*/

$return = array();
if (isset($_POST["username"]) && isset($_POST["threadID"])) {
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);
    $sql = "INSERT INTO subscription VALUES (? , ?);";
    $sqlParams[] = $_POST["username"];
    $sqlParams[] = $_POST["threadID"];
    $stmt = $dbh->prepare($sql);
    if ($stmt->execute($sqlParams)) {
        $return['result'] = "Success";
    } else {
        $return['result'] = "Failed";
    }
}

echo json_encode($return);
