$(document).ready(function () {
    var username = localStorage.getItem("username");
    var password = localStorage.getItem("password");
    console.log("Username: " + username);
    console.log("Password: " + password);
    if (localStorage.getItem("username") == null || localStorage.getItem("password") == null) {
        window.location.replace("login.html");
    }
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
    }
}

function showPosition(position) {
    localStorage.setItem("latitude", position.coords.latitude);
    localStorage.setItem("longitude", position.coords.longitude);
    console.log("Latitude: " + localStorage.getItem("latitude") + ", Longitude: " + localStorage.getItem("longitude"));
    lat = position.coords.latitude;
    lon = position.coords.longitude;
    latlon = new google.maps.LatLng(lat, lon);
    mapholder = document.getElementById('mapholder');
    var myOptions = {
        center: latlon, zoom: 19,
        scrollwheel: false,
        navigationControl: false,
        mapTypeControl: false,
        scaleControl: false,
        draggable: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
        disableDefaultUI: true
    };

    var map = new google.maps.Map(mapholder, myOptions);
    //var marker = new google.maps.Marker({position: latlon, map: map, title: "You are here!"});
    // session.here = {latitude: lat, longitude: lon};
    function listThreads() {
        var url = "http://hayhay.nimitae.sg/server/listing.php";
        $.ajax({
            url: url,
            type: "POST",
            data: {longitude: localStorage.getItem("longitude"), latitude: localStorage.getItem("latitude"), range: 1},
            success: populate,
            error: whoops
        });
    }

    function populate(list) {
        clearThreadList();
        obj = JSON.parse(list);
        console.log(list);
        for (var i = 0; i < obj.threadList.length; i += 1) {
            $("#thread-list").append("<div class='threadrow' onclick='viewThread(" + obj.threadList[i].threadID + ");' >" + obj.threadList[i].threadID + ":\t" +
                obj.threadList[i].title + "</div>");
            makeMarker(map, obj.threadList[i]);
        }
    }

    listThreads();
}

function makeMarker(map, curr) {
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(curr.latitude, curr.longitude),
        map: map,
        title: curr.title
    });
    var clos = function () {
        return curr.threadID;
    };
    google.maps.event.addListener(marker, 'click', function () {
        viewThread(clos());
    })
}

function viewThread(threadID) {
    show("thread");
    console.log("Viewing threadID: " + threadID);
    var url = "http://hayhay.nimitae.sg/server/threadmessage.php";
    $.ajax({
        url: url,
        type: "POST",
        data: {threadID: threadID},
        success: processThreadMessages,
        error: whoops
    });
}

function processThreadMessages(data) {
    obj = JSON.parse(data);
    console.log(data);
    var threadTitleDOM = document.getElementById("threadTitle");
    threadTitleDOM.innerHTML = obj.threadTitle;

    localStorage.setItem("threadID", obj.threadID);
    console.log(localStorage.getItem("threadID"));

    clearChatHistory();

    var colorToUserObj = {};
    for (var i = 0; i < obj.messageList.length; i += 1) {
        if (colorToUserObj.hasOwnProperty(obj.messageList[i].username)) {

        } else {
            colorToUserObj[obj.messageList[i].username] = getRandomColor();
        }

        if (obj.messageList[i].username == localStorage.getItem("username")) {
            $("#chathistory").append("<div class='mychatrow'><p class='message'>" + obj.messageList[i].messageContent + "</p><p class='timestamp'>" + obj.messageList[i].timeMessage + "</p></div>");
        } else {
            $("#chathistory").append("<div class='chatrow' style='border-left-color:" + colorToUserObj[obj.messageList[i].username] + " '><p class='message'>" + obj.messageList[i].messageContent + "</p><p class='timestamp'>" + obj.messageList[i].timeMessage + "</p></div>");
        }
    }
}

function clearChatHistory() {
    var chatHistory = document.getElementById("chathistory");
    while (chatHistory.firstChild) {
        chatHistory.removeChild(chatHistory.firstChild);
    }
}

function clearThreadList() {
    var threadList = document.getElementById("thread-list");
    while (threadList.firstChild) {
        threadList.removeChild(threadList.firstChild);
    }
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

function startThread() {
    var text = prompt("Say Hay! (Enter a title)", "");
    if (!isBlank(text)) {
        var url = "http://hayhay.nimitae.sg/server/create.php";
        $.ajax({
            url: url,
            type: "POST",
            data: {
                title: text,
                longitude: localStorage.getItem("longitude"),
                latitude: localStorage.getItem("latitude"),
                username: localStorage.getItem("username")
            },
            success: processNewThread,
            error: whoops
        });
    }
}

function processNewThread(data) {
    console.log(data);
    obj = JSON.parse(data);
    localStorage.setItem("threadID", obj.threadID);
    viewThread(obj.threadID);
}

function whoops() {
    console.log("whoops");
    //TODO: Something went wrong with ajax
}

function sendMessage(obj) {
    console.log("Sending message to threadID: " + localStorage.getItem("threadID"));
    var message = document.getElementById("newmessage").value;
    console.log("Message is: " + message);
    var url = "http://hayhay.nimitae.sg/server/message.php";
    document.getElementById("newmessage").value = "";
    $.ajax({
        url: url,
        type: "POST",
        data: {
            username: localStorage.getItem("username"),
            type: 2,
            message: message,
            receiver: localStorage.getItem("threadID")
        },
        success: processSentMessageResponse,
        error: whoops
    });
}

function processSentMessageResponse(data) {
    console.log(data);
    viewThread(localStorage.getItem("threadID"));
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}

function searchNearby() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(searchMap, showError);
    } else {
    }
}

function searchMap(position) {
    localStorage.setItem("latitude", position.coords.latitude);
    localStorage.setItem("longitude", position.coords.longitude);
    console.log("Latitude: " + localStorage.getItem("latitude") + ", Longitude: " + localStorage.getItem("longitude"));
    lat = position.coords.latitude;
    lon = position.coords.longitude;
    latlon = new google.maps.LatLng(lat, lon);
    mapholder = document.getElementById('mapholder2');
    var myOptions = {
        center: latlon, zoom: 19,
        scrollwheel: false,
        navigationControl: false,
        mapTypeControl: false,
        scaleControl: false,
        draggable: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
        disableDefaultUI: true
    };
git
    var map = new google.maps.Map(mapholder, myOptions);
    //var marker = new google.maps.Marker({position: latlon, map: map, title: "You are here!"});
    // session.here = {latitude: lat, longitude: lon};
    function listThreads() {
        var url = "http://hayhay.nimitae.sg/server/search2.php";
        var searchText = document.getElementById('searchText').value;
        $.ajax({
            url: url,
            type: "POST",
            data: {search: searchText},
            success: populate,
            error: whoops
        });
    }

    function populate(list) {
        clearThreadList();
        obj = JSON.parse(list);
        console.log(list);
        if (obj.hasOwnProperty('threadList')) {
            for (var i = 0; i < obj.threadList.length; i += 1) {
                $("#thread-list").append("<div class='threadrow' onclick='viewThread(" + obj.threadList[i].threadID + ");' >" + obj.threadList[i].threadID + ":\t" +
                    obj.threadList[i].title + "</div>");
                makeMarker(map, obj.threadList[i]);
            }
        }
    }

    listThreads();

}
