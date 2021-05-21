jQuery(document).ready(function(){
	/*
	* Allow use of Array.from in implementations that don't natively support it
	function conNavArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }
	*/
	
	// Buddyboss code

	function conNavArray(arr) {
		if (Array.isArray( arr )) {
			for (var i = 0, arr2 = Array( arr.length ); i < arr.length; i++) {
				arr2[i] = arr[i];
			}
			return arr2;
		} else {
			return [].slice.call( arr );
		}
	}

	var primaryWrap = document.getElementById( 'primary-navbar' ),
		primaryNav  = document.getElementById( 'primary-menu' ),
		extendNav   = document.getElementById( 'navbar-extend' ),
		navCollapse = document.getElementById( 'navbar-collapse' );

	function navListOrder() {
		var eChildren = extendNav.children;
		var numW      = 0;

		[].concat( conNavArray( eChildren ) ).forEach(
			function (item) {
				item.outHTML = '';
				primaryNav.appendChild( item );
			}
		);

		var primaryWrapWidth = primaryWrap.offsetWidth,
			navCollapseWidth = navCollapse.offsetWidth + 30,
			primaryWrapCalc  = primaryWrapWidth - navCollapseWidth,
			primaryNavWidth  = primaryNav.offsetWidth,
			pChildren        = primaryNav.children;

		[].concat( conNavArray( pChildren ) ).forEach(
			function (item) {
				numW += item.offsetWidth + 5;

				if (numW > primaryWrapCalc) {
					item.outHTML = '';
					extendNav.appendChild( item );
				}

			}
		);

		if (extendNav.getElementsByTagName( 'li' ).length >= 1) {
			navCollapse.classList.add( 'hasItems' );
		} else {
			navCollapse.classList.remove( 'hasItems' );
		}

		primaryNav.classList.remove( 'bb-primary-overflow' );
	}

	if (typeof (primaryNav) != 'undefined' && primaryNav != null) {
		if (jQuery(window).width() > 800) {
			window.onresize = navListOrder;
		}
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

		jQuery( '.bb-toggle-panel' ).on(
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
	
	jQuery( document ).on('click', '.more-button', function (e) {
		e.preventDefault();
		jQuery( this ).toggleClass( 'active' ).next().toggleClass( 'active' );
	});

	jQuery( document ).on('click', '.hideshow .sub-menu a', function (e) {
		// e.preventDefault();
		jQuery( 'body' ).trigger( 'click' );

		// add 'current' and 'selected' class
		var currentLI = $( this ).parent();
		currentLI.parent( '.sub-menu' ).find( 'li' ).removeClass( 'current selected' );
		currentLI.addClass( 'current selected' );
	});
	jQuery( document ).on('click', '.header-aside div.menu-item-has-children > a', function (e) {
		e.preventDefault();
		var current = jQuery( this ).closest( 'div.menu-item-has-children' );
		current.siblings( '.selected' ).removeClass( 'selected' );
		current.toggleClass( 'selected' );
	});

	jQuery( 'body' ).mouseup(function (e) {
		var container = jQuery( '.header-aside div.menu-item-has-children *' );
		if ( ! container.is( e.target )) {
			jQuery( '.header-aside div.menu-item-has-children' ).removeClass( 'selected' );
		}
	});
	
	// make some room for our fixed header
	jQuery("#masthead.site-header-custom").nextAll().not("script, style").first().css("padding-top", "76px");
	
	// mobile
	jQuery( document ).on('click', '.mobile-toggle-panel', function (e) {
		e.preventDefault();
		jQuery( "body" ).addClass("mobile-nav");
		jQuery( "#site-navigation.main-navigation" ).addClass("show");
		navCollapse.classList.remove( 'hasItems' );
		jQuery("#navbar-extend > li").each(function(){
			jQuery("#primary-menu").append(jQuery(this));
		});
	});
	jQuery( document ).on('click', '.close-mobile', function (e) {
		e.preventDefault();
		jQuery( "body" ).removeClass("mobile-nav");
		jQuery( "#site-navigation.main-navigation" ).removeClass("show");
		jQuery( ".menu-item-has-children" ).removeClass("show-submenu");
	});
	jQuery( document ).on('click', '.top-menu.menu-item-has-children', function (e) {
		jQuery( '.top-menu.menu-item-has-children' ).not(this).removeClass("show-submenu");
		jQuery( this ).toggleClass("show-submenu");
	});
	jQuery( '.bottom-menu.menu-item-has-children' ).on('click', function (e) {
		e.stopPropagation();
		jQuery( '.bottom-menu.menu-item-has-children' ).not(this).removeClass("show-submenu");
		jQuery( this ).toggleClass("show-submenu");
	});
	
	/* still got to get our credit from make projects
	var source = window.location.hostname.substr(0, source.indexOf('.')); 
	var _href = jQuery(".mp-nav-link a").attr("href");
	if (_href.indexOf('?') != -1) {
		jQuery(".mp-nav-link a").attr("href", _href + '&utm_source=' + source + "_nav");	
	} else {
		jQuery(".mp-nav-link a").attr("href", _href + '?utm_source=' + source + "_nav");	
	}
	*/

});

