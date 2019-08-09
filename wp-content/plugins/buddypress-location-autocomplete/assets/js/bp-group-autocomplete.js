(function ($) {
    "use strict";

    var autocompleteAddress, autocompleteStreet, autocompleteCountry, autocompleteState, autocompleteZip, autocompleteCity;
    var _address = 'bpla-group-address';
    var _street = 'bpla-group-street';
    var _country = 'bpla-group-country';
    var _state = 'bpla-group-state';
    var _zip = 'bpla-group-zip';
    var _city = 'bpla-group-city';
    
    var address_selector = ( $.type( document.getElementById(_address) ) !== "null" ) ? document.getElementById(_address) : '';
    var street_selector = ( $.type( document.getElementById(_street) ) !== "null" ) ? document.getElementById(_street) : '';
    var country_selector = ( $.type( document.getElementById(_country) ) !== "null" ) ? document.getElementById(_country) : '';
    var state_selector = ( $.type( document.getElementById(_state) )!== "null" ) ? document.getElementById(_state) : '' ;
    var zip_selector = ( $.type( document.getElementById(_zip) !== "null" ) ) ? document.getElementById(_zip) : '';
    var city_selector = ( $.type( document.getElementById(_city) !== "null" ) ) ? document.getElementById(_city) : '';
    
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'long_name',
        postal_code: 'short_name'
    };

    window.bp_group_profile = {
        init: function ( ) {
            this.initMap();
        },
        initMap: function ( ) {
            if (address_selector) {
                autocompleteAddress = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */ (
                        address_selector), {
                });

                autocompleteAddress.addListener('place_changed', this.onPlaceChangedAddress);
            }
            if (street_selector) {
                autocompleteStreet = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */ (
                        street_selector), {
                });

                autocompleteStreet.addListener('place_changed', this.onPlaceChangedStreet);
            }
            if (country_selector) {
                autocompleteCountry = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */ (
                                country_selector), {
                    types: ['(regions)'],
                });

                autocompleteCountry.addListener('place_changed', this.onPlaceChangedCountry);
            }
            if (zip_selector) {
                autocompleteZip = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */ (
                                zip_selector), {
                    types: ['(regions)'],
                });

                autocompleteZip.addListener('place_changed', this.onPlaceChangedZip);
            }
            if (state_selector) {
                autocompleteState = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */ (
                                state_selector), {
                    types: ['(regions)'],
                });

                autocompleteState.addListener('place_changed', this.onPlaceChangedState);
            }

            if (city_selector) {
                autocompleteCity = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */ (
                                city_selector), {
                    types: ['(cities)'],
                });

                autocompleteCity.addListener('place_changed', this.onPlaceChangedCity);
            }
        },
        onPlaceChangedAddress: function () {
            
        },
        onPlaceChangedStreet: function () {
            var place = autocompleteStreet.getPlace();

            if ( typeof place.address_components === 'undefined' ) {
                return false;
            }
            if ( street_selector ) {
                street_selector.value = '';
            }
            if ( city_selector ) {
                city_selector.value = '';
            }
            if ( state_selector ) {
                state_selector.value = '';
            }
            if ( country_selector ) {
                country_selector.value = '';
            }
            
            if ( zip_selector ) {
                zip_selector.value = '';
            }
            
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    if ('street_number' == addressType) {
                        street_selector.value = val;
                    }
                    if ('route' == addressType) {
                        street_selector.value = street_selector.value + ' ' + val;
                    }
                    if ( city_selector && 'locality' == addressType ) {
                        city_selector.value = val;
                    }
                    if ( state_selector && 'administrative_area_level_1' == addressType ) {
                        state_selector.value = val;
                    }
                    if ( country_selector && 'country' == addressType ) {
                        country_selector.value = val;
                    }
                    if ( zip_selector && 'postal_code' == addressType ) {
                        zip_selector.value = val;
                    }
                }
            }
        },
        onPlaceChangedCountry: function () {
            var place = autocompleteCountry.getPlace();
            bp_group_profile.fill_place(place);
        },
        onPlaceChangedZip: function () {
            var place = autocompleteZip.getPlace();
            
            if ( street_selector ) {
                street_selector.value = '';
            }
            if ( city_selector ) {
                city_selector.value = '';
            }
            if ( state_selector ) {
                state_selector.value = '';
            }
            if ( country_selector ) {
                country_selector.value = '';
            }
            
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    if ( city_selector && 'locality' == addressType ) {
                        city_selector.value = val;
                    }
                    if ( state_selector && 'administrative_area_level_1' == addressType ) {
                        state_selector.value = val;
                    }
                    if ( country_selector && 'country' == addressType ) {
                        country_selector.value = val;
                    }
                    if ( zip_selector && 'postal_code' == addressType ) {
                        zip_selector.value = val;
                    }
                }
            }
        },
        onPlaceChangedState: function () {
            var place = autocompleteState.getPlace();
            bp_group_profile.fill_place(place);
        },
        onPlaceChangedCity: function () {
            var place = autocompleteCity.getPlace();
            bp_group_profile.fill_place(place);
        },
        fill_place: function (place) {
            
            if ( street_selector ) {
                street_selector.value = '';
            }
            if ( city_selector ) {
                city_selector.value = '';
            }
            if ( state_selector ) {
                state_selector.value = '';
            }
            if ( zip_selector ) {
                zip_selector.value = '';
            }
            if ( country_selector ) {
                 country_selector.value = '';
            }
            
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    if ( city_selector && 'locality' == addressType ) {
                        city_selector.value = val;
                    }
                    if ( state_selector && 'administrative_area_level_1' == addressType ) {
                        state_selector.value = val;
                    }
                    if ( country_selector && 'country' == addressType ) {
                        country_selector.value = val;
                    }
                }
            }
        }
    };
    $(document).on('ready', function ( ) {
        bp_group_profile.init( );
    });
})(jQuery);