<?php
include("settings.php");
include("dbconfig.php");


if (isset($_POST["search"]) && !empty($_POST["search"])){
    $dbh = new PDO($DBCONFIG["connstring"], $DBCONFIG["username"], $DBCONFIG["password"]);
    $sql = "SELECT DISTINCT messages.threadID, thread.longitude, thread.latitude FROM messages LEFT JOIN thread
ON messages.threadID=thread.threadID WHERE message LIKE :searchterm;";
    $stmt = $dbh->prepare($sql);
    $searchTerm =  "%".$_POST["search"]."%";
    $stmt->bindParam(":searchterm",$searchTerm);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Hayhay Search</title>
    <style>
        #map-canvas {
            text-align: center;
            width: 1000px;
            height: 500px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="search.css">
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Nunito">
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script>
        function initialize() {
            var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
            var mapOptions = {
                zoom: 1,
                center: myLatlng
            };
            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
            <?php foreach ($result as $location)  :?>
            myLatlng = new google.maps.LatLng(<?php print $location["latitude"] . "," . $location["longitude"] ;?>);
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
            });
            <?php endforeach; ?>
        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
<div class="header">
    <img id="hayhay" src="hayhay.png">
</div>

<div class="search">
    <form method="post">
        <input id="searchbar" type="text" name="search" placeholder="search keywords" <?php isset($_POST["search"]) ? print 'value="' . $_POST['search'] . '"' : print  ""; ?>">
    </form>
</div>
<br><br>
<div style="align-content: center">
    <div id="map-canvas"></div>
</div>
</body>
</html>
