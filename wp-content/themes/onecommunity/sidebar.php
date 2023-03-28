<aside id="sidebar" class="sidebar">

	<?php 
	$transient = get_transient( 'onecommunity_sidebar_pages' );
	if ( $transient === false || !get_theme_mod( 'onecommunity_transient_sidebar_pages_enable', 0 ) == 1 ) {		
	ob_start();

	if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-pages')) : endif;

	$sidebar = ob_get_clean();

	if ( get_theme_mod( 'onecommunity_transient_sidebar_pages_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_sidebar_pages', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_pages_expiration', 20 ) );
	}

	print_r( $sidebar );

	} else {
		echo '<!-- Transient onecommunity_sidebar_pages ('.get_theme_mod( 'onecommunity_transient_sidebar_pages_expiration', 20 ).' min) -->';
		print_r( $transient );
	}
	?>

</aside><!--sidebar ends-->