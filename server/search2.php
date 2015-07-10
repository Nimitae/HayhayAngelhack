<?php
include("settings.php");
include("dbconfig.php");



$return = array();
if (isset($_POST["search"]) && !empty($_POST["search"])){
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);
    $sql = "SELECT DISTINCT messages.threadID, thread.longitude, thread.latitude FROM messages LEFT JOIN thread
ON messages.threadID=thread.threadID WHERE message LIKE :searchterm AND (createdate >= DATE_SUB(NOW(),INTERVAL 1 DAY));";
    $stmt = $dbh->prepare($sql);
    $searchTerm =  "%#".$_POST["search"]."%";
    $stmt->bindParam(":searchterm",$searchTerm);
    if ($stmt->execute($sqlParams)){
        $return['result'] = "Success";
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($result as $row){
            $thread =array();
            $thread["threadID"] = $row["threadID"];
            $thread["longitude"] = $row["longitude"];
            $thread["latitude"] = $row["latitude"];
            $resultArray[] = $thread;
        }
        $return['threadList'] = $resultArray;
    } else {
        $return['info'] = $stmt->errorInfo();
        $return['result'] = "Failed";
    }
}

echo json_encode($return);
