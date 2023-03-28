<aside id="sidebar" class="sidebar">

	<?php 
	if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-profiles')) : endif;

	$transient = get_transient( 'onecommunity_sidebar_profiles_cached' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_sidebar_profiles_cached_enable', 0 ) == 1 ) {
		ob_start();

			if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-profiles-cached')) : endif;

		$sidebar = ob_get_clean();
		print_r( $sidebar );

			if ( get_theme_mod( 'onecommunity_sidebar_profiles_cached_enable', 0 ) == 1 ) {
				set_transient( 'onecommunity_sidebar_profiles_cached', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_sidebar_profiles_cached_expiration', 20 ) );
			}

	} else {
		echo '<!-- Transient onecommunity_sidebar_profiles_cached ('.get_theme_mod( 'onecommunity_sidebar_profiles_cached_expiration', 20 ).' min) -->';
		print_r( $transient );
	}
	?>

</aside><!--sidebar ends-->

<div id="sidebar-spacer"></div>