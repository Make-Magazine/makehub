jQuery(document).ready(function() {
    wploggedin = false;        
    //if you are on a makehub site and logged in, you do not need to call auth0
    // we use ajax fields to set the user drop down in this case
    if (document.body.classList.contains('logged-in')) {
        wploggedin = true;        
    }
    displayButtons();   //let's set up the dropdowns

    function displayButtons() {
        //are we logged into wordpress?
        if (wploggedin) {
            //hide the logout button
            jQuery("#profile-view, #LogoutBtn").css('display', 'flex');
			//jQuery("#mzLoginBtn").css("display", "none");
			jQuery(".login-section #dropdownMenuLink .avatar").css("display", "block");
            getProfile();
        } else {
            //show the log in button
            jQuery("#LoginBtn").css("display", "block");
            jQuery("#profile-view, #LogoutBtn").css('display', 'none');
            jQuery(".login-section").css("display", "block");
        }

    }

    function getProfile() {
        var user = {};
        //are they logged into WP? this should just be mf and make.co domains
        if (wploggedin) {
            //user is logged into wordpress at this point, let's display wordpress data
            user = {
                user_avatar: (typeof ajax_object.wp_user_avatar == 'undefined') ? '' : ajax_object.wp_user_avatar,
                user_email: (typeof ajax_object.wp_user_email == 'undefined') ? '' : ajax_object.wp_user_email,
                user_name: (typeof ajax_object.wp_user_nicename == 'undefined') ? '' : ajax_object.wp_user_nicename,
                user_memlevel: (typeof ajax_object.wp_user_memlevel == 'undefined') ? '' : ajax_object.wp_user_memlevel,
            };
        } else {
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
        //callRimark(user);

        return user;
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
                    document.querySelector('.avatar-banner').src = "https://make.co/wp-content/universal-assets/v2/images/premium-banner.png";
                    document.querySelector('.avatar-banner').setAttribute('alt', "Premium Member");
                    break;
                case "upgrade":
                    document.querySelector('.avatar-banner').src = "https://make.co/wp-content/universal-assets/v2/images/upgrade-banner.png";
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
			var avatarBanner = document.querySelector('.avatar-banner') !== null;
			if(avatarBanner) {
				document.querySelector(".avatar-banner").style.display = "block";
			}
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
    //end functions   
}); // end event listener
