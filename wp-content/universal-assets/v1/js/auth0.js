window.addEventListener('load', function () {
    //set variable defaults
    var userProfile;
    var loggedin = false;
    var wpLoginRequired = true;

    //do not show the login button on makezine or makercamp
    var url = new URL(location.href).hostname;
    if(url.indexOf('makezine')!== -1 || url.indexOf('makercamp')!== -1 ) {
      wpLoginRequired = false;
      jQuery(".search-separator").hide();
    }

	if (wpLoginRequired == true) {
    //check if logged into Wordpress
    if(document.body.classList.contains( 'logged-in' )){
		    loggedin = true;
    }
		var webAuth = new auth0.WebAuth({
			domain: AUTH0_CUSTOM_DOMAIN,
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
				function (err, result) {
					if (err) {
						if (err.error !== 'login_required') {
							errorMsg(userProfile.email + " had an issue logging in at the checkSession phase. That error was: " + JSON.stringify(err));
						}
						clearLocalStorage();
					} else {
						setSession(result);
					}
					displayButtons();
				}
		);
	}

  //place functions here so they can access the variables inside the event addEventListener
  function clearLocalStorage() {
    localStorage.removeItem('access_token');
    localStorage.removeItem('id_token');
    localStorage.removeItem('expires_at');
  }

  function setSession(authResult) {
    if (authResult) {
      // Set the time that the access token will expire at
      var expiresAt = JSON.stringify(
        authResult.expiresIn * 1000 + new Date().getTime()
      );
      localStorage.setItem('access_token', authResult.accessToken);
      localStorage.setItem('id_token', authResult.idToken);
      localStorage.setItem('expires_at', expiresAt);
    } else {
      clearLocalStorage();
    }
  }

  function displayButtons() {
    if (localStorage.getItem('expires_at')) {
      jQuery("#profile-view, #LogoutBtn").css('display', 'flex');
      getProfile();
    } else {
      jQuery("#LoginBtn").css("display", "block");
      jQuery("#profile-view, #LogoutBtn").css('display', 'none');
      jQuery(".login-section").css("display", "block");
      WPlogout();

    }
  }

  // css will hide buddyboss side panel until page loads and the content of the buddypanel menu refreshes
  function showBuddypanel() {
    jQuery("#buddypanel-menu").load(document.URL + " #buddypanel-menu > *", function(){
      if(loggedin == false) {
        jQuery("body").addClass("buddypanel-open");
      } else {
        jQuery("body").addClass("buddypanel-closed");
      }
      //simulate a window resize when buddypanel opens so social wall and other elements that depend on javascript for their positioning get readjusted
      window.dispatchEvent(new Event('resize'));
    });
  }

  function getProfile() {
    var accessToken = localStorage.getItem('access_token');

    if (!accessToken) {
      console.log('Access token must exist to fetch profile');
      errorMsg('Login attempted without Access Token');
    }

    webAuth.client.userInfo(accessToken, function (err, profile) {
      if (profile) {
        userProfile = profile;
        // make sure that there isn't a wordpress acount with a different user logged in
        if (ajax_object.wp_user_email && ajax_object.wp_user_email != userProfile.email) {
          WPlogout();
        }
        // display the avatar
        document.querySelector('.dropdown-toggle img').src = userProfile.picture;
        document.querySelector('.profile-info img').src = userProfile.picture;
        document.querySelector('.dropdown-toggle img').style.display = "block";
        document.querySelector('#LoginBtn').style.display = "none";
        document.querySelector('.profile-email').innerHTML = userProfile.email;
        // do we need http://makershare.com/last_name / first_name anymore
        if (userProfile['http://makershare.com/first_name'] != undefined && userProfile['http://makershare.com/last_name'] != undefined) {
          document.querySelector('.profile-info .profile-name').innerHTML = userProfile['http://makershare.com/first_name'] + " " + userProfile['http://makershare.com/last_name'];
        }
        if (wpLoginRequired && loggedin == false && !jQuery("body").is(".logged-in")) {
          // loading spinner to show user we're pulling up their data. Once styles are completely universal, move these inline styles out of there
          jQuery('.universal-footer').append('<img src="https://make.co/wp-content/universal-assets/v1/images/makey-spinner.gif" class="universal-loading-spinner" style="position:absolute;top:50%;left:50%;margin-top:-75px;margin-left:-75px;" />');
          WPlogin();
        } else if( jQuery("body").is(".buddyboss-theme") ) {
          // css will hide buddyboss side panel until page loads and the content of the buddypanel menu refreshes
          showBuddypanel();
        }
      }
      if (err) {
        errorMsg("There was an issue logging in at the getProfile phase. That error was: " + JSON.stringify(err));
      }
      jQuery(".login-section").css("display", "block");
    });
  }

  function WPlogin() {
    if (typeof userProfile !== 'undefined') {
      var user_id = userProfile.sub;
      var access_token = localStorage.getItem('access_token');
      var id_token = localStorage.getItem('id_token');

      //login to wordpress
      var data = {
        'action': 'mm_wplogin',
        'auth0_userProfile': userProfile,
        'auth0_access_token': access_token,
        'auth0_id_token': id_token
      };
      jQuery.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: data,
        timeout: 10000,
        success: function (data) {
        },
      }).done(function () {
        // the very first time a user visits and gets logged in to wordpress, we need to refresh some things
        if (loggedin == false) {
          // reload subnavs as necessary
          jQuery('#menu-secondary_universal_menu').load(document.URL + " #menu-secondary_universal_menu > *");
          // reload the digital libary if necessary
          if (jQuery('.main-content').length && jQuery('.join-box').length) {
            window.location.replace("/digital-library/");
          }
          if (jQuery('.main-content').length && !jQuery('.blog.tribe-theme-child-make-campus').length && !jQuery('.page-template-page-makerspaces-map-php').length && !jQuery('.post-type-archive-tribe_events ').length) {
            jQuery('.main-content').load(document.URL + " .main-content > *");
          }
          // this is for mf. maybe we could make mf use .main-content as it's default page wrapper in the future
          if (jQuery('.page-content').length) {
            jQuery('.page-content').load(document.URL + " .page-content > *");
          }
          // for anything else that has content that will changed if logged in
          if (jQuery('.logged-in-refresh').length) {
            jQuery('.logged-in-refresh').load(document.URL + " .logged-in-refresh > *");
          }
          jQuery('.universal-loading-spinner').remove();
        }
        // css will hide buddyboss side panel until page loads and the content of the buddypanel menu refreshes
        showBuddypanel();
      }).fail(function (xhr, status, error) {
        jQuery('.universal-loading-spinner').remove();
        if (status === 'timeout') {
          alert("Your login has timed out. Please try the login again.");
          errorMsg(userProfile.email + " ran over the timeout limit of 10 seconds. Error was: " + JSON.stringify(error));
          location.href = location.href; // reload in the hopes it was just a temp server blip
        } else {
          alert("I'm sorry. We had an issue logging you into our system. Error Code: 0599.\nPlease contact our support at community@make.co with this error code for assistance.");
          errorMsg(userProfile.email + " had an issue logging in at the WP Login phase. That error is: " + JSON.stringify(xhr));
        }
      });
      // this is if we're just logging in to begin with rather than visiting from another site
    } else {
      jQuery('.universal-loading-spinner').remove();
    }
  }

  function WPlogout(wp_only) {
    if (jQuery('#wpadminbar').length) {
      jQuery('body').removeClass('adminBar').removeClass('logged-in');
      jQuery('#wpadminbar').remove();
      jQuery('#mm-preview-settings-bar').remove();
    }
    var data = {'action': 'mm_wplogout'};
    jQuery.post(ajax_object.ajax_url, data, function (response) {
      window.location.href = 'https://login.make.co/v2/logout?returnTo=' + templateUrl + '&client_id=' + AUTH0_CLIENT_ID;
    }).done(function () {
      location.href = location.href;
    });
    // css will hide buddyboss side panel until page loads
    showBuddypanel();
  }

  function errorMsg(message) {
    var data = {
      'action': 'make_error_log',
      'make_error': message
    };
    jQuery.post(ajax_object.ajax_url, data, function (response) {});
  }

  //end functions
});  // end event listener
