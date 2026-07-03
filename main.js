

function auth (){
    var org = document.getElementById("org").value;
    var ID = document.getElementById("wID").value;
    var password = document.getElementById("password").value;

    if (org == "admin" && password == "user"){
        window.location.replace("./home.php");
    } else {
        alert("Invalid Info");
        return;
    }
}