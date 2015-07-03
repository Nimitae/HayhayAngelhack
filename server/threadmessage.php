<?php
include("settings.php");
include("dbconfig.php");

//$_POST["threadID"] = "157";


$return = array();
if (isset($_POST["threadID"])) {
    $return['threadID'] = $_POST["threadID"];
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);
    $sql = "SELECT * FROM thread WHERE threadID = :threadID;";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":threadID", $_POST["threadID"]);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $return['threadTitle'] =  $result[0]["title"];

    $sql = "SELECT * FROM messages WHERE threadID = :threadID ORDER BY messageID ASC;";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":threadID", $_POST["threadID"]);
    if ($stmt->execute()) {
        $return['result'] = "Success";
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($result as $row){
            $message =array();
            $message["messageID"] = $row["messageID"];
            $message["messageContent"] = $row["message"];
            $resultArray[] = $message;
        }
        $return['messageList'] = $resultArray;
    } else {
        $return['result'] = "Failed";
    }
}
echo json_encode($return);