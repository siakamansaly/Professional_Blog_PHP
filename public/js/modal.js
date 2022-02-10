$(document).ready(function () {

    function GetURLParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam) {
                return sParameterName[1];
            }
        }
    }
    var url_register = GetURLParameter('register');
    var url_login = GetURLParameter('login');
    if (url_register == 'true') {
        $('#register').modal('show')
    }
    if (url_login == 'true') {
        $('#login').modal('show')
    }


});