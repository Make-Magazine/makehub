window.addEventListener('load', function () {
    //set variable defaults
    var userProfile;
    var url = new URL(location.href).hostname;
    var auth0loggedin = false;

    //used in makehub API call
    var makecoRoot = "https://make.co";
    if( url.indexOf('makezine')!== -1 || url.indexOf('mzinedev')!== -1 || url.indexOf('makercamp.com')!== -1 ) {
		    wpLoginRequired = false;
    }else if(url.indexOf('test')!== -1 || url.indexOf('local')!== -1 ) {
		    makecoRoot = "https://www.makehub.local"
    }else if(url.indexOf('dev.')!== -1 || url.indexOf('devmakehub')!== -1 || url.indexOf('mzinedev')!== -1 || url.indexOf('mfairedev')!== -1) {
		    makecoRoot = "https://devmakehub.make.co"
    }else if(url.indexOf('stage.')!== -1 || url.indexOf('stagemakehub')!== -1 || url.indexOf('mzinestage')!== -1 || url.indexOf('mfairestage')!== -1) {
		    makecoRoot = "https://stagemakehub.wpengine.com"
    }

    //we only login to wordpress on the makehub and the makerfaire website
    var wploggedin = false;        //is the user logged into WP?
    var wpLoginRequired = false;   //is a WP login required?
    var makehubSite = false;       //is this a makehub site?

    if(url.indexOf('make.co') !== -1 || url.indexOf('makehub') !== -1) {
      makehubSite = true;
      wpLoginRequired = true;
    } else if(url.indexOf('mfaire') !== -1  || url.indexOf('makerfaire') !== -1 ) {
      wpLoginRequired = true;
    }

    //if you are on a makehub site and logged in, you do not need to call auth0
    if(makehubSite && (document.body.classList.contains( 'logged-in' ) || getUrlParam('login') == "true")){
      wploggedin = true;
      //let's set up the dropdowns
      displayButtons();
    }else{
      console.log('auth0 login');
      //If the buddypanel exists, hide it while we check for logged in
      //alicia, shouldn't this just be done before the if wpLoginRequired check?
      if(jQuery(".buddypanel").length){
        jQuery(".buddypanel .side-panel-inner").prepend("<img src='https://make.co/wp-content/universal-assets/v1/images/makey-spinner.gif' height='50px' width='50px' style='margin:auto;' />");
			  jQuery(".buddypanel .side-panel-inner #buddypanel-menu").css("display", "none");
      }

      //ok let's check auth0 instead
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
						console.log(err);
						if (err.error !== 'login_required') {
							errorMsg("User had an issue logging in at the checkSession phase. That error was: " + JSON.stringify(err));
						}

						// If this IS makerfaire or makehub, and the user is logged into WP, we need to log them out as they are no longer logged into Auth0
						if(wpLoginRequired && jQuery("body").is(".logged-in")) {
							WPlogout();
						}

						clearLocalStorage();
					} else {
            //logged into Auth0
            auth0loggedin = true;
						userProfile = result.idTokenPayload;

						setSession(result);

            //if this is a site that requires WP login, but they aren't logged into wp, log them in
      			if (wpLoginRequired && wploggedin == false && !jQuery("body").is(".logged-in")) {
      				// loading spinner to show user we're pulling up their data. Once styles are completely universal, move these inline styles out of there
      				jQuery('.universal-footer').before('<img src="https://make.co/wp-content/universal-assets/v1/images/makey-spinner.gif" class="universal-loading-spinner" style="position:absolute;top:50%;left:50%;margin-top:-75px;margin-left:-75px;" />');
      				WPlogin();
      			}
					}
          displayButtons();
				}
			); //end webAuth.checkSession
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
    //are we logged into auth0 or wordpress?
		if (auth0loggedin || wploggedin) {
      console.log('before getProfile');
      //hide the logout button
			jQuery("#profile-view, #LogoutBtn").css('display', 'flex');
			var user = getProfile();
      console.log('after getProfile');
      // Now that we have the avatar and the drop down, let's call rimark and see what info they have
      jQuery.ajax({
        type: 'GET',
        url: makecoRoot+"/wp-json/MakeHub/v1/userNav?email=" + user.user_email,
        timeout: 100000,
        success: function (data) {
          if(data.makeCoins && data.makeCoins != "") {
            jQuery( "#make-coins" ).html( data.makeCoins );
          }
        }
      });
		} else {
      //show the log in button
			jQuery("#LoginBtn").css("display", "block");
			jQuery("#profile-view, #LogoutBtn").css('display', 'none');
			jQuery(".login-section").css("display", "block");
			showBuddypanel();
			hideSpinner();
		}

	}

	// css will hide buddyboss side panel until page loads and the content of the buddypanel menu refreshes
	function showBuddypanel() {
		//does this site have a bb left hand panel?
		if(document.body.classList.contains( 'bb-buddypanel' )){
      //are they logged into wp or auth0
			if(wploggedin == false) {
				jQuery("body").addClass("buddypanel-open");
			} else {
				jQuery("body").addClass("buddypanel-closed");
			}
			//simulate a window resize when buddypanel opens so social wall and other elements that depend on javascript for their positioning get re-adjusted
			window.dispatchEvent(new Event('resize'));
		}
	}

	function hideSpinner() {
		jQuery(".buddypanel .side-panel-inner img").remove();
		jQuery(".buddypanel .side-panel-inner #buddypanel-menu").css("display", "block");
	}

	function getProfile() {
    var user = {};
    //are they logged into WP or Auth0 and is this a makeco domain?
		if(wploggedin && makehubSite){
			//user is logged into wordpress at this point and is on a make.co site let's display wordpress data
      user = {user_avatar:(ajax_object.wp_user_avatar == undefined) ? '' : ajax_object.wp_user_avatar,
              user_email:(ajax_object.wp_user_email == undefined) ? '' : ajax_object.wp_user_email,
              user_name:(ajax_object.wp_user_nicename == undefined) ? '' : ajax_object.wp_user_nicename,
              user_memlevel:(ajax_object.wp_user_memlevel == undefined) ? '' : ajax_object.wp_user_memlevel,
             };

		}else if(auth0loggedin){ // if user is logged into auth0, we will call data from auth0
			//we already got the userprofile info from auth0 in the check session step
			var accessToken = localStorage.getItem('access_token');

			if (!accessToken) {
				console.log('Access token must exist to fetch profile');
				errorMsg('Login attempted without Access Token');
			}

      //user is logged into wordpress at this point and is on a make.co site let's display wordpress data
      user = {user_avatar: (userProfile['http://makershare.com/picture'] == undefined) ? userProfile.picture : userProfile['http://makershare.com/picture'],
              user_email: (userProfile.email == undefined) ? '' : userProfile.email,
              user_name: (userProfile['http://makershare.com/first_name'] == undefined && userProfile['http://makershare.com/last_name'] == undefined) ? '' : userProfile['http://makershare.com/first_name'] + " " + userProfile['http://makershare.com/last_name'],
              user_memlevel: (userProfile['http://makershare.com/membership_level'] == undefined) ? '' : userProfile['http://makershare.com/membership_level'],
             };
		}else{
      //not loged into auth0 or wp, get out of here
      return;
    }

    //set the user drop down and avatar
    setUserDrop(user);

    //if this is a buddyboss theme site, show the buddypanel
    if( jQuery("body").is(".buddyboss-theme") ) {
      // css will hide buddyboss side panel until page loads and the content of the buddypanel menu refreshes
      hideSpinner();
      showBuddypanel();
    }
    return user;
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
				if (wploggedin == false) {
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

					// if the non-logged in buddypanel is showing for a logged in user, refresh it
					if(jQuery("#buddypanel-menu .bp-login-nav").length) {
						jQuery('#buddypanel-menu').load(document.URL + " #buddypanel-menu > *", function() {
							hideSpinner();
						});
						jQuery("body").addClass("buddypanel-closed");
					} else if(jQuery("#buddypanel-menu .bp-logout-nav").length) {
						hideSpinner();
					}

					jQuery("body").addClass("logged-in");
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

  //this function is used to set the user avatar and drop down sections in the universal header
  function setUserDrop(user){
    //set user avatar
    if(user.user_avatar != '') {
      document.querySelector('.dropdown-toggle img').src =  user.user_avatar;
      document.querySelector('.profile-info img').src = user.user_avatar;
    }

    //set email and profile name
    document.querySelector('.profile-email').innerHTML = user.user_email;
    document.querySelector('.profile-info .profile-name').innerHTML = user.user_name;

    //set membership level
    //TBD - rename the returned profile path to not have makershare.com as part of it
    if (user.user_memlevel != '' ) {
      switch(user.user_memlevel) {
        case "premium":
          document.querySelector('.avatar-banner').src = "https://make.co/wp-content/universal-assets/v1/images/premium-banner.png";
          document.querySelector('.avatar-banner').setAttribute('alt', "Premium Member");
          break;
        case "upgrade":
          document.querySelector('.avatar-banner').src = "https://make.co/wp-content/universal-assets/v1/images/upgrade-banner.png";
          document.querySelector('.avatar-banner').setAttribute('alt', "Upgrade Membership");
          break;
        default:
          break;
      }
    }
    document.querySelector('#LoginBtn').style.display = "none";
    document.querySelector('.dropdown-toggle img').style.display = "block";
		jQuery(".login-section").css("display", "block");
  }

  //end functions
});  // end event listener
