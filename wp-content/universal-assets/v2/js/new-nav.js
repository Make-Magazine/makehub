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
        case "makercamp.devmakehub.make.co":
        case "makercamp.stagemakehub.wpengine.com":
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
			sitename = "Make: Magazine";
            break;
        case "makerspaces.makehub.test":
        case "makerspaces.makehub.local":
        case "makerspaces.devmakehub.make.co":
        case "makerspaces.stagemakehub.wpengine.com":
        case "makerspaces.make.co":
			sitename = "Maker Spaces";
            break;
        case "learn.makehub.test":
        case "learn.makehub.local":
        case "learn.devmakehub.wpengine.com":
        case "learn.stagemakehub.wpengine.com":
        case "learn.make.co":
			sitename = "Learning Labs";
            break;
        default:// the default is makehub/make.co
            if (window.location.href.indexOf("makercampus") > -1 || window.location.href.indexOf("maker-campus") > -1) {
				sitename = "Maker Campus";
                // except for makercampus which gets it's own logo and subnav items
                jQuery("h2.site-title a").attr("href", "https://make.co/maker-campus");
                jQuery("#site-logo .nav-logo").css("margin-top", "-8px");
                document.getElementById("navLogo").src = "/wp-content/universal-assets/v2/images/MakerCampus_Logo_Boxless.png";
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
        e.preventDefault();
        jQuery(this).toggleClass('active').next().toggleClass('active');
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
    jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav, div[id^='sm_box_']").first().css("padding-top", "121px");
    if (jQuery(window).width() <= 801) {
        jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav, div[id^='sm_box_']").first().css("padding-top", "76px");
    }

    // Subnav
    jQuery(".site-header-custom").append(jQuery("#universal-subnav"));
    jQuery(".nav-level-2").css("display", "block");
    if (jQuery(window).width() < 801) {
		jQuery("#primary-navbar h3").text(sitename);
        jQuery("#primary-navbar .mobile-subscribe-btn").after(jQuery("#menu-secondary_universal_menu"));
		jQuery("#primary-navbar .mobile-subscribe-btn").after(jQuery("#make-coin"));
    }
    window.onresize = function() {
        if (jQuery(window).width() < 801 && (jQuery("#universal-subnav #menu-secondary_universal_menu").length ) ) {
            jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "76px");
			jQuery("#primary-navbar h3").text(sitename);
	        jQuery("#primary-navbar .mobile-subscribe-btn").after(jQuery("#menu-secondary_universal_menu"));
			jQuery("#primary-navbar .mobile-subscribe-btn").after(jQuery("#make-coin"));
        } else if (jQuery(window).width() >= 801 && jQuery("#primary-navbar #menu-secondary_universal_menu").length ) {
            jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "121px");
            jQuery("#universal-subnav").append(jQuery("#menu-secondary_universal_menu"));
			jQuery("#make-join").before(jQuery("#make-coin"));
			jQuery("#primary-navbar h3").text("");
        }
        if (jQuery(window).width() >= 801) {
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
	jQuery('#profile-view #dropdownMenuLink').on('click', function (e) {
        e.preventDefault();
		jQuery("#profile-view .dropdown-menu").toggleClass("show");
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
	
	// search
	if(jQuery(".ajax-site-search").length) {
		jQuery(".sb-search").after(jQuery(".ajax-site-search"));
		jQuery(".sb-search").on("click", function(){
			jQuery(".ajax-site-search").toggle();
		});
	}

	// add profile links to the user dropdown
	var profilehtml = '<ul id="header-my-account-menu" class="bb-my-account-menu has-icon">' +
                    '<li class="menu-item icon-added">' +
                        '<a href="https://make.co/dashboard">' +
                            '<span>My Dashboard</span>' +
                        '</a>' +
                    '</li>' +
                    /*
                    '<li class="menu-item icon-added">' +
                        '<a href="https://make.co/members/me/membership/">' +
                            '<span>My Membership</span>' +
                        '</a>' +
                    '</li>' +                    
                    '<li class="menu-item icon-added">' +
                        '<a href="https://make.co/members/me/groups/my-groups/">' +
                            '<span>My Groups</span>' +
                        '</a>' +
                    '</li>' +*/
                    /*
                    '<li class="menu-item icon-added">' +
                        '<a href="https://make.co/members/me/profile/">' +
                            '<span>My Profile</span>' +
                        '</a>' +
                    '</li>' +*/
                    /*
                    '<li class="menu-item icon-added">' +
                        '<a href="https://make.co/members/me/messages/">' +
                            '<span>My Messages</span>' +
                        '</a>' +
                    '</li>' +*/
                    '<li class="menu-item icon-added">' +
                        '<a href="https://subscribe.makezine.com/loading.do?omedasite=Make_account_status&r=">' +
                            '<span>My Magazine Subscription</span>' +
                        '</a>' +
                    '</li>' +
                    '<li class="menu-item">' +
                        '<a href="https://make.co/digital-library">' +
                            '<span title="Premium Member Benefit">Access Digital Magazine</span>' +
                        '</a>' +
                    '</li>' +
                    '<hr />' +
                    '<li class="menu-item">' +
                        '<a href="https://discord.gg/mpBkj2hhJ4?utm_source=universal&utm_medium=dropdown&utm_campaign=discord&utm_content=launch">' +
                            '<span>Make: Discord</span>' +
                        '</a>' +
                    '</li>' +  
                    /*
                    '<li class="menu-item icon-added">' +
                        '<a href="https://make.co/activity">' +
                            '<span>Community Activity</span>' +
                        '</a>' +
                    '</li>' +         
                    '<li class="menu-item">' +
                        '<a href="https://make.co/groups/">' +
                            '<span>Groups</span>' +
                        '</a>' +
                    '</li>' +*/
                    /*
                    '<li class="menu-item">' +
                        '<a href="https://make.co/members/">' +
                            '<span>Member Directory</span>' +
                        '</a>' +
                    '</li>' +      */
                    '<li class="menu-item">' +
                        '<a href="https://make.co/video-library">' +
                            '<span title="Premium Member Benefit">Make: Video Library</span>' +
                        '</a>' +
                    '</li>' +   
                '</ul>';
	jQuery("#profileLinks").append(profilehtml);

});
