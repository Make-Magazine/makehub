var AUTH0_CLIENT_ID    = 'DRzbPDCo8kW5sDQ0e5Ynl0yaCAS2iR6e' 
var AUTH0_DOMAIN       = 'makermedia.auth0.com';

if (typeof templateUrl === 'undefined') {
  var templateUrl = window.location.origin;
}
var AUTH0_CALLBACK_URL = templateUrl + "/authenticate-redirect/";
var AUTH0_REDIRECT_URL = templateUrl;