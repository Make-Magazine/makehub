<?php
/**
 * The template for displaying the footer
 */
?>
<div class="clear"></div>
<?php
if ( get_theme_mod( 'onecommunity_sidenav_enable', true ) == true && shortcode_exists( 'onecommunity-sidenav' ) ) {
	 echo do_shortcode( '[onecommunity-sidenav]' );
} else { ?>
	<style type="text/css">
	body { padding-right:0px!important; }
	header#main { padding-right: 0px; }
</style>
<?php } ?>


<?php
wp_reset_query();
?>

<?php    require_once(WP_CONTENT_DIR.'/universal-assets/v2/page-elements/universal-footer.html'); ?>

<?php wp_footer(); ?>

</body>
</html>
