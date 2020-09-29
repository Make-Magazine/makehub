/* global tribe, tribe_events_filter_bar_js_config */
/* eslint-disable no-var, strict */

/**
 * Makes sure we have all the required levels on the Tribe Object.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar = tribe.filterBar || {};

/**
 * Configures Filter Button Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filterButton = {};

/**
 * Initializes in a Strict env the code that manages the filter Button.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filterButton
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 5.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		actionDone: '[data-js="tribe-filter-bar__action-done"]',
		filterBarVertical: '.tribe-events--filter-bar-vertical',
		filterBar: '[data-js~="tribe-filter-bar"]',
		filterBarOpen: '.tribe-filter-bar--open',
		filterBarMobileClosed: '.tribe-filter-bar--mobile-closed',
		filterButton: '[data-js~="tribe-events-filter-button"]',
		filterButtonActive: '.tribe-events-c-events-bar__filter-button--active',
		filterButtonText: '.tribe-events-c-events-bar__filter-button-text',
		filtersSliderContainer: '[data-js="tribe-filter-bar-filters-slider-container"]',
		select2ChoiceRemove: '.select2-selection__choice__remove',
	};

	/**
	 * Open filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.openFilterBar = function( $container ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $filterButtonText = $filterButton.find( obj.selectors.filterButtonText );
		var $actionDone = $container.find( obj.selectors.actionDone );
		var $filterBar = $container.find( obj.selectors.filterBar );

		$filterButton.addClass( obj.selectors.filterButtonActive.className() );
		$filterButtonText.text( tribe_events_filter_bar_js_config.l10n.hide_filters );
		$filterBar.addClass( obj.selectors.filterBarOpen.className() );
		$filterBar.removeClass( obj.selectors.filterBarMobileClosed.className() );

		tribe.events.views.accordion.setOpenAccordionA11yAttrs( $filterButton, $filterBar );
		tribe.events.views.accordion.setOpenAccordionA11yAttrs( $actionDone, $filterBar );

		// Set open state in session storage so we can ensure that the filters remain open/closed while receiving updates via AJAX.
		window.sessionStorage.setItem( 'filterBarOpen', true );
	};

	/**
	 * Close filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.closeFilterBar = function( $container ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $filterButtonText = $filterButton.find( obj.selectors.filterButtonText );
		var $actionDone = $container.find( obj.selectors.actionDone );
		var $filterBar = $container.find( obj.selectors.filterBar );

		$filterButton.removeClass( obj.selectors.filterButtonActive.className() );
		$filterButtonText.text( tribe_events_filter_bar_js_config.l10n.show_filters );
		$filterBar.removeClass( obj.selectors.filterBarOpen.className() );
		$filterBar.addClass( obj.selectors.filterBarMobileClosed.className() );

		tribe.events.views.accordion.setCloseAccordionA11yAttrs( $filterButton, $filterBar );
		tribe.events.views.accordion.setCloseAccordionA11yAttrs( $actionDone, $filterBar );

		// Set open state in session storage so we can ensure that the filters remain open/closed while receiving updates via AJAX.
		window.sessionStorage.setItem( 'filterBarOpen', false );
	};

	/**
	 * Handler for resize event on vertical filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */

	obj.handleResize = function( event ) {
		var $container = event.data.container;

		var $filterBar = $container.find( obj.selectors.filterBar );
		var state = $filterBar.data( 'tribeEventsState' );
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		if ( $container.is( obj.selectors.filterBarVertical ) ) {
			// Open vertical filter bar on resize to desktop.
			if ( ! isMobile && ! state.filterButtonDesktopInitialized ) {
				obj.openFilterBar( $container );

				state.filterButtonDesktopInitialized = true;
				$filterBar.data( 'tribeEventsState', state );

			// Reset `filterButtonDesktopInitialized` state on resize to mobile.
			} else if ( isMobile && state.filterButtonDesktopInitialized ) {
				state.filterButtonDesktopInitialized = false;
				$filterBar.data( 'tribeEventsState', state );
			}
		}

		if ( isMobile ) {
			obj.closeFilterBar( $container );
			obj.handleFilterButtonToggle( $container, false );
			$filterBar.addClass( obj.selectors.filterBarMobileClosed.className() );
		}
	};

	/**
	 * Handler for document click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleClick = function( event ) {
		var $container = event.data.container;
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		// Return early if not mobile.
		if ( ! isMobile ) {
			return;
		}

		var $target = $( event.target );
		var isParentFilterBar = Boolean( $target.closest( obj.selectors.filterBar ).length );
		var isParentFilterButton = Boolean( $target.closest( obj.selectors.filterButton ).length );
		var isParentSelect2ChoiceRemove = Boolean( $target.closest( obj.selectors.select2ChoiceRemove ).length );

		if ( ! isParentFilterBar && ! isParentFilterButton && ! isParentSelect2ChoiceRemove ) {
			obj.closeFilterBar( $container );
			obj.handleFilterButtonToggle( $container, false );
		}
	};

	/**
	 * Handler for document unload event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of unload event.
	 *
	 * @return {void}
	 */
	obj.handleUnload = function( event ) {
		// When leaving the page, clear the sessionStorage state so we don't inappropriately persist across pages.
		window.sessionStorage.setItem( 'filterBarOpen', false );
	};

	/**
	 * Handler for action done button click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleActionDoneClick = function( event ) {
		$( obj.selectors.filterButton ).trigger( 'click' );
	};

	/**
	 * Handler for filter button click event.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object of click event.
	 *
	 * @return {void}
	 */
	obj.handleFilterButtonClick = function( event ) {
		var $container = event.data.container;
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;
		var $filterButton = event.data.target;
		var $filterBar = $container.find( obj.selectors.filterBar );

		obj.handleMobileFilterbarToggle( $container );

		var isOpen       = $filterButton.is( obj.selectors.filterButtonActive );
		var newOpenState = ! isOpen;

		obj.handleFilterButtonToggle( $container, newOpenState );

		$filterBar.toggleClass( obj.selectors.filterBarOpen.className() );

		if ( isMobile ) {
			tribe.filterBar.filterBarSlider.deinitSlider( $container );
		} else {
			tribe.filterBar.filterBarSlider.initSlider( $container );
		}

		// Set open state in session storage so we can ensure that the filters remain open while receiving updates via AJAX.
		window.sessionStorage.setItem( 'filterBarOpen', newOpenState );
	};

	/**
	 * Handles all the behavioral and attribute changes on toggling filter bar.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 * @param {boolean} isOpen
	 *
	 * @return {void}
	 */
	obj.handleFilterButtonToggle = function( $container, isOpen ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $filterBar = $container.find( obj.selectors.filterBar );
		var $actionDone = $container.find( obj.selectors.actionDone );
		var $filterButtonText = $filterButton.find( obj.selectors.filterButtonText );
		var text = isOpen
			? tribe_events_filter_bar_js_config.l10n.hide_filters
			: tribe_events_filter_bar_js_config.l10n.show_filters;

		var setAccordionA11yAttrs = isOpen
			? tribe.events.views.accordion.setOpenAccordionA11yAttrs
			: tribe.events.views.accordion.setCloseAccordionA11yAttrs;

		$filterButtonText.text( text );

		if ( isOpen ) {
			$filterButton.addClass( obj.selectors.filterButtonActive.className() );
		} else {
			$filterButton.removeClass( obj.selectors.filterButtonActive.className() );
		}

		setAccordionA11yAttrs( $filterButton, $filterBar );
		setAccordionA11yAttrs( $actionDone, $filterBar );
	};

	/**
	 * Handles special logic for mobile Filter Bar toggling.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.handleMobileFilterbarToggle = function( $container ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		if ( ! isMobile ) {
			return;
		}

		if (
			! $filterBar.is( obj.selectors.filterBarOpen )
			|| $filterBar.is( obj.selectors.filterBarMobileClosed )
		) {
			$filterBar.removeClass( obj.selectors.filterBarOpen.className() );
		}

		$filterBar.toggleClass( obj.selectors.filterBarMobileClosed.className() );
	};

	/**
	 *
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initMobile = function ( $container ) {
		var $filterBar = $container.find( obj.selectors.filterBar );
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		if ( ! isMobile ) {
			return;
		}

		obj.handleFilterButtonToggle( $container, false );

		if (
			'true' === window.sessionStorage.getItem( 'filterBarOpen' )
			&& $filterBar.is( obj.selectors.filterBarMobileClosed )
		) {
			$( obj.selectors.filterButton ).trigger( 'click' );
		}
	};

	/**
	 * Unbind events for filter toggles functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( $container ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $actionDone = $container.find( obj.selectors.actionDone );
		$filterButton.off( 'click', obj.handleFilterButtonClick );
		$actionDone.off( 'click', obj.handleActionDoneClick );
		$document.off( 'click', obj.handleClick );
		$container.off( 'resize.tribeEvents', obj.handleResize );
	};

	/**
	 * Bind events for filter button functionality.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		var $filterButton = $container.find( obj.selectors.filterButton );
		var $actionDone = $container.find( obj.selectors.actionDone );
		$filterButton.on(
			'click',
			{ target: $filterButton, actionDone: $actionDone, container: $container },
			obj.handleFilterButtonClick,
		);
		$actionDone.on(
			'click',
			{ target: $actionDone, filterButton: $filterButton, container: $container },
			obj.handleActionDoneClick,
		);
		$document.on( 'click', { container: $container }, obj.handleClick );

		// If a user navigates away via the browser, trigger the unload method to clear sessionStorage.
		window.addEventListener( 'beforeunload', obj.handleUnload );

		// If a user clicks on _any_ link, trigger the unload method to clear sessionStorage.
		$document.on( 'beforeOnLinkClick.tribeEvents', obj.handleUnload );

		$container.on( 'resize.tribeEvents', { container: $container }, obj.handleResize );
	};

	/**
	 * Initializes filter button state.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.initState = function( $container ) {
		// Return early if filter bar is not vertical.
		if ( ! $container.is( obj.selectors.filterBarVertical ) ) {
			return;
		}

		var $filterBar = $container.find( obj.selectors.filterBar );
		var containerState = $container.data( 'tribeEventsState' );
		var isMobile = containerState.isMobile;

		var state = {
			filterButtonDesktopInitialized: ! isMobile,
		};

		$filterBar.data( 'tribeEventsState', state );
	};

	/**
	 * Deinitialize filter button JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeAjaxSuccess.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.deinit = function( event ) {
		var $container = event.data.container;
		obj.unbindEvents( $container );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filter button JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event}   event      event object for 'afterSetup.tribeEvents' event.
	 * @param  {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container ) {
		obj.initState( $container );
		obj.bindEvents( $container );
		obj.initMobile( $container );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filter button when Document is ready.
	 *
	 * @since 5.0.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready.
	$document.ready( obj.ready );
} )( jQuery, tribe.filterBar.filterButton );
