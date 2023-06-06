<aside id="sidebar" class="sidebar">

	<?php
	if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-groups')) : endif;

	$transient = get_transient( 'onecommunity_sidebar_groups_cached' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_sidebar_groups_cached_enable', 0 ) == 1 ) {
		ob_start();

			if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-groups-cached')) : endif;

		$sidebar = ob_get_clean();
		print_r( $sidebar );

		if ( get_theme_mod( 'onecommunity_sidebar_groups_cached_enable', 0 ) == 1 ) {
			set_transient( 'onecommunity_sidebar_groups_cached', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_sidebar_groups_cached_expiration', 20 ) );
		}

	} else {
		echo '<!-- Transient onecommunity_sidebar_groups_cached ('.get_theme_mod( 'onecommunity_sidebar_groups_cached_expiration', 20 ).' min) -->';
		print_r( $transient );
	}

	?>

</aside><!--sidebar ends-->

<div id="sidebar-spacer"></div>