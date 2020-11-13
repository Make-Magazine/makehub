window.addEventListener('load', function() {
	
	var url = new URL(location.href).hostname;
	var wplogin_domains = ["makerfaire", "campus", "makerspaces", "community", "makehub"];
	var wpLoginRequired = false;
	for (var i = 0, ln = wplogin_domains.length; i < ln; i++) {
	  if (url.indexOf(wplogin_domains[i]) !== -1) {
		wpLoginRequired = true;
		break;
	  }
	}

	var userProfile;
	
	var webAuth = new auth0.WebAuth({
			domain: AUTH0_DOMAIN,
			clientID: AUTH0_CLIENT_ID,
			redirectUri: AUTH0_CALLBACK_URL,
			audience: 'https://' + AUTH0_DOMAIN + '/userinfo',
			responseType: 'token id_token',
			scope: 'openid profile email user_metadata',
			//scope of data pulled by auth0
			leeway: 60
		});
	
	//check if logged in another place
	webAuth.checkSession({},
		function(err, result) {
			if (err) {
				clearLocalStorage();
				if(err.error!=='login_required'){
					errorMsg(userProfile.email + " had an issue logging in at the checkSession phase. That error was: " + JSON.stringify(err));
				}
			} else {
				setSession(result);
			}
			displayButtons();
		}
	);

	// if the auth0 plugin is not present, we need to hijack the login buttons ourselves
	if(wpLoginRequired == false) {
		jQuery("#LoginBtn").on('click', function(e) {
			e.preventDefault();
			// these can probably be removed if we're sticking to the same page
			setCookie("login_referer", url, .05);
			localStorage.setItem('redirect_to',url);
			webAuth.authorize(); //login to auth0
		});
		jQuery("#LogoutBtn").on('click', function(e) {
			e.preventDefault();
			clearLocalStorage()
			//redirect to auth0 logout page
			window.location.href = 'https://makermedia.auth0.com/v2/logout?returnTo='+templateUrl+ '&client_id='+AUTH0_CLIENT_ID;
		});
	}

	function clearLocalStorage() {
		 localStorage.removeItem('access_token');
		 localStorage.removeItem('id_token');
		 localStorage.removeItem('expires_at');
    }

	function setSession(authResult) {
		if ( authResult ) {
			// Set the time that the access token will expire at
			var expiresAt = JSON.stringify(
				authResult.expiresIn * 1000 + new Date().getTime()
			);
			localStorage.setItem('access_token', authResult.accessToken);
			localStorage.setItem('id_token', authResult.idToken);
			localStorage.setItem('expires_at', expiresAt);
		}else {
			clearLocalStorage();
		}
	}
	function displayButtons() {
		if (localStorage.getItem('expires_at')) {
			jQuery("#profile-view").css('display', 'flex');
			getProfile();
		} else {
			jQuery("#profile-view").css('display', 'none');
			WPlogout();
		}
	}

	function getProfile() {
		var accessToken = localStorage.getItem('access_token');

		if (!accessToken) {
			console.log('Access token must exist to fetch profile');
			errorMsg('Login attempted without Access Token');
		}

		webAuth.client.userInfo(accessToken, function(err, profile) {
			if (profile) {
				userProfile = profile;
				// make sure that there isn't a wordpress acount with a different user logged in
				if(ajax_object.wp_user_email && ajax_object.wp_user_email != userProfile.email) {
					WPlogout("wp_only");
				}
				// display the avatar
				document.querySelector('.dropdown-toggle img').src = userProfile.picture;
				document.querySelector('.profile-info img').src = userProfile.picture;
				document.querySelector('.dropdown-toggle img').style.display = "block";
				document.querySelector('#LoginBtn').style.display = "none";
				document.querySelector('.profile-email').innerHTML = userProfile.email; 
				if(userProfile['http://makershare.com/first_name'] != undefined && userProfile['http://makershare.com/last_name'] != undefined) {
				document.querySelector('.profile-info .profile-name').innerHTML = userProfile['http://makershare.com/first_name'] + " " + userProfile['http://makershare.com/last_name'];
				}
				if(wpLoginRequired) {
					WPlogin();
				}
			}
			if (err) {
				errorMsg("There was an issue logging in at the getProfile phase. That error was: " + JSON.stringify(err));
			}
		});
	}
	
	function WPlogin(){
		if (typeof userProfile !== 'undefined') {
			var user_id      = userProfile.sub;
			var access_token = localStorage.getItem('access_token');
			var id_token     = localStorage.getItem('id_token');

			//login to wordpress
			var data = {
				'action'              : 'mm_wplogin',
				'auth0_userProfile'   : userProfile,
				'auth0_access_token'  : access_token,
				'auth0_id_token'      : id_token
			};
  			console.log(ajax_object.ajax_url);
			jQuery.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: data,
				timeout: 10000,
				success: function(data){
					console.log(data);
				},
			}).fail(function(xhr, status, error) {
				if(status === 'timeout') {
					 alert( "Your login has timed out. Please try the login again." );
					 errorMsg(userProfile.email + " ran over the timeout limit of 10 seconds. Error was: " + JSON.stringify(error));
					 location.href = location.href; // reload in the hopes it was just a temp server blip
				} else {
					 alert( "I'm sorry. We had an issue logging you into our system. Please try the login again." );
					 errorMsg(userProfile.email + " had an issue logging in at the WP Login phase. That error is: " + JSON.stringify(xhr));
				}
			});
		}
	}
	
	function WPlogout(wp_only){
		if ( jQuery( '#wpadminbar' ).length ) {
			jQuery( 'body' ).removeClass( 'adminBar' ).removeClass( 'logged-in' );
			jQuery( '#wpadminbar' ).remove();
			jQuery( '#mm-preview-settings-bar' ).remove();
		}
		var data = { 'action': 'mm_wplogout' };
		jQuery.post(ajax_object.ajax_url, data, function(response) {
			if(wp_only != "wp_only"){
				window.location.href = 'https://makermedia.auth0.com/v2/logout?returnTo='+templateUrl+ '&client_id='+AUTH0_CLIENT_ID;
			}else{
				// In cases where there is already a different wp user, log back in with  new user
				if(wpLoginRequired) {
					WPlogin();
				}
			}
		});
	}

	function errorMsg(message) {
		var data = {
			'action'       : 'make_error_log',
			'make_error'   : message
		};
		jQuery.post(ajax_object.ajax_url, data, function(response) {});	
	}
});