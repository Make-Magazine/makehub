describe( 'Filter Button', () => {
	beforeAll( () => {
		String.prototype.className = function() {
			if (
				(
					'string' !== typeof this &&
					! this instanceof String /* eslint-disable-line no-unsafe-negation */
				) ||
				'function' !== typeof this.replace
			) {
				return this;
			}

			return this.replace( '.', '' );
		};

		global.tribe = {};
		require( '../filter-button' );
		tribe.events = {
			views: {
				accordion: {},
			},
		};
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterButton.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Open filter bar', () => {
		test( 'Should open filter bar', () => {
			// Setup test.
			global.tribe_events_filter_bar_js_config = {
				l10n: {
					hide_filters: 'Hide filters',
				},
			};
			tribe.events.views.accordion.setOpenAccordionA11yAttrs = jest.fn();
			const $container = $();
			const $filterButton = $();
			const $filterButtonText = $();
			const $filterBar = $();
			$container.find = ( selector ) => {
				switch ( selector ) {
					case tribe.filterBar.filterButton.selectors.filterButton:
						return $filterButton;
					case tribe.filterBar.filterButton.selectors.filterBar:
						return $filterBar;
					default:
						return $();
				}
			};
			$filterButton.find = () => $filterButtonText;
			$filterButton.addClass = jest.fn();
			$filterButtonText.text = jest.fn();
			$filterBar.addClass = jest.fn();

			// Test.
			tribe.filterBar.filterButton.openFilterBar( $container );

			// Confirm final states.
			expect( tribe.events.views.accordion.setOpenAccordionA11yAttrs.mock.calls.length ).toBe( 2 );
			expect( $filterButton.addClass.mock.calls.length ).toBe( 1 );
			expect( $filterButtonText.text.mock.calls.length ).toBe( 1 );
			expect( $filterBar.addClass.mock.calls.length ).toBe( 1 );

			// Cleanup test.
			delete global.tribe_events_filter_bar_js_config;
		} );
	} );

	describe( 'Close filter bar', () => {
		test( 'Should close filter bar', () => {
			// Setup test.
			global.tribe_events_filter_bar_js_config = {
				l10n: {
					show_filters: 'Show filters',
				},
			};
			tribe.events.views.accordion.setCloseAccordionA11yAttrs = jest.fn();
			const $container = $();
			const $filterButton = $();
			const $filterButtonText = $();
			const $filterBar = $();
			$container.find = ( selector ) => {
				switch ( selector ) {
					case tribe.filterBar.filterButton.selectors.filterButton:
						return $filterButton;
					case tribe.filterBar.filterButton.selectors.filterBar:
						return $filterBar;
					default:
						return $();
				}
			};
			$filterButton.find = () => $filterButtonText;
			$filterButton.removeClass = jest.fn();
			$filterButtonText.text = jest.fn();
			$filterBar.removeClass = jest.fn();

			// Test.
			tribe.filterBar.filterButton.closeFilterBar( $container );

			// Confirm final states.
			expect( tribe.events.views.accordion.setCloseAccordionA11yAttrs.mock.calls.length ).toBe( 2 );
			expect( $filterButton.removeClass.mock.calls.length ).toBe( 1 );
			expect( $filterButtonText.text.mock.calls.length ).toBe( 1 );
			expect( $filterBar.removeClass.mock.calls.length ).toBe( 1 );

			// Cleanup test.
			delete global.tribe_events_filter_bar_js_config;
		} );
	} );

	describe( 'Handle resize', () => {
		let openFilterBarHold;
		let $filterBar;
		let $container;

		beforeEach( () => {
			openFilterBarHold = tribe.filterBar.filterButton.openFilterBar;
			tribe.filterBar.filterButton.openFilterBar = jest.fn();

			$filterBar = $( '<div></div>' );
			$container = $( '<div></div>' );
			$container.find = () => $filterBar;
		} );

		afterEach( () => {
			tribe.filterBar.filterButton.openFilterBar = openFilterBarHold;
		} );

		test( 'Should open filter bar on resize from mobile to desktop', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: false } );
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterButton.openFilterBar.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should not open filter bar on resize from desktop to mobile', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: true } );
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterButton.openFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not open filter bar on resize from mobile to mobile', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: false } );
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterButton.openFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not open filter bar on resize from desktop to desktop', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: true } );
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterButton.openFilterBar.mock.calls.length ).toBe( 0 );
		} );
	} );

	describe( 'Handle click', () => {
		let closeFilterBarHold;
		let $container;

		beforeEach( () => {
			closeFilterBarHold = tribe.filterBar.filterButton.closeFilterBar;
			tribe.filterBar.filterButton.closeFilterBar = jest.fn();

			$container = $( '<div></div>' );
		} );

		afterEach( () => {
			tribe.filterBar.filterButton.closeFilterBar = closeFilterBarHold;
		} );

		test( 'Should return early if not mobile', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterButton.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filter bar if click target parent is filter bar', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: true } );
			const filterBar = `
				<div
					class="tribe-filter-bar tribe-filter-bar--horizontal"
					id="tribe-filter-bar--12345"
					data-js="tribe-filter-bar"
				>
					<form
						class="tribe-filter-bar__form"
						method="post"
						action=""
						aria-labelledby="tribe-filter-bar__form-heading--12345"
						aria-describedby="tribe-filter-bar__form-description--12345"
					>
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			const event = {
				target: $filterBar.find( 'form' )[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterButton.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filter bar if click target parent is filter button', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: true } );
			const filterButton = `
				<button
					class="tribe-events-c-events-bar__filter-button"
					aria-controls="tribe-filter-bar--12345"
					aria-expanded="false"
					data-js="tribe-events-accordion-trigger tribe-events-filter-button"
				>
				</button>
			`;
			const $filterButton = $( filterButton );
			const event = {
				target: $filterButton[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterButton.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filter bar if click target parent is filter button', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				target: $container[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterButton.closeFilterBar.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Handle action done click', () => {
		test( 'Should close filter bar', () => {
			// Setup test.
			global.tribe_events_filter_bar_js_config = {
				l10n: {
					show_filters: 'Show filters',
					hide_filters: 'Hide filters',
				},
			};
			tribe.events.views.accordion.setCloseAccordionA11yAttrs = jest.fn();
			const $container = $( '<div></div>' );
			const $filterButton = $( '<button></button>' );
			const $filterButtonText = $( '<span></span>' );
			const $filterBar = $( '<div></div>' );
			$filterButton.find = () => $filterButtonText;
			$container.find = () => $filterBar;
			$filterButtonText.text = jest.fn();
			$filterButton.removeClass = jest.fn();
			$filterBar.removeClass = jest.fn();
			const event = {
				data: {
					target: $(),
					filterButton: $filterButton,
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleActionDoneClick( event );

			// Confirm final states.
			expect( $filterButtonText.text.mock.calls.length ).toBe( 1 );
			expect( $filterButton.removeClass.mock.calls.length ).toBe( 1 );
			expect( $filterBar.removeClass.mock.calls.length ).toBe( 1 );
			expect( tribe.events.views.accordion.setCloseAccordionA11yAttrs.mock.calls.length ).toBe( 2 );

			// Cleanup test.
			delete global.tribe_events_filter_bar_js_config;
		} );
	} );

	describe( 'Handle filter button click', () => {
		let $container;
		let $filterButtonText;
		let $filterBar;

		beforeEach( () => {
			global.tribe_events_filter_bar_js_config = {
				l10n: {
					show_filters: 'Show filters',
					hide_filters: 'Hide filters',
				},
			};
			tribe.events.views.accordion.setCloseAccordionA11yAttrs = jest.fn();
			tribe.events.views.accordion.setOpenAccordionA11yAttrs = jest.fn();
			tribe.filterBar.filterBarSlider = {
				initSlider: jest.fn(),
				deinitSlider: jest.fn(),
			};
			$container = $( '<div></div>' );
			$filterButtonText = $( '<span></span>' );
			$filterBar = $( '<div></div>' );
			$container.find = () => $filterBar;
			$filterButtonText.text = jest.fn();
			$filterBar.toggleClass = jest.fn();
		} );

		afterEach( () => {
			delete global.tribe_events_filter_bar_js_config;
			delete global.tribe.filterBar.filterBarSlider;
		} );

		test( 'Should close filter bar on click', () => {
			// Setup test.
			const $filterButton = $( '<button></button>' );
			$container.data = () => ( { isMobile: false } );
			$filterButton.addClass( tribe.filterBar.filterButton.selectors.filterButtonActive.className() );
			$filterButton.find = () => $filterButtonText;
			$filterButton.toggleClass = jest.fn();
			const event = {
				data: {
					target: $filterButton,
					actionDone: $(),
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleFilterButtonClick( event );

			// Confirm final states.
			expect( $filterButtonText.text.mock.calls.length ).toBe( 1 );
			expect( $filterButton.toggleClass.mock.calls.length ).toBe( 1 );
			expect( $filterBar.toggleClass.mock.calls.length ).toBe( 1 );
			expect( tribe.events.views.accordion.setCloseAccordionA11yAttrs.mock.calls.length ).toBe( 2 );
		} );

		test( 'Should open filter bar on click', () => {
			// Setup test.
			const $filterButton = $( '<button></button>' );
			$container.data = () => ( { isMobile: false } );
			$filterButton.find = () => $filterButtonText;
			$filterButton.toggleClass = jest.fn();
			const event = {
				data: {
					target: $filterButton,
					actionDone: $(),
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleFilterButtonClick( event );

			// Confirm final states.
			expect( $filterButtonText.text.mock.calls.length ).toBe( 1 );
			expect( $filterButton.toggleClass.mock.calls.length ).toBe( 1 );
			expect( $filterBar.toggleClass.mock.calls.length ).toBe( 1 );
			expect( tribe.events.views.accordion.setOpenAccordionA11yAttrs.mock.calls.length ).toBe( 2 );
		} );

		test( 'Should init slider on click', () => {
			// Setup test.
			const $filterButton = $( '<button></button>' );
			$container.data = () => ( { isMobile: false } );
			$filterButton.find = () => $filterButtonText;
			$filterButton.toggleClass = jest.fn();
			const event = {
				data: {
					target: $filterButton,
					actionDone: $(),
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleFilterButtonClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterBarSlider.initSlider.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarSlider.deinitSlider.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should deinit slider on click', () => {
			// Setup test.
			const $filterButton = $( '<button></button>' );
			$container.data = () => ( { isMobile: true } );
			$filterButton.find = () => $filterButtonText;
			$filterButton.toggleClass = jest.fn();
			const event = {
				data: {
					target: $filterButton,
					actionDone: $(),
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleFilterButtonClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterBarSlider.initSlider.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarSlider.deinitSlider.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Initialize state', () => {
		let $container;
		let $filterBar;

		beforeEach( () => {
			$container = $( '<div></div>' );
			$filterBar = $( '<div></div>' );
			$container.find = () => $filterBar;
			$container.data( 'tribeEventsState', { isMobile: true } );
		} );

		test( 'Should return early if filter bar is not vertical', () => {
			// Test.
			tribe.filterBar.filterButton.initState( $container );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
		} );

		test( 'Should initialize state if filter bar is vertical', () => {
			// Setup test.
			$container.addClass( tribe.filterBar.filterButton.selectors.filterBarVertical.className() );

			// Test.
			tribe.filterBar.filterButton.initState( $container );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
		} );
	} );
} );
