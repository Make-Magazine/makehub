// Start putting login related scripts here and maybe someday we can even move in auth0

jQuery(document).ready(function(){
	var pathname = window.location.pathname;
	// make sure cookies and local storage are enabled in browser
	if((checkCookie() == false || lsTest() == false) && (beginsWith("/authenticate-redirect", pathname) || beginsWith("/authenticated", pathname))) {
		alert("We are unable to log you in. Please enable cookies in your browser and reattempt to login.");
		if(jQuery("#authenticated-redirect").length > 0) {
			jQuery("#authenticated-redirect").html("<h2 style='color:#ED1C24;text-align:center;'><b>We are unable to log you in.</b><br /><br />Please enable cookies in your browser and reattempt to login.</h2><br /><br />")
		}
	}
});
