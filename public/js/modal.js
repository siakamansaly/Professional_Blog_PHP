$(document).ready(function () {
    function GetURLParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split("&");
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split("=");
            if (sParameterName[0] === sParam) {
                return sParameterName[1];
            }
        }
    }
    var urlRegister = GetURLParameter("register");
    var urlLogin = GetURLParameter("login");
    if (urlRegister === "true") {
        $("#register").modal("show");
    }
    if (urlLogin === "true") {
        $("#login").modal("show");
    }
});