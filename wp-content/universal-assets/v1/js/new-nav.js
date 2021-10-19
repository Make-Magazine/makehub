jQuery(document).ready(function () {

	    // Change logo based on site
    var site = window.location.hostname
	var sitename = "Make: Community";
    switch (site) {
        case "makerfaire.local":
        case "makerfaire.test":
        case "makerfaire.com":
        case "makerfaire.staging.wpengine.com":
        case "dev.makerfaire.com":
        case "stage.makerfaire.com":
        case "mfairestage.wpengine.com":
        case "mfairedev.wpengine.com":
            jQuery("h2.site-title a").attr("href", "https://makerfaire.com");
            document.getElementById("navLogo").src = "/wp-content/themes/makerfaire/img/Maker_Faire_Logo.svg";
			sitename = "Maker Faire";
            break;
        case "makercamp.local":
        case "makercamp.test":
        case "makercamp.com":
        case "makercamp.staging.wpengine.com":
        case "dev.makercamp.com":
        case "stage.makercamp.com":
        case "mcampstage.wpengine.com":
        case "mcampdev.wpengine.com":
        case "makercamp.makehub.local":
        case "makercamp.make.co":
        case "makercamp.devmakehub.wpengine.com":
        case "makercamp.stagemakehub.wpengine.com":
            jQuery("h2.site-title a").attr("href", "https://makercamp.com");
            document.getElementById("navLogo").src = "https://makercamp.com/wp-content/themes/makercamp-theme/assets/img/makercamp-logo.png";
			sitename = "Maker Camp";
            break;
        case "makezine.test":
        case "makezine.local":
        case "makezine.staging.wpengine.com":
        case "makezine.com":
        case "stage.makezine.com":
        case "dev.makezine.com":
		case "mzinedev.wpengine.com":
		case "mzinestage.wpengine.com":
            jQuery("h2.site-title a").attr("href", "https://makezine.com");
            jQuery("#site-logo .nav-logo").css("height", "30.4px");
            jQuery("#site-logo .nav-logo").css("margin-top", "-5px");
			sitename = "Make: Magazine";
            break;
        case "makerspaces.makehub.test":
        case "makerspaces.makehub.local":
        case "makerspaces.devmakehub.wpengine.com":
        case "makerspaces.stagemakehub.wpengine.com":
        case "makerspaces.make.co":
			sitename = "Maker Spaces";
            jQuery("h2.site-title a").attr("href", "https://makerspaces.make.co");
            document.getElementById("navLogo").src = "/wp-content/universal-assets/v1/images/makerspaces-logo.jpg";
            break;
        case "learn.makehub.test":
        case "learn.makehub.local":
        case "learn.devmakehub.wpengine.com":
        case "learn.stagemakehub.wpengine.com":
        case "learn.make.co":
            jQuery("h2.site-title a").attr("href", "https://learn.make.co");
            document.getElementById("navLogo").src = "/wp-content/universal-assets/v1/images/make-learning-labs-logo.png";
			sitename = "Learning Labs";
            break;
        default:// the default is makehub/make.co
            if (window.location.href.indexOf("makercampus") > -1 || window.location.href.indexOf("maker-campus") > -1) {
				sitename = "Maker Campus";
                // except for makercampus which gets it's own logo and subnav items
                jQuery("h2.site-title a").attr("href", "https://make.co/maker-campus");
                jQuery("#site-logo .nav-logo").css("margin-top", "-8px");
                document.getElementById("navLogo").src = "/wp-content/universal-assets/v1/images/MakerCampus_Logo_Boxless.png";
                jQuery("#universal-subnav .community-subnav-item, #primary-navbar .community-subnav-item").css("display", "none");
                jQuery("#universal-subnav .campus-subnav-item, #primary-navbar .campus-subnav-item").css("display", "block");
            } else {
                document.getElementById("navLogo").src = "/wp-content/universal-assets/v1/images/make_co_logo.png";
            }
            break;
    }

    /*
     * Allow use of Array.from in implementations that don't natively support it
     function conNavArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }
     */

    // Buddyboss code

    function conNavArray(arr) {
        if (Array.isArray(arr)) {
            for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        } else {
            return [].slice.call(arr);
        }
    }

    var primaryWrap = document.getElementById('primary-navbar'),
            primaryNav = document.getElementById('primary-menu'),
            extendNav = document.getElementById('navbar-extend'),
            navCollapse = document.getElementById('navbar-collapse');

    function navListOrder() {
        var eChildren = extendNav.children;
        var numW = 0;

        [].concat(conNavArray(eChildren)).forEach(
                function (item) {
                    item.outHTML = '';
                    primaryNav.appendChild(item);
                }
        );

        var primaryWrapWidth = primaryWrap.offsetWidth,
                navCollapseWidth = navCollapse.offsetWidth + 30,
                primaryWrapCalc = primaryWrapWidth - navCollapseWidth,
                primaryNavWidth = primaryNav.offsetWidth,
                pChildren = primaryNav.children;

        [].concat(conNavArray(pChildren)).forEach(
                function (item) {
                    numW += item.offsetWidth + 5;

                    if (numW > primaryWrapCalc) {
                        item.outHTML = '';
                        extendNav.appendChild(item);
                    }

                }
        );

        if (extendNav.getElementsByTagName('li').length >= 1) {
            navCollapse.classList.add('hasItems');
        } else {
            navCollapse.classList.remove('hasItems');
        }

        primaryNav.classList.remove('bb-primary-overflow');

    }

    if (typeof (primaryNav) != 'undefined' && primaryNav != null) {

        navListOrder();

        setTimeout(
                function () {
                    navListOrder();
                },
                300
                );
        setTimeout(
                function () {
                    navListOrder();
                },
                900
                );

        jQuery('.bb-toggle-panel').on(
                'click',
                function (e) {
                    e.preventDefault();
                    navListOrder();

                    setTimeout(
                            function () {
                                navListOrder();
                            },
                            300
                            );

                    setTimeout(
                            function () {
                                navListOrder();
                            },
                            600
                            );
                }
        );
    }

    jQuery(document).on('click', '.more-button', function (e) {
        if(!document.getElementById('buddyboss-theme-main-js-js')) {
            e.preventDefault();
            jQuery(this).toggleClass('active').next().toggleClass('active');
        }
    });

    jQuery(document).on('click', '.hideshow .sub-menu a', function (e) {
        // e.preventDefault();
        jQuery('body').trigger('click');

        // add 'current' and 'selected' class
        var currentLI = $(this).parent();
        currentLI.parent('.sub-menu').find('li').removeClass('current selected');
        currentLI.addClass('current selected');
    });
    jQuery(document).on('click', '.header-aside div.menu-item-has-children > a', function (e) {
        e.preventDefault();
        var current = jQuery(this).closest('div.menu-item-has-children');
        current.siblings('.selected').removeClass('selected');
        current.toggleClass('selected');
    });

    jQuery('body').mouseup(function (e) {
        var container = jQuery('.header-aside div.menu-item-has-children *');
        if (!container.is(e.target)) {
            jQuery('.header-aside div.menu-item-has-children').removeClass('selected');
        }
    });

    // make some room for our fixed header
    jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "121px");
    if (jQuery(window).width() < 800) {
        jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "76px");
    }

    // Subnav
    jQuery(".site-header-custom").append(jQuery("#universal-subnav"));
    jQuery(".nav-level-2").css("display", "block");
    if (jQuery(window).width() < 800) {
        jQuery("#primary-navbar").prepend(jQuery("#menu-secondary_universal_menu"));
		jQuery("#primary-navbar").prepend(jQuery("#buddypanel-menu"));
		jQuery("#primary-navbar").prepend("<h3>" + sitename + "</h3>");
    }
    window.onresize = function() {
        if (jQuery(window).width() < 800 && (jQuery("#universal-subnav #menu-secondary_universal_menu").length || jQuery(".side-panel-menu-container #buddypanel-menu").length) ) {
            jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "76px");
            jQuery("#primary-navbar").prepend(jQuery("#menu-secondary_universal_menu"));
			if(jQuery(".side-panel-menu-container #buddypanel-menu").length) {
				jQuery("#primary-navbar").prepend(jQuery("#buddypanel-menu"));
			}
			jQuery("#primary-navbar").prepend("<h3>" + sitename + "</h3>");
        } else if (jQuery(window).width() > 800 && (jQuery("#primary-navbar #menu-secondary_universal_menu").length || jQuery("#primary-navbar #buddypanel-menu").length) ) {
            jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "121px");
            jQuery("#universal-subnav").append(jQuery("#menu-secondary_universal_menu"));
			if(jQuery("#primary-navbar #buddypanel-menu").length) {
				jQuery(".side-panel-menu-container").append(jQuery("#buddypanel-menu"));
			}
			jQuery("#primary-navbar h3").remove();
        }
        if (jQuery(window).width() > 800) {
            navListOrder();
        }
    }

    // mobile
    jQuery(document).on('click', '.mobile-toggle-panel', function (e) {
        e.preventDefault();
        jQuery("body").addClass("mobile-nav");
        jQuery("#site-navigation-custom.main-navigation").addClass("show");
        navCollapse.classList.remove('hasItems');
        jQuery("#navbar-extend > li").each(function () {
            jQuery("#primary-menu").append(jQuery(this));
        });
    });
    jQuery(document).on('click', '.close-mobile', function (e) {
        e.preventDefault();
        jQuery("body").removeClass("mobile-nav");
        jQuery("#site-navigation-custom.main-navigation").removeClass("show");
        jQuery(".menu-item-has-children").removeClass("show-submenu");
    });
    jQuery(document).on('click', '.top-menu.menu-item-has-children', function (e) {
        jQuery('.top-menu.menu-item-has-children').not(this).removeClass("show-submenu");
        jQuery(this).toggleClass("show-submenu");
    });
    jQuery('.bottom-menu.menu-item-has-children').on('click', function (e) {
        e.stopPropagation();
        jQuery('.bottom-menu.menu-item-has-children').not(this).removeClass("show-submenu");
        jQuery(this).toggleClass("show-submenu");
    });

    jQuery('.sub-menu-full-height').css('height', jQuery('.sub-menu-full-height').closest(".sub-menu-parent").height() + 8);

    // still got to get our credit from make projects
    var site = window.location.hostname.split(".")[0];
	// add utm parameters to all makeprojects links that don't have them
	jQuery('[href*="makeprojects.com"]').each(function() {
		var href = jQuery(this).attr('href');
		if (href && href.indexOf('utm') == -1) {
			var medium = site == 'makezine' ? 'blog' : 'make';
			if(jQuery(this).parents('header').length) {
				medium = site + "_nav";
			}
			href += (href.match(/\?/) ? '&' : '?') + 'utm_source=make&utm_medium=' + medium + '&utm_campaign=' + site + '&utm_content=link';
			jQuery(this).attr('href', href);
		}
	});

	// add profile links to makehub sites
	if(jQuery("body").hasClass("buddyboss-theme")) {
		var profilehtml = '<ul id="header-my-account-menu" class="bb-my-account-menu has-icon">' +
                        '<li class="menu-item icon-added">' +
                            '<a href="/members/me/dashboard">' +
                                '<i class="_mi _before buddyboss bb-icon-board-list" aria-hidden="true"></i><span>My Dashboard</span>' +
                            '</a>' +
                        '</li>' +
                        '<li class="menu-item menu-item-facilitator-portal">' +
                            '<a href="/edit-submission/">' +
                                '<i class="_mi _before buddyboss bb-icon-graduation-cap" aria-hidden="true"></i><span>Facilitator Portal</span>' +
                            '</a>' +
                        '</li>' +
                        '<li class="menu-item menu-item-event-cart">' +
                            '<a href="/registration-checkout/?event_cart=view#checkout">' +
                                '<i class="_mi _before buddyboss bb-icon-shopping-cart" aria-hidden="true"></i><span>Event Cart</span>' +
                            '</a>' +
                        '</li>' +
                        '<li class="bp-menu bp-profile-nav menu-item menu-item-has-children">' +
                            '<a href="/members/me/profile/">' +
                                '<i class="_mi _before buddyboss bb-icon-user-alt" aria-hidden="true"></i><span>Profile</span>' +
                            '</a>' +
                            '<div class="wrapper ab-submenu">' +
                                '<ul class="bb-sub-menu">' +
                                    '<li class="bp-menu bp-public-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/profile/">View</a>' +
                                    '</li>' +
                                    '<li class="bp-menu bp-edit-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/profile/edit/">Edit</a>' +
                                    '</li>' +
                                    '<li class="bp-menu bp-change-avatar-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/profile/change-avatar/">Profile Photo</a>' +
                                    '</li>' +
                                    '<li class="bp-menu bp-change-cover-image-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/profile/change-cover-image/">Cover Photo</a>' +
                                    '</li>' +
                                '</ul>' +
                            '</div>' +
                        '</li>' +
                        '<li class="bp-menu bp-settings-nav menu-item menu-item-has-children">' +
                            '<a href="/members/me/settings/">' +
                                '<i class="_mi _before buddyboss bb-icon-settings" aria-hidden="true"></i>' +
                                '<span>Account</span>' +
                            '</a>' +
                            '<div class="wrapper ab-submenu"><ul class="bb-sub-menu">' +
                                    '<li class="bp-menu bp-settings-notifications-sub-nav menu-item no-icon"><a href="/members/me/settings/notifications/">Email Preferences</a></li>' +
                                    '<li class="bp-menu bp-view-sub-nav menu-item no-icon"><a href="/members/me/settings/profile/">Privacy</a></li>' +
                                    '<li class="bp-menu bp-blocked-members-sub-nav menu-item no-icon"><a href="/members/me/settings/blocked-members/">Blocked Members</a></li>' +
                                    '<li class="bp-menu bp-group-invites-settings-sub-nav menu-item no-icon"><a href="/members/me/settings/invites/">Group Invites</a></li>' +
                                    '<li class="bp-menu bp-export-sub-nav menu-item no-icon"><a href="/members/me/settings/export/">Export Data</a></li>' +
                                '</ul></div>' +
                        '</li>' +
                        '<li class="bp-menu bp-friends-nav menu-item menu-item-has-children">' +
                            '<a href="/members/me/friends/">' +
                                '<i class="_mi _before buddyboss bb-icon-users" aria-hidden="true"></i><span>Connections</span></a>' +
                            '<div class="wrapper ab-submenu">' +
                                '<ul class="bb-sub-menu">' +
                                    '<li class="bp-menu bp-my-friends-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/friends/">My Connections</a>' +
                                    '</li>' +
                                    '<li class="bp-menu bp-requests-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/friends/requests/">Requests</a>' +
                                    '</li>' +
                                '</ul>' +
                            '</div>' +
                        '</li>' +
                        '<li class="bp-menu bp-groups-nav menu-item menu-item-has-children">' +
                            '<a href="/members/me/groups/">' +
                                '<i class="_mi _before buddyboss bb-icon-groups" aria-hidden="true"></i>' +
                                '<span>Groups</span>' +
                            '</a>' +
                            '<div class="wrapper ab-submenu">' +
                                '<ul class="bb-sub-menu">' +
                                    '<li class="bp-menu bp-groups-create-nav menu-item no-icon">' +
                                        '<a href="/groups/create/">Create Group</a>' +
                                    '</li>' +
                                    '<li class="bp-menu bp-my-groups-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/groups/">My Groups</a>' +
                                    '</li>' +
                                    '<li class="bp-menu bp-group-invites-sub-nav menu-item no-icon">' +
                                        '<a href="/members/me/groups/invites/">Invitations</a>' +
                                    '</li>' +
                                '</ul>' +
                            '</div>' +
                        '</li>' +
                    '</ul>';
		jQuery("#profileLinks").append(profilehtml);
	}
});
