<?php
include("settings.php");
include("dbconfig.php");
require_once('lib/OpenFireUserService.php');


$return = array();
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);
    $sql = "SELECT * FROM user WHERE username = ?;";
    $sqlParams[] = $_POST["username"];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($sqlParams);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (sizeof($result) > 0) {
        $return['result'] = "Failed";
        $return['reason'] = "Username already exists!";
    } else {
        $sql = "INSERT INTO user VALUES (? , ?);";
        $sqlParams = array();
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
                $return['reason'] = "Unable to connect to Openfire Service.";
            }
        } else {
            $return['result'] = "Failed";
            $return['reason'] = "SQL Database insertion failure.";
        }
    }
}
echo json_encode($return);