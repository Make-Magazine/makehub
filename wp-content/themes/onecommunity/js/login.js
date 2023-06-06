window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

if(document.getElementById('shortcode-login-submit')){

if(document.querySelectorAll('login-popup-action')){
    document.querySelectorAll('.login-popup-action').forEach(function(el){
    el.addEventListener('click', function() {
        document.querySelectorAll('.popup-shortcode-login')[0].classList.add('show', 'fadein');
        document.getElementById('shortcode-user-login').focus();
        document.getElementById('shortcode-user-login').select();
    });
    });
}

if(document.querySelector('.shortcode-login-close')){
    document.querySelector('.shortcode-login-close').addEventListener('click', function() {
        document.querySelectorAll('.popup-shortcode-login')[0].classList.remove('show', 'fadein');
    });
}

if(document.querySelector('.header-top-login-jump')){
    document.querySelectorAll('.header-top-login-jump')[0].addEventListener('click', function() {
        document.querySelectorAll('.shortcode-login')[0].classList.add('jump-animation');
    });
}

    document.getElementById('shortcode-login-submit').addEventListener('click', function(e) {
    var status = document.querySelectorAll('form.shortcode-login-form .status')[0];
    status.classList.add('show', 'fadein');
    status.innerHTML = login_js.loadingmessage;

    var r = new XMLHttpRequest();
    r.open('POST', ajaxurl, true);
    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    r.onload = function () {
    if (this.status >= 200 && this.status < 400) {

        var respObj = JSON.parse(this.response);
        status.innerHTML = respObj.message;

        if (respObj.loggedin === true){
            location.reload();
        }

    } else {
        // Response error
        console.log('Response error');
    }
    };
    r.onerror = function() {
    // Connection error
        console.log('Connection error');
    };
    r.send('action=ajaxlogin&username=' + escape(document.querySelectorAll('form.shortcode-login-form #shortcode-user-login')[0].value) + '&password=' + escape(document.querySelectorAll('form.shortcode-login-form #shortcode-user-pass')[0].value) + '&rememberme=' + document.querySelectorAll('form.shortcode-login-form .shortcode-rememberme')[0].value + '');

        e.preventDefault();

    });



    document.querySelector('.shortcode-login .password-show').addEventListener('click', function() {
    document.querySelector('.shortcode-login .password-show').classList.toggle('hidden');
    var el = document.getElementById("shortcode-user-pass");
    if ( el.getAttribute('type') == "password" ) {
        el.setAttribute("type", "text");
    } else {
        el.setAttribute("type", "password");
    }
    });

}

});


function loginPopUp() {
document.querySelector('.popup-shortcode-login').classList.add('show', 'fadein');
}