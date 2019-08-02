<?php ?>

<div ng-controller="MapCtrl as $ctrl" class="col-xs-12">
        <nav class="map-filters-wrp">
          <div class="searchbox">
            <h2><?php echo $a['searchtext']?></h2>
            <input type="text"
              class="form-control input-sm"
              placeholder="Location, Name"
              ng-model="$ctrl.filterText"
              ng-model-options="{debounce: 500}"
              ng-change="$ctrl.applyMapFilters()" />
				 <h2><a href="/register">+ add yours</a></h2>
          </div>
          <!-- Commenting out checkboxes till we need them again -->
<!--           <div class="filters ng-cloak" ng-if="$ctrl.faireMarkers">
            <faires-map-filter default-state="false" filter="School">
              School <span class="hidden-sm hidden-xs">Maker Faires</span>
            </faires-map-filter>
            <faires-map-filter default-state="true" filter="Mini">
              Mini <span class="hidden-sm hidden-xs">Maker Faires</span>
            </faires-map-filter>
            <faires-map-filter default-state="true" filter="Featured">
              Featured <span class="hidden-sm hidden-xs">Faires</span>
            </faires-map-filter>
            <faires-map-filter default-state="true" filter="Flagship">
              Flagship <span class="hidden-sm hidden-xs">Faires</span>
            </faires-map-filter>
          </div> -->
        </nav>

        <div class="loading-spinner" ng-if="!$ctrl.faireMarkers">
          <i class="fa fa-circle-o-notch fa-spin"></i>
        </div>
        <!-- Map Angular Component -->
        <faires-google-map
          id="makerspaces-map"
          map-data="::$ctrl.faireMarkers"
          ng-if="$ctrl.faireMarkers">
        </faires-google-map>

        <!-- COmmenting out the filter button till we need them again -->
        <!-- Color Key -->
<!--         <div class="faire-key-boxes">
          <div class="flagship-key" ng-click="$ctrl.toggleBox('Flagship')">
            Flagship Maker Faires
            <p>Faires curated and produced by the Maker Media team</p>
          </div>
          <div class="featured-key" ng-click="$ctrl.toggleBox('Featured')">
            Featured Maker Faires
            <p>Larger-scale regional events</p>
          </div>
          <div class="mini-key" ng-click="$ctrl.toggleBox('Mini')">
            Mini Maker Faires
            <p>Community events</p>
          </div>
          <div class="school-key" ng-click="$ctrl.toggleBox('School')">
            School Maker Faires
            <p>K-12 Faires (closed to general public)</p>
          </div>
        </div> -->

        <!-- List of Faires -->
        <div class="faire-list-table table-responsive">
          <div class="container table table-striped table-condensed">
            <div class="row header" ng-init="sort='event_start_dt';reverse=false">
              <!--<div class="cursor-pointer col-sm-3" ng-click="sort='annual';reverse=!reverse">
                <span ng-show="sort == 'annual'">
                  <span ng-show="!reverse">^</span>
                  <span ng-show="reverse">v</span>
                </span>
              </div>-->
              <div class="cursor-pointer col-sm-4 col-xs-6" ng-click="sort='mmap_eventname';reverse=!reverse">NAME
                <span ng-show="sort == 'mmap_eventname'">
                  <span ng-show="!reverse"><i class="fa fa-chevron-up"></i></span>
                  <span ng-show="reverse"><i class="fa fa-chevron-down"></i></span>
                </span>
              </div>
              <div class="cursor-pointer col-sm-3 col-xs-6" ng-click="sort='mmap_city';reverse=!reverse">LOCATION
					  <span ng-show="sort == 'mmap_city'">
                  <span ng-show="!reverse"><i class="fa fa-chevron-up"></i></span>
                  <span ng-show="reverse"><i class="fa fa-chevron-down"></i></span>
                </span>
				  </div>
              <div class="cursor-pointer col-sm-2 hidden-xs" ng-click="sort='mmap_country';reverse=!reverse">COUNTRY
                <span ng-show="sort == 'mmap_country'">
                  <span ng-show="!reverse"><i class="fa fa-chevron-up"></i></span>
                  <span ng-show="reverse"><i class="fa fa-chevron-down"></i></span>
                </span>
              </div>
					<div class="cursor-pointer col-sm-3 hidden-xs" ng-click="sort='mmap_type';reverse=!reverse">TYPE
                <span ng-show="sort == 'mmap_type'">
                  <span ng-show="!reverse"><i class="fa fa-chevron-up"></i></span>
                  <span ng-show="reverse"><i class="fa fa-chevron-down"></i></span>
                </span>
              </div>
            </div>
            <div class="row" dir-paginate="(index, row) in $ctrl.faireMarkers | orderBy:sort:reverse | itemsPerPage: 20">
              <div class="col-sm-4 col-xs-6">
                <a target="_blank" ng-if="row.mmap_url" href="{{row.mmap_url}}">{{row.mmap_eventname}}</a><div class="hidden-sm hidden-md hidden-lg hidden-xl">{{row.mmap_type}}</div>
                <span ng-if="!row.mmap_url">{{row.mmap_eventname}}
					 </span>
              </div>
              <div class="col-sm-3 col-xs-6">{{row.mmap_city}}{{row.mmap_state && ', '+row.mmap_zip || ''}}
					                              <div class="hidden-sm hidden-md hidden-lg hidden-xl">{{row.mmap_country}}</div>
				  </div>
              <div class="col-sm-2 hidden-xs">{{row.mmap_country}}</div>
				  <div class="col-sm-3 hidden-xs">{{row.mmap_type}}</div>
            </div>
          </div>
          <div class="text-center">
            <dir-pagination-controls
              boundary-links="true"
              template-url="/wp-content/plugins/MakeMap/js/bower_components/angularUtils-pagination/dirPagination.tpl.html">
            </dir-pagination-controls>
          </div>
        </div>
      </div>