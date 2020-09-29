describe( 'Filters', () => {
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
		require( '../filters' );
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filters.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Remove square brackets from end', () => {
		test( 'Should remove square brackets from string', () => {
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( 'hello[]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( 'hello[0]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( 'hello[][]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( 'hello[0][1]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( '[]hello[0]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( '[0]hello[]' ) ).toMatchSnapshot();
		} );

		test( 'Should return string', () => {
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( '[]hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( '[0]hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( '[hello]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( 'hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( 'hello[' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( 'hello]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( '[hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.removeSquareBracketsFromEnd( ']hello' ) ).toMatchSnapshot();
		} );
	} );

	describe( 'Has square brackets at end', () => {
		test( 'Should return true', () => {
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( 'hello[]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( 'hello[0]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( 'hello[][]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( 'hello[0][1]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( '[]hello[0]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( '[0]hello[]' ) ).toMatchSnapshot();
		} );

		test( 'Should return false', () => {
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( '[]hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( '[0]hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( '[hello]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( 'hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( 'hello[' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( 'hello]' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( '[hello' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filters.hasSquareBracketsAtEnd( ']hello' ) ).toMatchSnapshot();
		} );
	} );

	describe( 'Remove value from base key query string pieces', () => {
		let queryStringPieces;

		beforeEach( () => {
			queryStringPieces = [
				'hello[0]=world',
				'hello[1]=foo',
				'hello[2]=bar',
			];
		} );

		test( 'Should return original query string pieces array', () => {
			// Test.
			const result = tribe.filterBar.filters.removeValueFromBaseKeyQueryStringPieces( queryStringPieces, 'baz' );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should remove value from query string pieces array', () => {
			// Test.
			const result = tribe.filterBar.filters.removeValueFromBaseKeyQueryStringPieces( queryStringPieces, 'bar' );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should reindex query keys in query string pieces array', () => {
			// Test.
			const result = tribe.filterBar.filters.removeValueFromBaseKeyQueryStringPieces( queryStringPieces, 'foo' );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );
	} );

	describe( 'Remove key value from query string pieces', () => {
		test( 'Should not remove key value from query string pieces', () => {
			// Setup test.
			const queryStringPieces = [
				'hello=world',
				'foo=bar',
				'modern=tribe',
			];

			// Test.
			const result = tribe.filterBar.filters.removeKeyValueFromQueryStringPieces( queryStringPieces, 'foo', 'baz' );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should remove key value from query string pieces', () => {
			// Setup test.
			const queryStringPieces = [
				'hello=world',
				'foo=bar',
				'modern=tribe',
			];

			// Test.
			const result = tribe.filterBar.filters.removeKeyValueFromQueryStringPieces( queryStringPieces, 'foo', 'bar' );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should remove key value from query string pieces when value is true', () => {
			// Setup test.
			const queryStringPieces = [
				'hello=world',
				'foo=bar',
				'modern=tribe',
			];

			// Test.
			const result = tribe.filterBar.filters.removeKeyValueFromQueryStringPieces( queryStringPieces, 'foo', true );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should remove array key value from query string pieces', () => {
			// Setup test.
			const queryStringPieces = [
				'hello=world',
				'foo[0]=bar',
				'foo[1]=baz',
				'modern=tribe',
			];

			// Test.
			const result = tribe.filterBar.filters.removeKeyValueFromQueryStringPieces( queryStringPieces, 'foo[]', 'bar' );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should remove all array keys and values from query string pieces', () => {
			// Setup test.
			const queryStringPieces = [
				'hello=world',
				'foo[0]=bar',
				'foo[1]=baz',
				'modern=tribe',
			];

			// Test.
			const result = tribe.filterBar.filters.removeKeyValueFromQueryStringPieces( queryStringPieces, 'foo[]', true );

			// Confirm final state.
			expect( result ).toMatchSnapshot();
		} );
	} );

	describe( 'Remove key value from query', () => {
		let location;

		beforeEach( () => {
			// url = 'https://test.tri.be/events/month/?hello=world&foo=bar'
			location = {
				href: 'https://test.tri.be/events/month/?hello=world&foo=bar',
				origin: 'https://test.tri.be',
				pathname: '/events/month/',
				search: '?hello=world&foo=bar',
				hash: '',
			};
		} );

		test( 'Should remove key value pair from query', () => {
			// Test.
			const loc1 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'hello', 'world' );
			const loc2 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'foo', 'bar' );

			// Confirm final states.
			expect( loc1 ).toMatchSnapshot();
			expect( loc2 ).toMatchSnapshot();
		} );

		test( 'Should remove key value pair from query and return blank query string', () => {
			// Setup test.
			// url = 'https://test.tri.be/events/month/?hello=world'
			location.href = 'https://test.tri.be/events/month/?hello=world';
			location.search = '?hello=world';

			// Test.
			const loc = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'hello', 'world' );

			// Confirm final states.
			expect( loc ).toMatchSnapshot();
		} );

		test( 'Should remove all instances of key from query', () => {
			// url = 'https://test.tri.be/events/month/?hello=world'
			location.href = 'https://test.tri.be/events/month/?hello[0]=world&foo[0]=bar&hello[1]=goodbye&foo[1]=baz';
			location.search = '?hello[0]=world&foo[0]=bar&hello[1]=goodbye&foo[1]=baz';

			// Test.
			const url1 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'hello[]', true );
			const url2 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'foo[]', true );

			// Confirm final states.
			expect( url1 ).toMatchSnapshot();
			expect( url2 ).toMatchSnapshot();
		} );

		test( 'Should not remove key value pair from query', () => {
			// Test.
			const loc1 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'goodbye', 'world' );
			const loc2 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'foo', 'baz' );

			// Confirm final states.
			expect( loc1 ).toMatchSnapshot();
			expect( loc2 ).toMatchSnapshot();
		} );

		test( 'Should return current url if no query string', () => {
			// Setup test.
			// url = 'https://test.tri.be/events/month/'
			location.href = 'https://test.tri.be/events/month/';
			location.search = '';

			// Test.
			const loc1 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'hello', 'world' );
			const loc2 = tribe.filterBar.filters.removeKeyValueFromQuery( location, 'foo', 'bar' );

			// Confirm final states.
			expect( loc1 ).toMatchSnapshot();
			expect( loc2 ).toMatchSnapshot();
		} );
	} );

	describe( 'Get query to add', () => {
		let queryStringPieces;

		beforeEach( () => {
			queryStringPieces = [ 'hello=world' ];
		} );

		test( 'Should get query to add to existing query string', () => {
			// Test.
			const result = tribe.filterBar.filters.getQueryToAdd( queryStringPieces, 'foo', 'bar' );

			// Confirm final states.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should get query to add to blank query', () => {
			// Setup test.
			queryStringPieces = [];

			// Test.
			const result = tribe.filterBar.filters.getQueryToAdd( queryStringPieces, 'foo', 'bar' );

			// Confirm final states.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should return blank string if key value pair exists in query', () => {
			// Setup test.
			queryStringPieces.push( 'foo=bar' );

			// Test.
			const result = tribe.filterBar.filters.getQueryToAdd( queryStringPieces, 'foo', 'bar' );

			// Confirm final states.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should return blank string if array key value pair exists in query', () => {
			// Setup test.
			queryStringPieces.push( 'foo[0]=bar' );

			// Test.
			const result = tribe.filterBar.filters.getQueryToAdd( queryStringPieces, 'foo[]', 'bar' );

			// Confirm final states.
			expect( result ).toMatchSnapshot();
		} );

		test( 'Should return indexed array query to add if base key value pair exists in query', () => {
			// Setup test.
			queryStringPieces.push( 'foo[0]=bar' );

			// Test.
			const result = tribe.filterBar.filters.getQueryToAdd( queryStringPieces, 'foo[]', 'baz' );

			// Confirm final states.
			expect( result ).toMatchSnapshot();
		} );
	} );

	describe( 'Add key value from query', () => {
		let location;

		beforeEach( () => {
			// url = 'https://test.tri.be/events/month/?hello=world&foo=bar'
			location = {
				href: 'https://test.tri.be/events/month/?hello=world&foo=bar',
				origin: 'https://test.tri.be',
				pathname: '/events/month/',
				search: '?hello=world&foo=bar',
				hash: '',
			};
		} );

		test( 'Should add key value pair to query', () => {
			// Setup test.
			// url = 'https://test.tri.be/events/month/?hello=world'
			location.href = 'https://test.tri.be/events/month/?hello=world';
			location.search = '?hello=world';

			// Test.
			const loc = tribe.filterBar.filters.addKeyValueToQuery( location, 'foo', 'bar' );

			// Confirm final states.
			expect( loc ).toMatchSnapshot();
		} );

		test( 'Should add key value pair to blank query', () => {
			// Setup test.
			// url = 'https://test.tri.be/events/month/'
			location.href = 'https://test.tri.be/events/month/';
			location.search = '';

			// Test.
			const loc = tribe.filterBar.filters.addKeyValueToQuery( location, 'foo', 'bar' );

			// Confirm final states.
			expect( loc ).toMatchSnapshot();
		} );

		test( 'Should return current url if key value pair exists in query', () => {
			// Test.
			const loc = tribe.filterBar.filters.addKeyValueToQuery( location, 'foo', 'bar' );

			// Confirm final states.
			expect( loc ).toMatchSnapshot();
		} );
	} );

	describe( 'Get filters state', () => {
		test( 'Should return false if container is not mobile and not vertical', () => {
			// Setup test.
			const $container = $( '<div></div>' );
			$container.is = () => false;
			$container.data( 'tribeEventsState', { isMobile: false } );

			// Test.
			const result = tribe.filterBar.filters.getFiltersState( $container );

			// Confirm final state.
			expect( result ).toBe( false );
		} );

		test( 'Should return false if there are no filters', () => {
			// Setup test.
			const $container = $( '<div></div>' );
			$container.data( 'tribeEventsState', { isMobile: true } );

			// Test.
			const result = tribe.filterBar.filters.getFiltersState( $container );

			// Confirm final state.
			expect( result ).toBe( false );
		} );

		test( 'Should calculate filter state of 0', () => {
			// Setup test.
			const container = `
				<div>
					<div class="tribe-filter-bar-c-filter"></div>
					<div class="tribe-filter-bar-c-filter"></div>
					<div class="tribe-filter-bar-c-filter"></div>
					<div class="tribe-filter-bar-c-filter"></div>
				</div>
			`;
			const $container = $( container );
			$container.is = () => true;
			$container.data( 'tribeEventsState', { isMobile: false } );

			// Test.
			const result = tribe.filterBar.filters.getFiltersState( $container );

			// Confirm final state.
			expect( result ).toBe( 0 );
		} );

		test( 'Should calculate filter state of 5', () => {
			// Setup test.
			const container = `
				<div>
					<div class="tribe-filter-bar-c-filter tribe-filter-bar-c-filter--open"></div>
					<div class="tribe-filter-bar-c-filter"></div>
					<div class="tribe-filter-bar-c-filter tribe-filter-bar-c-filter--open"></div>
					<div class="tribe-filter-bar-c-filter"></div>
				</div>
			`;
			const $container = $( container );
			$container.is = () => false;
			$container.data( 'tribeEventsState', { isMobile: true } );

			// Test.
			const result = tribe.filterBar.filters.getFiltersState( $container );

			// Confirm final state.
			expect( result ).toBe( 5 );
		} );
	} );

	describe( 'Submit request', () => {
		let windowHold;
		let $container;
		let url;
		let getFiltersStateHold;

		beforeEach( () => {
			windowHold = global.window;
			delete global.window.location;
			global.window = Object.create( window );
			// url = 'https://test.tri.be/events/month/?hello=world'
			global.window.location = {
				href: 'https://test.tri.be/events/month/?hello=world',
			};
			global.tribe.events = {
				views: {
					manager: {
						request: jest.fn(),
						shouldManageUrl: jest.fn().mockImplementation( () => true ),
					},
				},
			};
			$container = $( '<div></div>' );
			$container.trigger = jest.fn();
			$container.data = jest.fn();
			url = 'https://test.tri.be/events/month/';
		} );

		afterEach( () => {
			global.window = windowHold;
			delete global.tribe.events;
			global.tribe.filterBar.filters.getFiltersState = getFiltersStateHold;
		} );

		test( 'Should submit request without filter state', () => {
			// Setup test.
			getFiltersStateHold = tribe.filterBar.filters.getFiltersState;
			tribe.filterBar.filters.getFiltersState = jest.fn().mockImplementation( () => false );

			// Test.
			tribe.filterBar.filters.submitRequest( $container, url );

			// Confirm final states.
			expect( $container.trigger.mock.calls.length ).toBe( 2 );
			expect( $container.data.mock.calls.length ).toBe( 1 );
			expect( global.tribe.events.views.manager.shouldManageUrl.mock.calls.length ).toBe( 1 );
			expect( global.tribe.filterBar.filters.getFiltersState.mock.calls.length ).toBe( 1 );
			expect( global.tribe.events.views.manager.request.mock.calls.length ).toBe( 1 );
			expect( global.tribe.events.views.manager.request.mock.calls[ 0 ][ 0 ] ).toMatchSnapshot();
		} );

		test( 'Should submit request with filter state', () => {
			// Setup test.
			getFiltersStateHold = tribe.filterBar.filters.getFiltersState;
			tribe.filterBar.filters.getFiltersState = jest.fn().mockImplementation( () => 5 );

			// Test.
			tribe.filterBar.filters.submitRequest( $container, url );

			// Confirm final states.
			expect( $container.trigger.mock.calls.length ).toBe( 2 );
			expect( $container.data.mock.calls.length ).toBe( 1 );
			expect( global.tribe.events.views.manager.shouldManageUrl.mock.calls.length ).toBe( 1 );
			expect( global.tribe.filterBar.filters.getFiltersState.mock.calls.length ).toBe( 1 );
			expect( global.tribe.events.views.manager.request.mock.calls.length ).toBe( 1 );
			expect( global.tribe.events.views.manager.request.mock.calls[ 0 ][ 0 ] ).toMatchSnapshot();
		} );
	} );
} );
