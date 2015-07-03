<?php
include("settings.php");
include("dbconfig.php");

/*
$_POST["longitude"] = 123123;
$_POST["latitude"] = 12300.52;
$_POST["range"] = 10000000;
*/
$_POST["range"] = 99999999;
$return = array();
if (isset($_POST["range"]) && isset($_POST["longitude"]) && isset($_POST["latitude"])) {
    $sqlParams = array();
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);

    $sql = "SELECT * FROM thread WHERE (longitude BETWEEN ? AND ?) AND (latitude BETWEEN ? AND ?) AND (createdate >= DATE_SUB(NOW(),INTERVAL 1 DAY));";
    $sqlParams[] = $_POST["longitude"]-$_POST["range"];
    $sqlParams[] = $_POST["longitude"]+$_POST["range"];
    $sqlParams[] = $_POST["latitude"]-$_POST["range"];
    $sqlParams[] = $_POST["latitude"]+$_POST["range"];
    $stmt = $dbh->prepare($sql);
    if ($stmt->execute($sqlParams)){
        $return['result'] = "Success";
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($result as $row){
            $thread =array();
            $thread["threadID"] = $row["threadID"];
            $thread["longitude"] = $row["longitude"];
            $thread["latitude"] = $row["latitude"];
            $thread["title"] = $row["title"];
            $resultArray[] = $thread;
        }
        $return['threadList'] = $resultArray;
    } else {
        $return['info'] = $stmt->errorInfo();
        $return['result'] = "Failed";
    }
}
echo json_encode($return);