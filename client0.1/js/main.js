$(document).ready(function () {
    var username = localStorage.getItem("username");
    console.log("Username: " + username);
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
    }
}

function showPosition(position) {
    lat = position.coords.latitude;
    lon = position.coords.longitude;
    latlon = new google.maps.LatLng(lat, lon);
    mapholder = document.getElementById('mapholder');
    var myOptions = {
        center: latlon, zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: false,
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
        disableDefaultUI: true
    };

    var map = new google.maps.Map(mapholder, myOptions);
    var marker = new google.maps.Marker({position: latlon, map: map, title: "You are here!"});
    // session.here = {latitude: lat, longitude: lon};
    /* function listThreads() {
     var url = "http://www.nimitae.sg/hayhay/server/listing.php";
     $.ajax({
     url: url,
     type: "POST",
     data: {longitude: session.here.longitude, latitude: session.here.latitude, range: session.range},
     success: populate,
     error: whoops
     });
     }*/
/*
    function populate(list) {
        obj = JSON.parse(list);
        for (var i = 0; i < obj.threadList.length; i += 1) {
            $("#home").append("<div class='row'> " + obj.threadList[i].threadID + ":\t" +
                obj.threadList[i].title + "</div>");
            makeMarker(map, obj.threadList[i]);
        }
    }
*/
    // listThreads();
}

function showError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
            break;
    }
}