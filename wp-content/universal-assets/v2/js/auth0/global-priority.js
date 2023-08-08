//moved from global, but we need these loaded first like the rest of the auth0 code
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0)
            return c.substring(nameEQ.length, c.length);
    }
    return null;
}
function delete_cookie(name) {
    document.cookie = name + '=; expires=Tue, 16 Oct 1979 00:00:01 GMT;path=/';
}