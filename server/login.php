<?php
include("settings.php");
include("dbconfig.php");
/*
$_POST["username"] = "test3";
$_POST["password"] = "testpassword";
*/
$return = array();
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);

    $sql = "SELECT * FROM user WHERE username = ? AND password = ?;";
    $sqlParams[] = $_POST["username"];
    $sqlParams[] = $_POST["password"];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($sqlParams);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($result)){
        $return['result'] = "Failed";
    } else {
        $return['result'] = "Success";
    }
}

echo json_encode($return);