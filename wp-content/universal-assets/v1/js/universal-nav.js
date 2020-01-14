(function ($) {
    var domain = window.location.host;
    var parts = domain.split('.');
    var subdomain = parts[0];
    if (subdomain != "community") {
        jQuery("#community-login").remove();
    }
    // keep these from happening before any angular or login scripts
    jQuery(window).bind("load", function () {
        // to keep this nav universal, detect site and do the things universally
        var site = window.location.hostname,
                firstpath = jQuery(location).attr('pathname'),
                e = jQuery(".universal-nav"),
                hamburger = jQuery(".nav-hamburger"),
                y_pos = jQuery(".nav-level-2").offset().top,
                nextItemUnderNav = ""; // this varies from site to site
        firstpath.indexOf(1);
        firstpath.toLowerCase();
        firstpath = firstpath.split("/")[1];
        var shareSection = site + "/" + firstpath;

        function universalNavActive(site) {
            jQuery(".nav-" + site).addClass("active-site");
            jQuery(".nav-" + site + " .nav-level-2-arrow").addClass("active-site");
        }
        function toggleMobileSection(section) {
            if (jQuery(window).width() < 577) {
                jQuery(".nav-" + section + " .expanding-underline").toggleClass('underline-open');
                jQuery(".nav-" + section + " .expanding-underline").next('.nav-flyout-ul').slideToggle();
            }
        }
        // each one has to apply to a number of environments
        switch (site) {
            case "make-zine":
            case "makezine":
            case "makezine.wpengine.com":
            case "makezine.staging.wpengine.com":
            case "makezine.com":
            case "stage.makezine.com":
            case "dev.makezine.com":
                universalNavActive("zine");
                toggleMobileSection("zine");
                nextItemUnderNav = jQuery("#home-featured"); // Makezine doesn't stay consistent
                if (jQuery(".second-nav").length && jQuery(".second-nav").css("display") != "none") {
                    nextItemUnderNav = jQuery(".second-nav");
                } else {
                    if (jQuery(".mz-story-infinite-view").length) {
                        nextItemUnderNav = jQuery(".mz-story-infinite-view");
                    }
                    if (jQuery(".ad-unit").length) {
                        nextItemUnderNav = jQuery(".ad-unit");
                    }
                    if (jQuery(".gift-guide-container").length) {
                        nextItemUnderNav = jQuery(".gift-guide-container");
                    }
                }
                break;
            case "makeco":
            case "makeco.wpengine.com":
            case "makeco.staging.wpengine.com":
            case "make.co":
            case "stage.make.co":
            case "dev.make.co":
                universalNavActive("make");
                toggleMobileSection("make");
                nextItemUnderNav = jQuery(".page-content");
                conversionPageHighlights();
                break;
            case "makershed":
            case "www.makershed":
                universalNavActive("shed");
                toggleMobileSection("shed");
                nextItemUnderNav = jQuery(".maker-page")
                break;
            case "maker-faire":
            case "makerfaire":
            case "makerfaire.wpengine.com":
            case "makerfaire.staging.wpengine.com":
            case "makerfaire.com":
            case "stage.makerfaire.com":
            case "dev.makerfaire.com":
                universalNavActive("faire");
                toggleMobileSection("faire");
                nextItemUnderNav = jQuery("#main");
                break;
            case "makerspaces":
            case "makerspaces.wpengine.com":
            case "makerspaces.staging.wpengine.com":
            case "spaces.makerspace.com":
            case "makerspaces.make.co":
            case "stage.makerspace.com":
            case "dev.makerspace.com":
                nextItemUnderNav = jQuery(".main-content");
                break;
            case "makehub.local":
            case "makehub":
            case "devmakehub.wpengine.com":
            case "stagemakehub.wpengine.com":
            case "community.make.co":
                universalNavActive("community");
                toggleMobileSection("community");
                nextItemUnderNav = jQuery(".main-content");
                break;
            case "learn.make.co":
            case "learn.makehub.local":
            case "learn.makehub.wpengine.com":
            case "learn.makehub.staging.wpengine.com":
                universalNavActive("learn");
                toggleMobileSection("learn");
                nextItemUnderNav = jQuery(".main-content");
                break;
            default:// the default is pretty much makermedia right now
                nextItemUnderNav = jQuery("#page-content");
                break;
        }
        switch (shareSection) {
            case "makershare/":
            case "makeshare.wpengine.com/":
            case "makershare.staging.wpengine.com/":
            case "makershare.com/":
                universalNavActive("share-p");
                toggleMobileSection("share");
                nextItemUnderNav = jQuery(".main-container");
                break;
            default:
                break;
                // as sites get more universal on the hub, the above switch case will become necessary for less things
                nextItemUnderNav = jQuery(".main-content");
        }

        jQuery('#hamburger-click-event, .nav-flyout-underlay').click(function () {
            jQuery('.stagingMsg').toggleClass('gone');
            jQuery('#hamburger-icon').toggleClass('open');
            jQuery('#hamburger-text').animate({opacity: 'toggle'});
            jQuery('#nav-flyout').animate({opacity: 'toggle'});
            jQuery('body').toggleClass('nav-open-no-scroll');
            jQuery('html').toggleClass('nav-open-no-scroll');
            jQuery('.nav-flyout-underlay').animate({opacity: 'toggle'});
        });

        jQuery('.nav-flyout-column').on('click', '.expanding-underline', function (event) {
            if (jQuery(window).width() < 577) {
                event.preventDefault();
                jQuery(this).toggleClass('underline-open');
                jQuery(this).next('.nav-flyout-ul').slideToggle();
            }
        });

        // fix nav to top on scrolldown, stay fixed for transition from mobile to desktop
        if (jQuery(window).width() < 578) {
            jQuery(".auth-target").append(jQuery(".nav-level-1-auth"));
            jQuery("#dropdownMenuLink").click(function () {
                jQuery(".expanding-underline").removeClass("underline-open");
                jQuery(".nav-flyout-ul").css("display", "none");
            })
        }
        if (jQuery(window).width() < 767) {
            nextItemUnderNav.css("margin-top", "115px"); // initially start it lower as well
        }
        jQuery(window).on('resize', function () {
            if (jQuery(window).width() < 767) {
                y_pos = 0;
                nextItemUnderNav.css("margin-top", "115px");
                jQuery(".nav-flyout-ul").css("display", "none");
            } else {
                if (jQuery(".banner-bar").height) {
                    y_pos = jQuery(".banner-bar").height() + jQuery(".nav-level-1").height();
                } else {
                    y_pos = jQuery(".nav-level-1").height();
                }

                nextItemUnderNav.css("margin-top", "0px");
                jQuery(".nav-flyout-ul").css("display", "block");
            }
            if (jQuery(window).width() < 578) {
                jQuery(".auth-target").append(jQuery(".nav-level-1-auth"));
            } else {
                jQuery("nav.container").prepend(jQuery(".nav-level-1-auth"));
            }
        });
        jQuery(document).scroll(function () {
            var scrollTop = jQuery(this).scrollTop();
            if (scrollTop > y_pos && jQuery(window).width() > 767) {
                e.addClass("main-nav-scrolled");
                jQuery("body").addClass("scrolled");
                hamburger.addClass("ham-menu-animate");
                // make hamburger dynamic to height of banner
                if (document.querySelector(".banner-bar") != null) {
                    jQuery(".nav-hamburger .container").css("margin-top", jQuery(".banner-bar").height() + jQuery(".nav-level-1"));
                } else {
                    jQuery(".nav-hamburger .container").css("margin-top", "0px");
                }
                nextItemUnderNav.css("margin-top", "55px");
            } else if (scrollTop <= y_pos) {
                e.removeClass("main-nav-scrolled");
                jQuery("body").removeClass("scrolled");
                hamburger.removeClass("ham-menu-animate");
                jQuery(".nav-hamburger .container").css("margin-top", "0px");
                if (jQuery(window).width() > 767) {
                    nextItemUnderNav.css("margin-top", "0px");
                }
            }
        });
        jQuery("#dropdownMenuLink .avatar").css("display", "block");

        // Title is assigned by wordpress function, rather than extend the function, just change through js
        jQuery('.make-subnav a').each(function () {
            jQuery(this).prop('title', jQuery(this).prop("title").replace(/(<([^>]+)>)/ig, " "));
        });

    });
})(jQuery);