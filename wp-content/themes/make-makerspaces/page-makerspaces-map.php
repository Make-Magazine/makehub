<?php
/**
 * Template Name: Makerspaces Map
 *
 * @package _makerspaces
 */
//* Force full width content layout
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
// Add our custom loop
add_action('genesis_loop', 'spaces_map_loop');

function spaces_map_loop() {
    // Remove posts.
    remove_action( 'Genesis_loop', 'Genesis_do_loop' ); ?>

   <div class="container-fluid inner-container directory-container" id="directory">

      <div class="row map-header">
         <div class="col-md-12">
            <h1>Makerspace Directory</h1>
            <!--
            <div class="admin-buttons">
                <a class="btn universal-btn" href="/register">Add yours <i class="fas fa-plus"></i></a>
		<a class="btn universal-btn" href="/edit-your-makerspace">Manage <i class="far fa-edit"></i></a>
            </div>-->
            <p><?php //echo the_content(); ?></p>
         </div>
      </div>
      <div class="message-container">
         <div class="loading-indicator" ref="loadingIndicator">Loading... <i class="fas fa-spinner"></i></div>
         <div class="error-indicator hidden text-danger" ref="errorIndicator">Sorry! We couldn't load the map... please try again later. <i class="fas fa-exclamation-triangle"></i></div>
      </div>
      <div class="map-table-hidden" ref="mapTableWrapper" >

         <div class="row">
            <div class="col-xs-12">
               <div id="map" ref="map" style="height: 40px;"></div>
            </div>
         </div>

         <div class="row">
            <div class="col-xs-12">

               <div class="map-filters-wrp">
                  <form action="" class="" @submit="filterOverride">
                     <div class="">
                        <label for="filter">Find a Makerspace</label>
                        <input class="form-control input-sm" type="search" id="filter" name="filter" ref="filterField" v-model="filterVal" @input="doFilter" placeholder="Search by Name">
                     </div>
                  </form>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-xs-12">
               <v-client-table :data="tableData" :columns="columns" :options="options" @row-click="onRowClick" ref="directoryGrid">
                  <span slot="mmap_eventname" slot-scope="props">
                     <a :href="props.row.mmap_url" target="_blank" title="Visit site in new window">{{ props.row.mmap_eventname }}</a>
                  </span>
               </v-client-table>
            </div>
         </div>

      </div>  <!-- end map-table-wrapper -->

   </div>

<div class="container-fluid light-blue">
   <div class="container">
      <div class="row">
         <div class="col-md-4 col-sm-4 col-xs-12 makerspace-bottom-nav">
            <h4>Join our global network of makerspaces</h4>
            <a class="btn universal-btn" href="/register">Add your makerspace</a>
         </div>
         <div class="col-md-4 col-sm-4 col-xs-12 makerspace-bottom-nav">
            <h4>See an error or need to update your info?</h4>
            <a class="btn universal-btn" href="/edit-your-makerspace">Manage your listing</a>					
         </div>
			<div class="col-md-4 col-sm-4 col-xs-12 makerspace-bottom-nav">
				<h4>Join Make: Community</h4>
				<a href="https://community.make.co"><button class="btn blue-btn">Learn More</button></a>
			</div>
      </div>
   </div>
</div>  

<?php echo do_shortcode('[make_rss title="Makerspace", feed="https://makezine.com/tag/makerspaces/feed/", moreLink="http://makezine.com/tag/makerspaces/", number=4]'); ?>

<?php
} //end function

Genesis();