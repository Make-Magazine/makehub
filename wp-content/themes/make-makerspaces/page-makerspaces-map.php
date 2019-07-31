<?php
/**
 * Template Name: Makerspaces Map
 *
 * @package _makerspaces
 */

get_header(); ?>

   <div class="container directory-container" id="directory">

      <div class="row map-header">
         <div class="col-md-12">
            <h1>Makerspace Directory</h1>
            <div class="admin-buttons">
               <a class="btn btn-blue" href="/register">Add yours <i class="fa fa-plus"></i></a>
					<a class="btn btn-blue" href="/edit-your-makerspace">Manage <i class="fa fa-pencil-square-o"></i></a>
            </div>
            <p><?php //echo the_content(); ?></p>
         </div>
      </div>
      <div class="message-container">
         <div class="loading-indicator" ref="loadingIndicator">Loading... <i class="fa fa-spinner"></i></div>
         <div class="error-indicator hidden text-danger" ref="errorIndicator">Sorry! We couldn't load the map... please try again later. <i class="fa fa-exclamation-triangle"></i></div>
      </div>
      <div class="map-table-hidden" ref="mapTableWrapper" >

         <div class="row">
            <div class="col-md-12">
               <div id="map" ref="map" style="height: 40px;"></div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-12">

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
            <div class="col-md-12">
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
         <div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
            <h4>Join our global network of makerspaces</h4>
            <a class="btn btn-blue" href="/register">Add your makerspace</a>
         </div>
         <div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
            <h4>See an error or need to update your info?</h4>
            <a class="btn btn-blue" href="/edit-your-makerspace">Manage your listing</a>					
         </div>
         <div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
            <h4>Get a free PDF guide on starting a makerspace</h4>
            <a class="btn btn-blue" href="/playbook">Download the playbook</a>
         </div>
         <div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
            <h4>Start your own makerspace today</h4>
            <a class="btn btn-blue" href="https://learn.make.co/courses/starting-a-school-makerspace/info">Take the course</a>
         </div>
      </div>
   </div>
</div>  


<div class="container makerspace-news">
   <div class="row posts-feeds-wrapper">
      <h2>Makerspace News from <img class="logo" src="https://make.co/wp-content/themes/memberships/img/make_logo.svg" /> magazine</h2>

      <?php
      $rss = fetch_feed('https://makezine.com/tag/makerspaces/feed/');
      if (!is_wp_error($rss)) :
         $maxitems = $rss -> get_item_quantity(4); //gets latest 5 items, this can be changed to suit your requirements
         $rss_items = $rss -> get_items(0, $maxitems);
      endif;

      //grabs our post thumbnail image
      function get_first_image_url($html) {
         if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
            return $matches[1];
         }
      }
      
      // get just the text
      function get_summary($html) {
         $summary = preg_replace('/<a[^>]*>([\s\S]*?)<\/a[^>]*>/', '', $html);
         $summary = strip_tags(str_replace('The post appeared first on .', '', $summary));
         return $summary;
      }

      //shortens description
      function shorten($string, $length) {
         $suffix = '&hellip;';
         $short_desc = trim(str_replace(array("\r", "\n", "\t"), ' ', strip_tags($string)));
         $desc = trim(substr($short_desc, 0, $length));
         $lastchar = substr($desc, -1, 1);
         if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?')
         $suffix = '';
         $desc .= $suffix;
         return $desc;
      }
      ?>
      <?php
      if ($maxitems == 0) echo '<li>No items.</li>';
      else foreach ( $rss_items as $item ) :
      ?>
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
         <div class="post-feed">
            <a class="full-link" href="<?php echo esc_url($item -> get_permalink()); ?>" target="_blank">
               <div class="title">
                  <img src="<?php echo get_first_image_url($item -> get_content()); ?>" alt="Makerspace post featured image">
                  <p class="p-title"><?php echo esc_html($item -> get_title()); ?></p>
                  <p><?php echo shorten(get_summary($item -> get_content()), 100); ?></p>
               </div>
            </a>
         </div>
      </div>
      <?php endforeach; ?>
      
      <div class="col-xs-12">
         <a class="btn btn-blue btn-more-articles" href="http://makezine.com/tag/makerspaces/" target="_blank">See more articles</a>
      </div>
   </div>

</div>

<?php get_footer(); ?>
