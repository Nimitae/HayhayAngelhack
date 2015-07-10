<?php
include("settings.php");
include("dbconfig.php");
require_once('lib/XMPPHP/XMPP.php');
define("PRIVATE_CHAT", 1);
define("GROUP_CHAT", 2);

/*
$_POST["username"] = "test4";
$_POST["type"] = GROUP_CHAT;
$_POST["message"] = "test group chat";
$_POST["threadID"] = 18;
//$_POST["receiver"] = "kkk@hayhay";
*/

$return = array();
if (isset($_POST["username"]) && isset($_POST["type"]) && isset($_POST["message"])) {
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);
    $sql = "SELECT * FROM user WHERE username = ?;";
    $sqlParams[] = $_POST["username"];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($sqlParams);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $username = $result[0]["username"];
    $password = $result[0]["password"];
    $messageType = $_POST["type"];
    if ($messageType == PRIVATE_CHAT) {
        $conn = new XMPPHP_XMPP("localhost", 5222, $username, $password, "hayhay");
        try {
            $conn->useEncryption(false);
            $conn->connect();
            $conn->processUntil('session_start');
            $conn->presence();
            $conn->message($_POST["receiver"], $_POST["message"]);
            $conn->disconnect();
            $return['result'] = "Success";
        } catch (XMPPHP_Exception $e) {
            $return['result'] = "Failed";
            error_log($e->getMessage());
        }
    } else if ($messageType == GROUP_CHAT) {

        $threadID = $_POST["receiver"];
        /*
        $sql = "SELECT * FROM subscription WHERE threadID = :threadID ;";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":threadID", $threadID);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn = new XMPPHP_XMPP("localhost", 5222, $username, $password, "hayhay");
        try {
            $conn->useEncryption(false);
            $conn->connect();
            $conn->processUntil('session_start');
            $conn->presence();
            foreach ($results as $row) {
                $conn->message($row["username"] . "@hayhay", $_POST["message"], $threadID);
            }
            $conn->disconnect();
            $return['result'] = "Success";
        } catch (XMPPHP_Exception $e) {
            $return['result'] = "Failed";
            error_log($e->getMessage());
        }
        */
        $return['result'] = "Success";
        $sql = "INSERT INTO messages VALUES (NULL, :threadID, :message, :username, :messagetime);";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":threadID", $threadID);
        $stmt->bindParam(":message", $_POST["message"]);
        $stmt->bindParam(":username", $_POST["username"]);
        $stmt->bindParam(":messagetime", date('Y-m-d H:i:s',time()));
        $stmt->execute();
    } else {
        $return['result'] = "Failed";
    }
}

echo json_encode($return);