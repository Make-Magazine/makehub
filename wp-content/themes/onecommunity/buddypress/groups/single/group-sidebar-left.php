<aside id="left-sidebar" class="sidebar bp-group">
	<button id="left-sidebar-close"></button>

		<?php
				$transient = get_transient( 'onecommunity_sidebar_left_groups' );
				if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_sidebar_left_enable', 0 ) == 1 ) {
				ob_start();
				if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-groups-left')) : ?><?php endif;

				$sidebar_left = ob_get_clean();
					print_r( $sidebar_left );

					if ( get_theme_mod( 'onecommunity_transient_sidebar_left_groups', 0 ) == 1 ) {
						set_transient( 'onecommunity_sidebar_left_groups', $sidebar_left, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_left_groups_expiration', 20 ) );
					}

				} else {
				echo '<!-- Transient onecommunity_sidebar_left_groups ('.get_theme_mod( 'onecommunity_transient_sidebar_left_groups_expiration', 20 ).' min) -->';
					print_r( $transient );
				}
	?>

</aside>