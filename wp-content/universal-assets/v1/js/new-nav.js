jQuery(document).ready(function () {
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
    }
    window.onresize = function() {
        if (jQuery(window).width() < 800 && jQuery("#universal-subnav #menu-secondary_universal_menu").length) {
            jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "76px");
            jQuery("#primary-navbar").prepend(jQuery("#menu-secondary_universal_menu"));
        } else if (jQuery(window).width() > 800 && jQuery("#primary-navbar #menu-secondary_universal_menu").length) {
            jQuery("#masthead.site-header-custom").nextAll().not("script, style, #universal-subnav").first().css("padding-top", "121px");
            jQuery("#universal-subnav").append(jQuery("#menu-secondary_universal_menu"));
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
    var source = window.location.hostname.split(".")[0];
    var _href = jQuery(".mp-nav-link a").attr("href");
    if (_href.indexOf('?') != -1) {
        jQuery(".mp-nav-link a").attr("href", _href + '&utm_source=' + source + "_nav");
    } else {
        jQuery(".mp-nav-link a").attr("href", _href + '?utm_source=' + source + "_nav");
    }

    // Change logo based on site
    var site = window.location.hostname
    switch (site) {
        case "makerfaire.local":
        case "makerfaire.test":
        case "makerfaire.com":
        case "makerfaire.staging.wpengine.com":
        case "dev.makerfaire.com":
        case "stage.makerfaire.com":
            jQuery("h2.site-title a").attr("href", "https://makerfaire.com");
            document.getElementById("navLogo").src = "/wp-content/themes/makerfaire/img/Maker_Faire_Logo.svg";
            break;
        case "makercamp.local":
        case "makercamp.test":
        case "makercamp.com":
        case "makercamp.staging.wpengine.com":
        case "dev.makercamp.com":
        case "stage.makercamp.com":
            jQuery("h2.site-title a").attr("href", "https://makercamp.com");
            document.getElementById("navLogo").src = "/wp-content/themes/makercamp-theme/assets/img/makercamp-logo.png";
            break;
        case "makezine.test":
        case "makezine.local":
        case "makezine.staging.wpengine.com":
        case "makezine.com":
        case "stage.makezine.com":
        case "dev.makezine.com":
            jQuery("h2.site-title a").attr("href", "https://makezine.com");
            jQuery("#site-logo .nav-logo").css("height", "30.4px");
            jQuery("#site-logo .nav-logo").css("margin-top", "-5px");
            break;
        case "makerspaces.makehub.test":
        case "makerspaces.makehub.local":
        case "makerspaces.devmakehub.wpengine.com":
        case "makerspaces.stagemakehub.wpengine.com":
        case "makerspaces.make.co":
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
            break;
        default:// the default is makehub/make.co/makezine
            if (window.location.href.indexOf("makercampus") > -1 || window.location.href.indexOf("maker-campus") > -1) {
                // except for makercampus which gets it's own logo and subnav items
                jQuery("h2.site-title a").attr("href", "https://make.co/maker-campus");
                jQuery("#site-logo .nav-logo").css("margin-top", "-8px");
                document.getElementById("navLogo").src = "/wp-content/universal-assets/v1/images/MakerCampus_Logo_Boxless.png";
                jQuery("#universal-subnav .community-subnav-item").hide();
                jQuery("#universal-subnav .campus-subnav-item").show();
            } else {
                document.getElementById("navLogo").src = "/wp-content/universal-assets/v1/images/make_co_logo.png";
            }
            break;
    }

});

