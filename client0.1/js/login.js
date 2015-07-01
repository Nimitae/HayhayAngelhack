$(document).ready(function() {
    if (localStorage.getItem("username") !== null ){
        window.location.replace("index.html");
    }
});

function login() {
    var url = "http://www.nimitae.sg/hayhay/server/register.php";
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var repeat = document.getElementById("repeat").value;
    if (password == repeat) {
        $.ajax({
            url: url,
            type: "POST",
            data: {username: email, password: password},
            success: processResult,
            error: whoops
        });
    } else {
        //TODO: Passwords not the same
    }
}

function processResult(data){
    var dataObj = jQuery.parseJSON(data);
    if (dataObj.result == "Success"){
        var email = document.getElementById("email").value;
        localStorage.setItem("username", email);
        window.location.replace("index.html");
    } else {
        //TODO: Failed to register new account
    }
}

function whoops(){
    //TODO: Something went wrong with ajax
}
