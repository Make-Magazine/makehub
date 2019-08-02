// Compiled file - any changes will be overwritten by grunt task
//!!
//!! includes/js/angular/makerspaces-map-app/makerSpacesApp.js
(function(angular) {
  'use strict';
  angular.module('makerSpacesApp', ['angularUtils.directives.dirPagination', 'ordinal']);

  angular.module('makerSpacesApp').factory('FaireMapsSharedData', ['$q', function($q) {
      var defer = $q.defer();
      var FaireMapsSharedData = {
        gmarkers1: [],
        infowindow: undefined,
        mapDone: function() {
          return defer.promise;
        },
        setMapDone: function() {
          defer.notify(true);
        }
      };
      return FaireMapsSharedData;
    }]);
})(window.angular);
;//!!
//!! includes/js/angular/makerspaces-map-app/GMapsInitializer.factory.js
(function(angular) {
  'use strict';
  angular.module('makerSpacesApp').factory('GMapsInitializer', ['$window', '$q',
    function($window, $q) {
      var asyncUrl = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDtWsCdftU2vI9bkZcwLxGQwlYmNRnT2VM&v=3.exp&callback=googleMapsInitialized',
          mapsDefer = $q.defer();
      //Callback function - resolving promise after maps successfully loaded
      $window.googleMapsInitialized = mapsDefer.resolve;
      //Async loader
      var asyncLoad = function(asyncUrl) {
        var script = document.createElement('script');
        script.src = asyncUrl;
        document.body.appendChild(script);
      };
      //Start loading google maps
      asyncLoad(asyncUrl);
      //Usage: GMapsInitializer.then(callback)
      return mapsDefer.promise;
    }
  ]);
})(window.angular);
;//!!
//!! includes/js/angular/makerspaces-map-app/MapCtrl.controller.js
(function(angular) {
  'use strict';
  angular.module('makerSpacesApp').controller('MapCtrl', ['$http', '$rootScope', '$filter', 'FaireMapsSharedData',
    function($http, $rootScope, $filter, FaireMapsSharedData) {
      var ctrl = this;
      var faireFilters = {
        filters: ['Flagship', 'Featured', 'Mini'],
        search: ''
      };

      $rootScope.$on('toggleMapFilter', function(event, args) {
        var index = faireFilters.filters.indexOf(args.filter);
        if (args.state && index < 0) {
          faireFilters.filters.push(args.filter);
        } else if (!args.state && index > -1) {
          faireFilters.filters.splice(index, 1);
        }
        ctrl.applyMapFilters();
      });
      ctrl.toggleBox = function(type) {
        jQuery('.filters faires-map-filter').each(function(index,obj){
          var filter = jQuery(obj).attr( "filter");
          var index = faireFilters.filters.indexOf(filter);
          //if the filter is not the same as the selected type
          if(filter!==type){
            //uncheck and remove from the faireFilters Object
            jQuery(obj).find('input').prop( "checked", false );
            if (index > -1) {
              faireFilters.filters.splice(index, 1);
            }
          }else{
            //make sure it is checked and add to the faireFilters Object if it's not there
            jQuery(obj).find('input').prop( "checked", true );
            if (index < 0) {
              faireFilters.filters.push(filter);
            }
          }
        });

        ctrl.applyMapFilters();
      }

      ctrl.applyMapFilters = function() {
        FaireMapsSharedData.infowindow.close();
        faireFilters.search = ctrl.filterText;
        var newModel = [];

        // check if "sorting.search" string exists in marker object:
        function containsString(marker) {
          if (!faireFilters.search) {
            return true;
          }
          function checkForValue(json, value) {
            for (var key in json) {
              if (typeof(json[key]) === 'object') {
                checkForValue(json[key], value);
              } else if (typeof(json[key]) === 'string' && json[key].toLowerCase().match(value)) {
                return true;
              }

            }
            return false;

          }
          return checkForValue(marker, faireFilters.search.toLowerCase());
        }
        // check if type matches ok:
        function isTypeToggled(marker) {
          return (faireFilters.filters.indexOf(marker.mmap_type) > -1);
        }

        FaireMapsSharedData.gmarkers1.map(function(marker) {
          var rowData = marker.dataRowSrc;
          //if (containsString(rowData) && isTypeToggled(rowData)) {
          if (containsString(rowData)) {
            newModel.push(rowData);
            marker.setVisible(true);
          } else {
            marker.setVisible(false);
          }
        });
        ctrl.faireMarkers = newModel;
      };

      //get map data
      var formID = 2;
      $http.get('/wp-json/makemap/v1/mapdata/'+formID)
        .then(function successCallback(response) {
          ctrl.faireMarkers = response && response.data && response.data.Locations;
          FaireMapsSharedData.mapDone().then(null, null, function() {
            ctrl.applyMapFilters();
          });
        }, function errorCallback() {
          // error
        });
    }
  ]);
})(window.angular);
;//!!
//!! includes/js/angular/makerspaces-map-app/faireMapsFilter.component.js
(function(angular) {
  'use strict';
  angular.module('makerSpacesApp').component('fairesMapFilter', {
    template: '<div class="checkbox">\
        <label>\
          <input type="checkbox" class="checkbox-fa-icon" ng-model="$ctrl.defaultState" ng-click="$ctrl.toggleFilter()">\
          <i class="fa fa-square-o"></i>\
          <i class="fa fa-check-square-o"></i>\
          <ng-transclude></ng-transclude>\
        </label>\
      </div>',
    transclude: true,
    bindings: {
      filter: '@',
      defaultState: '='
    },
    replace: true,
    controller: ['$rootScope', function($rootScope) {
      var ctrl = this;
      $rootScope.$on('toggleMapSearch', function() {
        ctrl.defaultState = true;
      });
      ctrl.toggleFilter = function() {
        var toggleState = {
          filter: ctrl.filter,
          state: ctrl.defaultState
        };
        $rootScope.$emit('toggleMapFilter', toggleState);
      };
    }]
  });
})(window.angular);
;//!!
//!! includes/js/angular/makerspaces-map-app/fairesGoogleMap.component.js
(function (angular) {
  'use strict';
  angular.module('makerSpacesApp').component('fairesGoogleMap', {
    bindings: {
      mapId: '@id',
      mapData: '='
    },
    controller: ['$rootScope', 'GMapsInitializer', 'FaireMapsSharedData', '$filter',
      function ($rootScope, GMapsInitializer, FaireMapsSharedData, $filter) {
        var ctrl = this;
        var gMap;
        function initMap(mapId) {
          var customMapType = new google.maps.StyledMapType([{
              stylers: [{
                  hue: '#FFFFFF'
                }, {
                  visibility: 'simplified'
                }, {
                  gamma: 2
                }, {
                  weight: 0.5
                }]
            }, {
              elementType: 'labels',
              stylers: [{
                  visibility: 'on'
                }]
            }, {
              featureType: 'landscape',
              stylers: [{
                  color: '#FFFFFF'
                }]
            },
            {
              featureType: 'all',
              elementType: 'geometry',
              stylers: [{
                  color: '#FFFFFF'
                }, {
                  visibility: 'on'
                }]
            }, {
              featureType: 'water',
              stylers: [{
                  color: '#d0eafc'
                }]
            },
            {
              featureType: 'administrative.country',
              elementType: 'labels',
              stylers: [
                {
                  visibility: 'on'
                }
              ]
            },
            {
              featureType: 'all',
              elementType: 'labels.icon',
              stylers: [{
                  visibility: 'off'
                }]
            },
            {
              featureType: 'poi',
              elementType: 'all',
              stylers: [
                {
                  visibility: 'off'
                }
              ]
            },
            {
              featureType: 'administrative.province',
              elementType: 'labels',
              stylers: [
                {
                  visibility: 'off'
                }
              ]
            },
            {
              featureType: 'administrative.locality',
              elementType: 'all',
              stylers: [
                {
                  visibility: 'on'
                }
              ]
            },
            {
              featureType: 'road',
              elementType: 'all',
              stylers: [
                {
                  visibility: 'off'
                }
              ]
            }

          ], {
            name: 'Custom Style'
          });
          var customMapTypeId = 'custom_style';
          gMap = new google.maps.Map(document.getElementById(mapId), {
            center: {
              lat: 23.9758543, lng: 1.4487502
            },
            disableDefaultUI: true,
            scrollwheel: false,
            zoomControl: true,
            minZoom: 1,
            zoom: 2
          });
          gMap.mapTypes.set(customMapTypeId, customMapType);
          gMap.setMapTypeId(customMapTypeId);
          FaireMapsSharedData.infowindow = new google.maps.InfoWindow({
            content: undefined
          });

          function setMarkers(data) {
            var row;
            var gMarker;
            var gMarkerIcon;
            var gMarkerZIndex;
            for (var i = 0; i < data.length; i++) {
              row = data[i];
              gMarkerIcon = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 5,
                fillOpacity: 1,
                strokeOpacity: 0
              };
              gMarkerZIndex = 1;
              switch (row.mmap_type) {
                case 'Flagship':
                  gMarkerIcon.fillColor = '#D42410';
                  gMarkerIcon.scale = 11;
                  gMarkerZIndex = 2;
                  break;
                case 'Featured':
                  gMarkerIcon.fillColor = '#01A3E0';
                  gMarkerIcon.scale = 8;
                  break;
                case 'School':
                  gMarkerIcon.fillColor = '#7ED321';
                  break;
                default:
                  gMarkerIcon.fillColor = '#67D0F7';
              }
              gMarker = new google.maps.Marker({
                position: {
                  lat: parseFloat(row.mmap_lat),
                  lng: parseFloat(row.mmap_lng)
                },
                icon: gMarkerIcon,
                map: gMap,
                animation: google.maps.Animation.DROP,
                title: row.mmap_eventname,
                description: row.mmap_eventname,
                mmap_type: row.mmap_type,
                zIndex: gMarkerZIndex,
                dataRowSrc: row
              });
              google.maps.event.addListener(gMarker, 'mouseover', displayMarkerInfo);
              //gMarker.dataRowSrc.event_end_dt = new Date(gMarker.dataRowSrc.event_end_dt);
              //gMarker.dataRowSrc.event_start_dt = new Date(gMarker.dataRowSrc.event_start_dt);
              FaireMapsSharedData.gmarkers1.push(gMarker);
            }
            FaireMapsSharedData.setMapDone();
          }

          function displayMarkerInfo() {
            var marker_map = this.getMap();
            FaireMapsSharedData.infowindow.setContent('<div id="content"><h3 class="firstHeading">' +
            this.title + '</h3>' +
            '<div id="bodyContent"><p>' +
            (this.dataRowSrc.mmap_city || '') +
            (this.dataRowSrc.mmap_state && ', ' + this.dataRowSrc.mmap_state || '') +
            (this.dataRowSrc.mmap_country && ', ' + this.dataRowSrc.mmap_country + ' ' || '') +
            (this.dataRowSrc.event_dt || '') +
            '</p><p>' +
            (this.dataRowSrc.mmap_url &&
            '<a href="' + this.dataRowSrc.mmap_url + '" target="_blank">' + this.dataRowSrc.mmap_url + '</a>' || '') +
            '</p></div>' +
            '</div>'
            );
            FaireMapsSharedData.infowindow.open(marker_map, this);
          }
          setMarkers(ctrl.mapData);
        }
        GMapsInitializer.then(function () {
          initMap(ctrl.mapId);
          /*
           if (navigator.geolocation) {
           navigator.geolocation.getCurrentPosition(function(position) {
           gMap.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
           });
           }*/
        });
      }
    ]
  });
})(window.angular);
