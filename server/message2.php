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
if (isset($_POST["receiver"]) && isset($_POST["message"])) {
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);
        $sql = "INSERT INTO messages VALUES (NULL, :threadID, :message);";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":threadID", $_POST["receiver"]);
        $stmt->bindParam(":message", $_POST["message"]);
        if ($stmt->execute()) {
            $return["status"] = "Success";
        } else {
            var_dump($stmt->errorInfo());
            $return["status"] = "Failed";
        }
}

echo json_encode($return);

?>

<form method="post">
    <input type="text" placeholder="threadID" name="receiver">
    <input type="text" placeholder="message" name="message">
    <input type="submit">
</form>