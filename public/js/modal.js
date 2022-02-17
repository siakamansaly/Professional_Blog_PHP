$(document).ready(function () {
       $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results[1] || 0;
    }
    var urlRegister = $.urlParam('register');
    var urlLogin = $.urlParam('login');
    if (urlRegister === "true") {
        $("#register").modal("show");
    }
    if (urlLogin === "true") {
        $("#login").modal("show");
    }
});