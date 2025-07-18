<?php
/*
Template Name: Frontpage 4
*/
?>

<?php get_header() ?>

<div id="frontpage-template-4">

<!-- ******************************************************************************************************
***********************************************1st row starts**********************************************
****************************************************************************************************** -->

<?php echo do_shortcode('[onecommunity-swiper-one tag="" limit="5"]'); ?>


<!-- ******************************************************************************************************
*************************************************1st row ends**********************************************
****************************************************************************************************** -->

<!-- ******************************************************************************************************
***********************************************2nd row starts**********************************************
****************************************************************************************************** -->

<div style="padding: 40px 0; background: var(--dd-background-1); padding-left:10px; text-aligin:center;">
  <h4 style="text-aligin:center; display:inline-block; margin-bottom:20px;">Featured</h4>
  <?php echo do_shortcode('[onecommunity-swiper-two tag="Featured" limit="7"]'); ?>
</div>

<!-- ******************************************************************************************************
***********************************************2nd row eds**********************************************
****************************************************************************************************** -->

<?php echo do_shortcode('[onecommunity-swiper-three tag="" limit="7"]'); ?>

<br><br>

</div><!-- #frontpage-template-4 -->

<?php get_footer() ?>