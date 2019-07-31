// Compiled file - any changes will be overwritten by grunt task
//!!
//!! js/auth0-variables.js
var AUTH0_CLIENT_ID    = 'ZIX3RQReetRyVxYUkRJlJJ6LnbND4lq1';
var AUTH0_DOMAIN       = 'makermedia.auth0.com';

if (typeof templateUrl === 'undefined') {
  var templateUrl = window.location.origin;
}
var AUTH0_CALLBACK_URL = templateUrl + "/authenticate-redirect/";
var AUTH0_REDIRECT_URL = templateUrl;
;//!!
//!! js/auth0.js
window.addEventListener('load', function() {
	// buttons and event listeners
	/*    If the login button, logout button or profile view elements do not exist
	*    (such as in wp-admin and wp - login pages) default to a 'fake' element
	*/
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
		
	jQuery(".trigger-login").click( function() {
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
	
	var webAuth = new auth0.WebAuth({
		domain: AUTH0_DOMAIN,
		clientID: AUTH0_CLIENT_ID,
		redirectUri: AUTH0_CALLBACK_URL,
		audience: 'https://' + AUTH0_DOMAIN + '/userinfo',
		responseType: 'token id_token',
		scope: 'openid email profile',
		leeway: 60
	});

	loginBtn.addEventListener('click', function(e) {
		e.preventDefault();
		localStorage.setItem('redirect_to',location.href);
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

	function setSession(authResult) {
		// Set the time that the access token will expire at
		var expiresAt = JSON.stringify(
		authResult.expiresIn * 1000 + new Date().getTime()
		);
		localStorage.setItem('access_token', authResult.accessToken);
		localStorage.setItem('id_token', authResult.idToken);
		localStorage.setItem('expires_at', expiresAt);
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
      getProfile();

      //login to wordpress if not already
      //check for wordpress cookie
      if ( !jQuery( '.logged-in' ).length ) { // is the user logged in?
        //wait .5 second for auth0 data to be returned from getProfile
        setTimeout(function(){ WPlogin(); }, 0500); //login to wordpress
      }
    } else {
      loginBtn.style.display = 'flex';
      profileView.style.display = 'none';

      if ( jQuery( '.logged-in' ).length ) { // is the user logged in?
        //logout of wordpress if not already
        WPlogout();//login to wordpress
      }
    }
  }

  function getProfile() {
    var accessToken = localStorage.getItem('access_token');

    if (!accessToken) {
      console.log('Access token must exist to fetch profile');
    }
    webAuth.client.userInfo(accessToken, function(err, profile) {
      if (profile) {
        userProfile = profile;
			
        // display the avatar
        document.querySelector('#profile-view img').src = userProfile.picture;
        document.querySelector('#profile-view img').style.display = "block";
		  document.querySelector('#profile-view .profile-email').innerHTML = userProfile.email; 
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

      jQuery.post(ajax_object.ajax_url, data, function(response) {

        if ( jQuery( '#authenticated-redirect' ).length ) { //are we on the authentication page?
            //redirect
          if(localStorage.getItem('redirect_to')){
            var redirect_url = localStorage.getItem('redirect_to'); //retrieve redirect URL
            localStorage.removeItem('redirect_to'); //unset after retrieved
            location.href=redirect_url;
          }else{  //redirect to home page
            location.href=templateUrl;
          }

        }

      }).fail(function() {
        alert( "I'm sorry. We had an issue logging you into our system. Please try the login again." );
      });
    }else{
      if ( jQuery( '#authenticated-redirect' ).length ) {
          console.log('undefined');
          alert("We're having trouble logging you in and ran out of time. Refresh the page and we'll try harder.");
		    jQuery(".redirect-message").html("<a class='reload' href='javascript:location.reload();'>Reload page</a>");
      }
    }
  }

  function WPlogout(){
    //logout of wordpress
    var data = {
      'action': 'mm_wplogout'
    };
    if ( jQuery( '#wpadminbar' ).length ) {
        jQuery( 'body' ).removeClass( 'adminBar' ).removeClass( 'logged-in' );
        jQuery( '#wpadminbar' ).remove();
        jQuery( '#mm-preview-settings-bar' ).remove();
    }

    jQuery.post(ajax_object.ajax_url, data, function(response) {
      window.location.href = 'https://makermedia.auth0.com/v2/logout?returnTo='+templateUrl+ '&client_id='+AUTH0_CLIENT_ID;
    });
  }

  //check if logged in another place
  webAuth.checkSession({},
    function(err, result) {
      if (err) {
        // Remove tokens and expiry time from localStorage
        localStorage.removeItem('access_token');
        localStorage.removeItem('id_token');
        localStorage.removeItem('expires_at');
        if(err.error!=='login_required'){
          console.log(err);
        }
      } else {
        setSession(result);
      }
      displayButtons();
    }
  );

});;//!!
//!! js/custom-select.js
// For the stylish select bar that has everything
if ( jQuery( "#vimeography-galleries" ).length ) {
	var selElement = jQuery(".select select")[0];
	var divCopy = document.createElement("div");
	divCopy.setAttribute("class", "select-selected");
	divCopy.innerHTML = selElement.options[selElement.selectedIndex].innerHTML;
	jQuery(".select")[0].append(divCopy);
	var divList = document.createElement("div");
	divList.setAttribute("class", "select-items select-hide");
	var optionItem;
	jQuery("#vimeography-galleries select option").each( function() {
		optionItem = document.createElement("div");
		optionItem.innerHTML = this.innerHTML;
		optionItem.id = this.value;
		optionItem.addEventListener("click", function(e){
			jQuery("#vimeography-galleries select").val( this.id );
			jQuery("#vimeography-galleries select").change();
		});
		divList.append(optionItem);
	});
	jQuery(".select .select-selected").append(divList);
	jQuery(".select .select-selected").click(function(e) {
		e.stopPropagation();
		closeAllSelect(this);
		jQuery(".select-items").toggleClass("select-hide");
		jQuery(this).toggleClass("select-arrow-active");
	});
	
	function closeAllSelect(elmnt) {
	  var x, y, i, arrNo = [];
	  x = document.getElementsByClassName("select-items");
	  y = document.getElementsByClassName("select-selected");
	  for (i = 0; i < y.length; i++) {
		 if (elmnt == y[i]) {
			arrNo.push(i)
		 } else {
			y[i].classList.remove("select-arrow-active");
		 }
	  }
	  for (i = 0; i < x.length; i++) {
		 if (arrNo.indexOf(i)) {
			x[i].classList.add("select-hide");
		 }
	  }
	}
	document.addEventListener("click", closeAllSelect);
	document.addEventListener("click", closeAllSelect);
};//!!
//!! js/global.js
function setCookie(name,value,days) {
	 var expires = "";
	 if (days) {
		  var date = new Date();
		  date.setTime(date.getTime() + (days*24*60*60*1000));
		  expires = "; expires=" + date.toUTCString();
	 }
	 document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function delete_cookie( name ) {
  document.cookie = name + '=; expires=Tue, 16 Oct 1979 00:00:01 GMT;path=/';
}

function getY(element) {
    var yPosition = 0;
    while(element) {
        yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
        element = element.offsetParent;
    }
    return yPosition;
}

(function($){
	
	  jQuery( ".page-template-page-digital-library footer" ).mouseleave( function() {
		 jQuery( [document.documentElement, document.body] ).animate({
			  scrollTop: jQuery( "#page-content.bluetoad iframe" ).offset().top
		 }, 100);
	 });

    //////////// MY ACCOUNT PAGE TWEAKS ///////////////////
    if ( window.location.pathname == "/myaccount/" ) {
 
      // Fix Hands-On Membershihp Level punctuation visually
      if($('#membershipLevelName').length === 1) {
         var mLevelText = $('#membershipLevelName').text().trim();
         if(mLevelText === 'Hands On') {
            $('#membershipLevelName').text('Hands-On')
         }
      }

       // remove piping from update / cancel
       if( $( "#mm-subscriptions-table" ).length ) {
           var billingDetailBtns = $( "#mm-subscriptions-table tr td:nth-child(4)" ).html();
           billingDetailBtns = billingDetailBtns.replace( '|', '' );
           $( "#mm-subscriptions-table tr td:nth-child(4)" ).html( billingDetailBtns );
           $( ".mm-update-subscription-button" ).text( "Edit" ).addClass( "memButton" );
           $( ".mm-cancel-subscription-button" ).text( "Cancel" );
       }
       if( $( "#mm-gift-history-table" ).length ) { // Add in upgrade information for gift memberships
           $( "#mm-gift-history-table" ).after( "<a href='/faqs'>* For information on upgrading Gift Memberships, see our FAQ</a>" )
       }
        // Keep the email field from being editable on the myaccount page
        $( "#mm-account-details-update-button" ).on( "click", function(){
            setTimeout(function(){
                $( "#mm_email" ).prop( "disabled", true );
            } , 2000 );
        });
    }
    ////////////// CHECKOUT TWEAKS //////////////
    if ( window.location.pathname == "/checkout/" ) {
 
      // Fix Hands-On Membershihp Level punctuation visually
      if($('#membershipLevelName').length === 1) {
         var mLevelText = $('#membershipLevelName').text().trim();
         if(mLevelText === 'Hands On Membership') {
            $('#membershipLevelName').html('<span class="mm-data">Hands-On Membership</span><br>');
         }
      }


        if( getUrlParam( "gift" ) ) {
            $( "#mm_checkbox_is_gift" ).prop( 'checked', true );
        }
        if( !getUrlParam( "rid" ) ) {
            jQuery( ".mm-giftsection" ).css( "display", "none" );
        }
        // If selection is outside of the United States, let them know currency will have to be exchanged
        $( "#mm_field_billing_country" ).change( function() {
            if ( this.value != "United States" ) {
                $( "#mm_label_total_price" ).append( "<span style='font-size: 10px;padding-left: 20px;color: #ddd;' class='currency-warning'>* Currency conversion will be handled by your credit card company.</span>" );
            } else if ( $( '.currency-warning' ).length ) {
                $( '.currency-warning' ).remove();
            }
        });
    }

})(jQuery);


$(window).bind("load", function() {
    /// Restyle some things for the Digital Library ////
    if ( document.location.pathname.indexOf( "/digital-library" ) == 0) {
        $( ".flipbook-lightbox-close" ).removeClass( "fa" ).removeClass( "fa-times" );
    }
});;//!!
//!! js/homepage.js
var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player('videoTarget', {
    videoId: 'SQSr0IwEW2Q', // YouTube Video ID
    playerVars: {
      autoplay: 1,        // Auto-play the video on load
      controls: 0,        // Show pause/play buttons in player
      showinfo: 0,        // Hide the video title
      modestbranding: 1,  // Hide the Youtube Logo
      fs: 1,              // Hide the full screen button
      rel: 0,             // Hide Related Videos
      cc_load_policy: 0, // Hide closed captions
      iv_load_policy: 3,  // Hide the Video Annotations
    },
    events: {
      onReady: function(e) {
        e.target.mute();
      },
      onStateChange: function (event) {
        if (event.data == YT.PlayerState.PAUSED || event.data == YT.PlayerState.ENDED) {
            $("#memVideo .front-page").show();
            $("#memVideo .video-player").hide();
            $(".play-btn").addClass("toggled");
            $(".play-btn").text("PLAY");
        }
        if (event.data == YT.PlayerState.ENDED) {
            $(".play-btn").text("REPLAY");
        }
        if (player.isMuted() && player.getPlayerState() == 2) {
            player.unMute();
            $(".mute-btn").addClass("toggled");
        }
      }
    }
  });
}
(function($) { 
    $("#memVideo .front-page").hide();
    $("#memVideo .video-player").show();
    $(".mute-btn").click(function(){
        if(player.isMuted()) {
            $(".mute-btn").addClass("toggled");
            $(".mute-btn").text("MUTE")
            player.unMute();
        } else {
            $(".mute-btn").removeClass("toggled");
            $(".mute-btn").text("UNMUTE")
            player.mute();
        }
    });
    $(".play-btn").click(function(){
        var state = player.getPlayerState();
        if(state == 1) {
            $(".play-btn").text("PLAY");
            $(".play-btn").addClass("toggled");
            player.pauseVideo();
        } else {
            $("#memVideo .front-page").hide();
            $("#memVideo .video-player").show();
            $(".play-btn").text("PAUSE")
            $(".play-btn").removeClass("toggled");
            player.playVideo();
            $(".mute-btn").addClass("toggled");
            $(".mute-btn").text("MUTE")
            player.unMute();
        }
    });


    /*jQuery(document).scroll(function() {
        var memLvl_position = $( "#memLvls" ).offset().top;
        console.log ( slider_position + " vs " + memLvl_position );
        if ( slider_position > memLvl_position ) {
            $( ".down-circle" ).fadeOut( "fast" );
        }
        if ( slider_position < memLvl_position ) {
            $( ".down-circle" ).fadeIn( "slow" );
        }
    });*/
    
})(jQuery);

$(window).load(function() {

	// Testimonial carousel
	$('.owl-carousel').owlCarousel({
	  loop: true,
	  margin: 10,
	  nav: true,
	  navText: [
		 "<i class='fa fa-caret-left'></i>",
		 "<i class='fa fa-caret-right'></i>"
	  ],
	  autoplay: true,
	  autoplayHoverPause: true,
	  responsive: {
		 0: {
			items: 1
		 },
		 600: {
			items: 1
		 },
		 1000: {
			items: 1
		 }
	  }
	})
});

$(".sliding-link").click(function(e) {
    e.preventDefault();
    var aid = $(this).attr("href");
    $('html,body').animate({scrollTop: $(aid).offset().top - 53},'slow');
});

;//!!
//!! js/navigation.js
/*(function($) {
  $('#hamburger-icon, #hamburger-text, .nav-flyout-underlay').click(function() {
    $('#hamburger-icon').toggleClass('open');
    $('#hamburger-text').animate({opacity: 'toggle'});
    $('#nav-flyout').animate({opacity: 'toggle'});
    $('body').toggleClass('nav-open-no-scroll');
    $('html').toggleClass('nav-open-no-scroll');
    $('.nav-flyout-underlay').animate({opacity: 'toggle'});
  });
  $('.nav-flyout-column').on('click', '.expanding-underline', function(event) {
    if ($(window).width() < 577) { 
      event.preventDefault();
      $(this).toggleClass('underline-open');
      $(this).next('.nav-flyout-ul').slideToggle();
    }
  });
  // fix nav to top on scrolldown, stay fixed for transition from mobile to desktop
  var e = $(".universal-nav");
  var hamburger = $(".nav-hamburger");
  var y_pos = $(".nav-level-2").offset().top;
  if ($(window).width() < 578) {
          jQuery(".auth-target").append(jQuery(".nav-level-1-auth"));
  }
  $(window).on('resize', function(){
      if ($(window).width() < 767) {
          y_pos = 0;
          $(".page-content").css("margin-top", "55px");
      }else{
          y_pos = 75;
          $(".page-content").css("margin-top", "0px");
      }
      if ($(window).width() < 578) {
          jQuery(".auth-target").append(jQuery(".nav-level-1-auth"));
      }else{
          jQuery("nav.container").prepend(jQuery(".nav-level-1-auth"));
      }
  });
  jQuery(document).scroll(function() {
      var scrollTop = $(this).scrollTop();
      if(scrollTop > y_pos && $(window).width() > 767){
          e.addClass("main-nav-scrolled"); 
          hamburger.addClass("ham-menu-animate");
          $("#page-content").css("margin-top", "55px");
      }else if(scrollTop <= y_pos){
          e.removeClass("main-nav-scrolled"); 
          hamburger.removeClass("ham-menu-animate");
          if ($(window).width() > 767) {
            $("#page-content").css("margin-top", "0px");
          }
      }
  });

  // to keep this nav universal, detect site and highlight dynamically
  var site = window.location.hostname;
  var firstpath = $(location).attr('pathname');
    firstpath.indexOf(1);
    firstpath.toLowerCase();
    firstpath = firstpath.split("/")[1];
  var shareSection = site + "/" + firstpath;
  function universalNavActive( site ) {
    jQuery(".nav-" + site).addClass("active-site");
    jQuery(".nav-" + site + " .nav-level-2-arrow").addClass("active-site")
  }
  // each one has to apply to a number of environments
  switch(site) {
    case "make-zine":
    case "makezine":
    case "makezine.wpengine.com":
    case "makezine.staging.wpengine.com":
    case "makezine.com":
        universalNavActive("zine");
        break;
    case "makeco":
    case "makeco.wpengine.com":
    case "makeco.staging.wpengine.com/":
    case "makeco.com":
    case "make.co":
        universalNavActive("make");
        break;
    case "makershed.com":
        universalNavActive("shed")
        break;  
    case "maker-faire":
    case "makerfaire":
    case "https://makerfaire.wpengine.com":
    case "https://makerfaire.staging.wpengine.com":
    case "https://makerfaire.com":
        universalNavActive("faire")
        break;
    default:
          break;
  }
  switch(shareSection) {
    case "maker-share/learning":
    case "makershare/learning":
    case "makeshare.wpengine.com/learning":
    case "makershare.staging.wpengine.com/learning":
    case "makershare.com/learning":
        universalNavActive("share")
        break;
    case "maker-share/":
    case "makershare/":
    case "makeshare.wpengine.com/":
    case "makershare.staging.wpengine.com/":
    case "makershare.com/":
        universalNavActive("share-p")
        break;
    default:
          break;
  }
    
  $("#dropdownMenuLink .avatar").css("display","block");
  // Title is assigned by wordpress function, rather than extend the function, just change through js
  if( $(".make-subnav").length ) {
      $(".make-subnav a").prop('title', $(".make-subnav a").prop("title").replace(/(<([^>]+)>)/ig," "));
  }
	
  $( "#menu-secondary_universal_menu a" ).on( "click", function() {
	   var href = $(this).prop( "href" );
	   if(href.indexOf( "video" ) != -1) {
			delete_cookie( "membership-interest" )
	        setCookie( "membership-interest", "video", 1 );
        }
        else if (href.indexOf( "live-learning" ) != -1) {
            delete_cookie( "membership-interest" )
            setCookie( "membership-interest", "live learning", 1 );
        }else {
		    delete_cookie( "membership-interest" )
	        setCookie( "membership-interest", "digital magazine", 1 );
		}
  });

    // Subnav highlighting for conversion page(s) on Make.co
    if((site.slice(0,6) === 'makeco' || site === 'make.co') && (firstpath === 'mm-error')) {
        var cookies = document.cookie.split(';'),
            targets = [
                'video',
                'digital',
                'live'
            ],
            navSel,
            pageName = false,
            doSubNavHighlight = false,
            $subNav,
            $subNavItem;
        // Take the cookies array and see if it has what we're looking for
        // if so, call it the pageName, e.g. 'live learning'
        cookies.forEach(function(element) {
            var cleaned = element.trim(),
                parts = cleaned.split('=');
            if(parts[0] === 'membership-interest') {
                pageName = parts[1];
            }
        });
        // If we have a pageName, figure out which one it is
        if(pageName) {
            targets.forEach(function(item) {
                if(pageName.indexOf(item) > -1) {
                    doSubNavHighlight = true;
                    navSel = item;
                }
            });
        }
        // If this is the correct site, page, and path, do the subnav highlighting
        if(doSubNavHighlight) {
            // Ordinarily we'd want to cache the jQuery selection first
            // but for performance across sites, we don't bother 
            // looking for it until we know we need it
            $subNav = jQuery('#menu-secondary_universal_menu');
            $subNavItem = $subNav.find('[href*="'+navSel+'"]').closest('li');
            // Probably don't strictly need this, but just to be safe
            $subNav.find('li').each(function(el) {
                $(el).removeClass('active');
            });
            $subNavItem.addClass('active');
            //console.log('manually highlight the nav', $navItem[0]); 
        }
    }

})(jQuery);*/
;//!!
//!! js/recaptcha.js
jQuery(document).ready(function(jQuery){
  // Thank you modal with more newsletter options
  jQuery(".fancybox-thx").fancybox({
    autoSize : false,
    width  : 400,
    autoHeight : true,
    padding : 0,
    afterLoad   : function() {
      this.content = this.content.html();
    }
  });
  // reCAPTCHA error message
  jQuery(".nl-modal-error").fancybox({
    autoSize : false,
    width  : 250,
    autoHeight : true,
    padding : 0,
    afterLoad   : function() {
      this.content = this.content.html();
    }
  });
  // YOUTUBE PLAYER FOR FANCYBOX MODALS
  jQuery(".fancytube").fancybox({
    maxWidth  : 800,
    maxHeight : 600,
    fitToView : false,
    width   : '70%',
    height    : '70%',
    autoSize  : false,
    closeClick  : false,
    openEffect  : 'none',
    closeEffect : 'none',
    padding : 0
  });
});

// Footer
/*
var onSubmitFooter = function(token) {
  jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.footer-sub-form').serialize());
  jQuery('.fancybox-thx').trigger('click');
} 
jQuery(document).on('submit', '.footer-sub-form', function (e) {
  e.preventDefault();
  onSubmitFooter();
});

var recaptchaKey = '6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP';
onloadCallback = function() {
    
    grecaptcha.render('recaptcha-footer', {
      'sitekey' : recaptchaKey,
      'callback' : onSubmitFooter
    });
    //var recaptchaResponse = grecaptcha.getResponse('recaptcha-footer');
    //console.log( "test " + recaptchaResponse );
};*/