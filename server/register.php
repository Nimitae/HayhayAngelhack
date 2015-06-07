<?php
include("settings.php");
include("dbconfig.php");
require_once('lib/OpenFireUserService.php');

/*
$_POST["username"] = "test3";
$_POST["password"] = "testpassword";
*/
$return = array();
if (isset($_POST["username"]) && isset($_POST["password"])) {
    // TBD: Check if user already exists

    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);

    $sql = "INSERT INTO user VALUES (? , ?);";
    $sqlParams[] = $_POST["username"];
    $sqlParams[] = $_POST["password"];
    $stmt = $dbh->prepare($sql);

    if ($stmt->execute($sqlParams)) {
        $pofus = new OpenFireUserService();

        $pofus->secret = "1";
        $pofus->host = "localhost";
        $pofus->port = "9090"; // default 9090

        $pofus->useCurl = false;
        $pofus->useSSL = false;
        $pofus->plugin = "/plugins/userService/userservice"; // plugin folder location

        $result = $pofus->addUser($_POST["username"], $_POST["password"]);
        if ($result) {
            $result['result'] ? $return['result'] = "Success" : $return['result'] = "Failed";
        } else {
            $return['result'] = "Failed";
        }
    } else {
        $return['result'] = "Failed";
    }
}

echo json_encode($return);