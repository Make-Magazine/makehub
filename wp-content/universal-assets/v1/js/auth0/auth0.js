// Before we get started, let's check if there is an auth0Hash either in the url or in localstorage
var auth0Hash = window.location.hash ? window.location.hash : localStorage.getItem('auth0_hash');

if(window.location.hash.indexOf("access_token") > -1) {
	console.log("we are just setting the auth0hash on a first time login");
	localStorage.setItem('auth0_hash', auth0Hash);
	localStorage.setItem('first_login', 'true');
}

jQuery(document).ready(function() {
    //set variable defaults
    var userProfile;
    var url = new URL(location.href).hostname;
    var auth0loggedin = false;

    //we only login to wordpress on the makehub and the makerfaire website
    var wploggedin = false; //is the user logged into WP?
    var wpLoginRequired = false; //is a WP login required?
    var makehubSite = false; //is this a makehub site?

    if (url.indexOf('make.co') !== -1 || url.indexOf('makehub') !== -1) {
		console.log("we on makehub");
        makehubSite = true;
        wpLoginRequired = true;
    } else if (url.indexOf('mfaire') !== -1 || url.indexOf('makerfaire') !== -1) {
		console.log("we on makerfaire");
        wpLoginRequired = true;
    }

    //if you are on a makehub site and logged in, you do not need to call auth0
    // we use ajax fields to set the user drop down in this case
    if (makehubSite && (document.body.classList.contains('logged-in') || getUrlParam('login') == "true")) {
		console.log("we logged in already");
        wploggedin = true;
        //let's set up the dropdowns
        displayButtons();
    } else {
		console.log("we're not logged in! what are we going to do?");
        //otherwise we need to call auth0 for login and to show the user drop down

        //If the buddypanel exists, hide it while we check for logged in
        //TBD, shouldn't this just be done before the if wpLoginRequired check?
        if (jQuery(".buddypanel").length) {
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
		console.log("check out this webAuth");
		console.log(webAuth);

		// always need to make sure we clear the localStorage when the login button is clicked, regardless of case
		jQuery("#LogoutBtn").on("click", function(event) {
			clearLocalStorage();
		});

        // for makezine or other non wplogin sites, we still want the login button to trigger an auth0 login rather than
        if (wpLoginRequired == false) {
			console.log("we on makezine");
            jQuery("#LoginBtn").on("click", function(event) {
                event.preventDefault();
				setCookie("mz_redirect_url", window.location.href, 1);
                webAuth.authorize({
                    clientID: AUTH0_CLIENT_ID,
                    redirect_uri: location.protocol + "//" + location.hostname,
                });
            });

            //set the logout to the default auth0 logout
            jQuery("#LogoutBtn").on("click", function(event) {
                event.preventDefault();
                webAuth.logout({
                    clientID: AUTH0_CLIENT_ID,
                    returnTo: location.protocol + "//" + location.hostname,
                });
            });
        }
		//check for if this is the first login
		if(localStorage.getItem('first_login')) {
			console.log("this is the first login");

			if(auth0Hash.includes("access_token")){
				console.log("we got an access_token");
				// this is the first time logging in
				webAuth.parseHash(({hash: auth0Hash}),function(err, data) {
				  if (err) {
				   	//user does not have a session you'll see something like 'login required'
					console.log('err', err);
				  }
				  if (data) {
					  console.log("data was returned and here it is: ");
					  console.log(data);
						//logged into Auth0
						auth0loggedin = true;
						userProfile = data.idTokenPayload;
						setSession(data);
						displayButtons();
						//if this is a site that requires WP login, but they aren't logged into wp, log them in
						if (wpLoginRequired && wploggedin == false && !jQuery("body").is(".logged-in")) {
							console.log("we're on a wp login site");
							// loading spinner to show user we're pulling up their data. Once styles are completely universal, move these inline styles out of there
							//TBD - this needs styling as this isn't seen where it's at
							jQuery('.universal-footer').before('<img src="https://make.co/wp-content/universal-assets/v1/images/makey-spinner.gif" class="universal-loading-spinner" style="position:absolute;top:50%;left:50%;margin-top:-75px;margin-left:-75px;" />');
							WPlogin();
						}
				  }
				  window.location.hash = '';
				});
			}else if(auth0Hash.includes("login_required")){
				console.log("we don't got no hash, gotta log out.");
				// If this IS makerfaire or makehub, and the user is logged into WP, we need to log them out as they are no longer logged into Auth0
				//If you are makehub and you are logged in, you will never hit this code
				if (wpLoginRequired && jQuery("body").is(".logged-in")) {
					WPlogout();
				}
				clearLocalStorage();
			}
			console.log("we're done with the first login, remove the first_login flag from local storage");
			localStorage.removeItem('first_login');
		} else {
			console.log("this ain't the first login anymore, let's check if we've expired");
			//check if expires at is set and not expired and accesstoken is set in local storage
			//if yes then run the webAuth.client.userInfo() call
			var currentDate = new Date();
			if(localStorage.getItem('expires_at') && localStorage.getItem('expires_at') > currentDate.getTime()) {
				console.log("we sure haven't expired!");
				webAuth.client.userInfo(localStorage.getItem('access_token'), function(err, user) {
					// if we're getting an error at this stage and see the blank default makey avatar, let's complete logging the user out
					if(err && jQuery("#profile-view img.avatar").attr('src') == "https://make.co/wp-content/universal-assets/v1/images/default-makey.png") {
						console.log("uh oh, we got an error and so, were stopping the logout btn click to investigate it");
						console.log(err);
						alert("I'm just going to give you a second to check that error, as I suspect this is where we are losing our auth");
						//jQuery("#LogoutBtn").click();
					}
					console.log("we got user data");
					console.log(user);
					// other wise, do the thing!
					userProfile = user;
					auth0loggedin = true;
					displayButtons();
				});
			} else {
				console.log("we have expired");
				// log if we are still getting any user info after the expiration
				if(localStorage.getItem('expires_at') && localStorage.getItem('expires_at') <= currentDate.getTime()) {
					webAuth.client.userInfo(localStorage.getItem('access_token'), function(err, user) {
						console.log("can we get any user data anyways?");
						console.log(user);
					});
				}
		        //check if logged in another place
				console.log("check if logged in anywhere else");
		        webAuth.checkSession({},
		            function(err, result) {
		                if (err) {
							console.log("I guess not, check this error");
							console.log(err);
		                    //not logged into auth0 - Commenting these out since they go off even if a user is just visiting a site before logging in
		                    if (err.error !== 'login_required') {
		                        //errorMsg("User had an issue logging in at the checkSession phase. That error was: " + JSON.stringify(err));
		                    }

		                    // This should take care of SSO
		                    // If this IS makerfaire or makehub, and the user is logged into WP, we need to log them out as they are no longer logged into Auth0
		                    //If you are makehub and you are logged in, you will never hit this code
		                    if (wpLoginRequired && jQuery("body").is(".logged-in")) {
		                        WPlogout();
		                    }
		                    clearLocalStorage();
		                } else {
							console.log("oh sweet, we are logged in somewhere else");
		                    //logged into Auth0
		                    auth0loggedin = true;
		                    userProfile = result.idTokenPayload;
							console.log("and here's the user data:");
							console.log(userProfile);
		                    setSession(result);

		                    //if this is a site that requires WP login, but they aren't logged into wp, log them in
		                    if (wpLoginRequired && wploggedin == false && !jQuery("body").is(".logged-in")) {
								console.log("login to wordress");
		                        // loading spinner to show user we're pulling up their data. Once styles are completely universal, move these inline styles out of there
		                        //TBD - this needs styling as this isn't seen where it's at
		                        jQuery('.universal-footer').before('<img src="https://make.co/wp-content/universal-assets/v1/images/makey-spinner.gif" class="universal-loading-spinner" style="position:absolute;top:50%;left:50%;margin-top:-75px;margin-left:-75px;" />');
		                        WPlogin();
		                    }
		                }
		                displayButtons();
		            }
		        ); //end webAuth.checkSession
			}
		}
    }

    //place functions here so they can access the variables inside the event addEventListener
    function clearLocalStorage() {
		console.log("we are clearing local storage");
		localStorage.removeItem('auth0_hash');
        localStorage.removeItem('access_token');
        localStorage.removeItem('id_token');
        localStorage.removeItem('expires_at');
    }

    function setSession(authResult) {  // delete the hash localStorage and set the new one
		console.log("setting session");
        if (authResult) {
			console.log("auth result:");
			console.log(authResult);
            // Set the time that the access token will expire at
            var expiresAt = JSON.stringify(
                authResult.expiresIn * 36000 + new Date().getTime()
            );
            localStorage.setItem('access_token', authResult.accessToken);
            localStorage.setItem('id_token', authResult.idToken);
            localStorage.setItem('expires_at', expiresAt);
        } else {
			console.log("no authresult");
            clearLocalStorage();
        }
    }

    function displayButtons() {
        //are we logged into auth0 or wordpress?
        if (auth0loggedin || wploggedin) {
            //hide the logout button
            jQuery("#profile-view, #LogoutBtn").css('display', 'flex');
			jQuery("#mzLoginBtn").css("display", "none");
			jQuery(".login-section #dropdownMenuLink .avatar").css("display", "block");
            getProfile();
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
        if (document.body.classList.contains('bb-buddypanel')) {
            //are they logged into wp or auth0
            if (wploggedin == false) {
                jQuery("body").addClass("buddypanel-open");
            } else {
                jQuery("body").addClass("buddypanel-closed");
            }
			if (!jQuery("body").hasClass("bb-page-loaded")) {
				jQuery("body").addClass("bb-page-loaded");
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
		console.log("getting user profile");
        var user = {};
        //are they logged into WP or Auth0 and is this a makeco domain?
        if (wploggedin && makehubSite) {
			console.log("getting user profile from makehub");
            //user is logged into wordpress at this point and is on a make.co site let's display wordpress data
            user = {
                user_avatar: (ajax_object.wp_user_avatar == undefined) ? '' : ajax_object.wp_user_avatar,
                user_email: (ajax_object.wp_user_email == undefined) ? '' : ajax_object.wp_user_email,
                user_name: (ajax_object.wp_user_nicename == undefined) ? '' : ajax_object.wp_user_nicename,
                user_memlevel: (ajax_object.wp_user_memlevel == undefined) ? '' : ajax_object.wp_user_memlevel,
            };

        } else if (auth0loggedin) { // if user is logged into auth0, we will call data from auth0
			console.log("getting user profile from auth0");
            //we already got the userprofile info from auth0 in the check session step
			var accessToken = localStorage.getItem('access_token');

            if (!accessToken) {
                console.log('Access token must exist to fetch profile');
                errorMsg('Login attempted without Access Token');
            }

            user = {
                user_avatar: (userProfile['http://makershare.com/picture'] == undefined) ? userProfile.picture : userProfile['http://makershare.com/picture'],
                user_email: (userProfile.email == undefined) ? '' : userProfile.email,
                user_name: (userProfile['http://makershare.com/first_name'] == undefined && userProfile['http://makershare.com/last_name'] == undefined) ? '' : userProfile['http://makershare.com/first_name'] + " " + userProfile['http://makershare.com/last_name'],
                user_memlevel: (userProfile['http://makershare.com/membership_level'] == undefined) ? '' : userProfile['http://makershare.com/membership_level'],
            };
        } else {
			console.log("not logged into auth0 or wp, get out of here");
            //not logged into auth0 or wp, get out of here
            return;
        }

        //set the user drop down and avatar
        setUserDrop(user);

		jQuery(".profile-menu").addClass("logged-in");
		jQuery(".mobile-subscribe-btn").css("display", "none"); // logged in, we no longer show the mobile subscribe button, as it will be replaced with the upgrade or join buttons below

        //Set upgrade or join now buttons
        if (user.user_memlevel == "upgrade") {
            jQuery(".dropdown-menu .profile-info").after("<a href='https://make.co/join/' class='btn membership-btn'>Upgrade Membership</a>");
        } else if (user.user_memlevel == "none") {
            jQuery(".dropdown-menu .profile-info").after("<a href='https://make.co/join/' class='btn membership-btn'>Join Now!</a>");
        }

        // Now that we have the avatar and the drop down, let's call rimark and see what info they have
        callRimark(user);

        //if this is a buddyboss theme site, show the buddypanel
        if (jQuery("body").is(".buddyboss-theme")) {
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
                success: function(data) {},
            }).done(function() {

                // the very first time a user visits and gets logged in to wordpress, we need to refresh some things
                if (wploggedin == false) {
                    // reload subnavs as necessary
                    jQuery('#menu-secondary_universal_menu').load(document.URL + " #menu-secondary_universal_menu > *");
                    // reload the digital libary if necessary
                    if (jQuery('.main-content').length && jQuery('.join-box').length) {
                        window.location.replace("/digital-library/");
                    }
                    if (jQuery('.main-content').length && !jQuery('.blog.tribe-theme-child-make-campus').length && !jQuery('.makerspaces').length && !jQuery('.post-type-archive-tribe_events ').length) {
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
                    if (jQuery("#buddypanel-menu .bp-login-nav").length) {
                        jQuery('#buddypanel-menu').load(document.URL + " #buddypanel-menu > *", function() {
                            hideSpinner();
                        });
                        jQuery("body").addClass("buddypanel-closed");
                    } else if (jQuery("#buddypanel-menu .bp-logout-nav").length) {
                        hideSpinner();
                    }

                    jQuery("body").addClass("logged-in");
                    jQuery('.universal-loading-spinner').remove();
                }
                // css will hide buddyboss side panel until page loads and the content of the buddypanel menu refreshes
                showBuddypanel();
            }).fail(function(xhr, status, error) {
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

    // on logout delete the hash localStorage
    function WPlogout() {
        if (jQuery('#wpadminbar').length) {
            jQuery('body').removeClass('adminBar').removeClass('logged-in');
            jQuery('#wpadminbar').remove();
            jQuery('#mm-preview-settings-bar').remove();
        }
        var data = {
            'action': 'mm_wplogout'
        };
        jQuery.post(ajax_object.ajax_url, data, function(response) {
            window.location.href = 'https://login.make.co/v2/logout?returnTo=' + templateUrl + '&client_id=' + AUTH0_CLIENT_ID;
        }).done(function() {
			clearLocalStorage();
            location.href = location.href;
        });
        // css will hide buddyboss side panel until page loads
        showBuddypanel();
    }

    //this function is used to set the user avatar and drop down sections in the universal header
    function setUserDrop(user) {
        //set user avatar
        if (user.user_avatar != '') {
            jQuery('#profile-view #dropdownMenuLink img.avatar').attr("src", user.user_avatar);
            jQuery('#profile-view .profile-info img.avatar').attr("src", user.user_avatar);
        }

        //set email and profile name
        document.querySelector('.profile-email').innerHTML = user.user_email;
        document.querySelector('.profile-info .profile-name').innerHTML = user.user_name;

        //set membership level
        if (user.user_memlevel != '') {
            switch (user.user_memlevel) {
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
        } else {
            //no membership level set yet in auth0, remove the banner as we don't know if they have a membership level or not
            jQuery('.avatar-banner').remove();
        }
		setTimeout(function() {
			document.querySelector(".avatar-banner").style.display = "block";
		}, 100);
        document.querySelector('#LoginBtn').style.display = "none";
        document.querySelector('.dropdown-toggle img').style.display = "block";
        jQuery(".login-section").css("display", "block");
    }

    //call rimark and build coin section
    function callRimark(user) {
        //POST auth request to get jwt
        var url = "https://devapi.rimark.io/api/auth/local/";
        //TBD find a way to encrypt this
        var body = "{\"identifier\": \"webmaster@make.co\",\"password\":\"AHxv2sj3hK*rWpF\"}";
        jQuery.ajax({
            type: 'POST',
            url: "https://devapi.rimark.io/api/auth/local/",
            data: body,
            contentType: "application/json; charset=utf-8",
            success: function(data) {
                var jwt = data.jwt;
                //get specific user info
                jQuery.ajax({
                    type: 'GET',
                    url: 'https://devapi.rimark.io/api/makes?filters[user_email][$eq]=' + user.user_email,
                    headers: {
                        Authorization: 'Bearer ' + jwt
                    },
                    contentType: "application/json; charset=utf-8",
                    success: function(data) {
                        //data[0]->attributes->total_supply_owned
                        if (jQuery.isEmptyObject(data.data) || data.data[0].attributes.total_supply_owned == "") {
                            coins = '$MAKE:<br/><a target="_blank" href="https://make.co/make-faq/">Learn More</a>';
                        } else {
                            coins = '$MAKE:<br/><a target="_blank" href="https://beta.rimark.io/?target=219f76ovo2v0fi2nn9es0x9wf">' + data.data[0].attributes.total_supply_owned + '</a>';
                        }
                        jQuery("#make-coin").html(coins);
                    }
                });
            }
        });
    }

	// this logs
	function errorMsg(message) {
        var data = {
            'action': 'make_error_log',
            'make_error': message
        };
        jQuery.post(ajax_object.ajax_url, data, function(response) {});
    }

    //end functions
}); // end event listener
