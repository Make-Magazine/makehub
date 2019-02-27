window.addEventListener('load', function() {
	
	// buttons and event listeners
	/*    If the login button, logout button or profile view elements do not exist
	*    (such as in wp-admin and wp - login pages) default to a 'fake' element
	*/
	if ( jQuery( "#wp-admin-bar-logout" ).length ) {
		jQuery( "#wp-admin-bar-logout" ).remove();
	}
		
	if ( !jQuery( "#newLoginBtn" ).length ) {
		var loginBtn = document.createElement('div');
		loginBtn.setAttribute("id", "newLoginBtn");
	}else{
		var loginBtn    = document.getElementById('newLoginBtn');
	}
	
	if ( !jQuery( "#newLogoutBtn" ).length ) {
		var logoutBtn = document.createElement('div');
		logoutBtn.setAttribute("id", "newLogoutBtn");
	}else{
		var logoutBtn    = document.getElementById('newLogoutBtn');
	}
	
	if ( !jQuery( "#profile-view" ).length ) {
		var profileView = document.createElement('div');
		profileView.setAttribute("id", "profile-view");
	}else{
		var profileView    = document.getElementById('profile-view');
	}

		
	// This is specific to makeco, to turn the blue buttons on the conversion pages into pseudo logins
	jQuery(".trigger-login").click( function(event) {
		if(!jQuery(".page-template-page-conversion")) {
			localStorage.setItem('redirect_to','/myaccount');
		} else {
			localStorage.setItem('redirect_to','/mm-error/?code=100020');
		}
		webAuth.authorize();
		event.preventDefault();
	});
	
	//default profile view to hidden
	loginBtn.style.display    = 'none';
	profileView.style.display = 'none';

	var userProfile;
	
	var progressBar = jQuery(".progress .progress-bar");
	function updateProgressBar(percent) {
		if ( jQuery( '#authenticated-redirect' ).length ) {
			progressBar.attr("aria-valuenow", percent).css("width", percent).text(percent);
		}
	}
	
	var webAuth = new auth0.WebAuth({
		domain: AUTH0_DOMAIN,
		clientID: AUTH0_CLIENT_ID,
		redirectUri: AUTH0_CALLBACK_URL,
		audience: 'https://' + AUTH0_DOMAIN + '/userinfo',
		responseType: 'token id_token',
		scope: 'openid email profile user_metadata', //scope of data pulled by auth0
		leeway: 60
	});

	loginBtn.addEventListener('click', function(e) {
		e.preventDefault();
		if(location.href.indexOf('authenticate-redirect') >= 0){
			localStorage.setItem('redirect_to', templateUrl);
		}else{
			localStorage.setItem('redirect_to',location.href);
		}
		webAuth.authorize(); //login to auth0
	});

	logoutBtn.addEventListener('click', function(e) {
		e.preventDefault();

		// Remove tokens and expiry time from localStorage
		localStorage.removeItem('access_token');
		localStorage.removeItem('id_token');
		localStorage.removeItem('expires_at');

		//hide logged in button and logout of wp and auth0
		displayButtons();
	});
	
	function loginRedirect() {
      if ( jQuery( '#authenticated-redirect' ).length ) { //are we on the authentication page?
          if( localStorage.getItem( 'redirect_to' ) ){
				jQuery( '.redirect-message' ).text( "You will be redirected to the page you were trying to access shortly." );
            var redirect_url = localStorage.getItem( 'redirect_to' ); //retrieve redirect URL
            localStorage.removeItem( 'redirect_to' ); //unset after retrieved
            location.href = redirect_url;
          }else{ 
            // this is what's occurring sometimes when the page redirects to the homepage instead of to the url
            location.href = templateUrl;
          }
      } 
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
		} else {
			localStorage.removeItem('access_token');
			localStorage.removeItem('id_token');
			localStorage.removeItem('expires_at');
		}
	}

	function isAuthenticated() {
		// Check whether the current time is past the access token's expiry time
		if(localStorage.getItem('expires_at')){
			var expiresAt = JSON.parse(localStorage.getItem('expires_at'));
			return new Date().getTime() < expiresAt;
		}else{
			return false;
		}
	}

  function displayButtons() {
    if (isAuthenticated()) {
      loginBtn.style.display = 'none';
      //get user profile from auth0
      profileView.style.display = 'flex';
		updateProgressBar("25%");
      getProfile();

      //login to wordpress if not already
      if ( !ajax_object.wp_user_email ) { // is the user logged in?
         //wait .5 second for auth0 data to be returned from getProfile
         setTimeout(function(){ WPlogin(); }, 0500); //login to wordpress
      } else {
         loginRedirect();
      }
    } else {
      loginBtn.style.display = 'flex';
      profileView.style.display = 'none';
      if ( ajax_object.wp_user_email ) { // log out entirely if logged in to wp while not authenticated
          WPlogout();
      } else {
          jQuery(".redirect-message").html("<a href='javascript:location.reload();'>Try your login again</a>");

      }
    }
  }

  function getProfile() {
    var accessToken = localStorage.getItem('access_token');
    if (!accessToken) {
      console.log('Access token must exist to fetch profile');
		errorMsg('Login without Access Token');
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
			document.querySelector('.profile-email').innerHTML = userProfile.email; 
			document.querySelector('.profile-info .profile-name').innerHTML = userProfile['http://makershare.com/first_name'] + " " + userProfile['http://makershare.com/last_name'];
      }
		updateProgressBar("35%");
    });
	  
  }

  function WPlogin(){
    if (typeof userProfile !== 'undefined') {
		updateProgressBar("50%");
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
		 
		jQuery.ajax({
			type: 'POST',
			url: ajax_object.ajax_url,
			data: data,
			timeout: 10000,
			success: function(data){
				updateProgressBar("70%");
				loginRedirect();
				/* Nobody likes this, but page content is loaded before wp login otherwise */
				if(jQuery(".logged-in").length == 0 && jQuery( '#authenticated-redirect' ).length == 0 ) {
					if(window.location.href.indexOf("mm-error") > -1 ) {
				   	location.reload();
					}else {
						jQuery("#page-content").load(window.location.href + " #page-content", function(){
							owlCarouselInit();
						});
					}
		      }
			},
		}).fail(function(xhr, status, error) {
			if(status === 'timeout') {
				 alert( "Your login has timed out. Please try the login again." );
				 errorMsg(userProfile.email + " ran over the timeout limit of 10 seconds. Error was: " + JSON.stringify(error));
				 location.href = templateUrl;
			} else {
				 alert( "I'm sorry. We had an issue logging you into our system. Please try the login again." );
		       errorMsg(userProfile.email + " had an issue logging in at the WP Login phase. That error is: " + JSON.stringify(error));
			}
		});

    }else{
      if ( jQuery( '#authenticated-redirect' ).length ) {
          alert("We're having trouble logging you in and ran out of time. Refresh the page and we'll try harder.");
		    jQuery(".redirect-message").html("<a class='reload' href='javascript:location.reload();'>Reload page</a>");
			 errorMsg("Login failed for undefined user. Timeout.");
      }
    }
  }

  function WPlogout(wp_only){
    //logout of wordpress, in most cases this includes logging out of auth0. In cases where there is already a different wp user, just log them out and log back in with the new user
    var data = {
      'action': 'mm_wplogout'
    };
    if ( jQuery( '#wpadminbar' ).length ) {
	  jQuery( 'body' ).removeClass( 'adminBar' ).removeClass( 'logged-in' );
        jQuery( '#wpadminbar' ).remove();
        jQuery( '#mm-preview-settings-bar' ).remove();
    }

    jQuery.post(ajax_object.ajax_url, data, function(response) {
	   if(wp_only != "wp_only"){
      	window.location.href = 'https://makermedia.auth0.com/v2/logout?returnTo='+templateUrl+ '&client_id='+AUTH0_CLIENT_ID;
		}else{ // get rid of this by checking for wp_user email ratther than logged it above
			WPlogin();
		}
    });
  }

  //check if logged in another make property
  webAuth.checkSession({},
    function(err, result) {
      if (err) {
        // Remove tokens and expiry time from localStorage
        localStorage.removeItem('access_token');
        localStorage.removeItem('id_token');
        localStorage.removeItem('expires_at');
        if(err.error!=='login_required'){
			 errorMsg(userProfile.email + " had an issue logging in at the checkSession phase. That error was: " + JSON.stringify(err));
		  }
      } else {
         setSession(result);
      }
      displayButtons();  
    }
  );
	
  // this is for logging errors to the php error logs
  function errorMsg(message) {
	   var data = {
       'action'       : 'make_error_log',
       'make_error'   : message
     };
     jQuery.post(ajax_object.ajax_url, data, function(response) {});
  }

});