<?php
/*
Template Name: Frontpage Page Builder
*/
?>

<?php get_header() ?>

<div id="pagebuilder-container">

<?php
while ( have_posts() ) : the_post(); 
	the_content();
endwhile; 
?>

</div>


<?php
if ( get_theme_mod( 'onecommunity_front_shapes', true ) == true ) { ?>

<script type="text/javascript">
/*** Put design shapes on the frontpage header (Page Builder) ***/
if(document.querySelector(".animated-shapes-container")){
window.addEventListener("DOMContentLoaded", function(event) {
"use strict";
var id = document.querySelector('.animated-shapes-container');
var htmlString = '<div class="shape-1"></div><div class="shape-2"></div><div class="shape-3"></div><div class="shape-4"></div><div class="shape-5"></div><div class="shape-6"></div>'
id.insertAdjacentHTML('beforeend', htmlString);
});
}
</script>

<?php } ?>

<?php get_footer() ?>