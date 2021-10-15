<?php

// Remove Empty Tabs
add_filter( 'woocommerce_product_tabs', 'woo_remove_empty_tabs', 20, 1 );
function woo_remove_empty_tabs( $tabs ) {
	if ( ! empty( $tabs ) ) {
		foreach ( $tabs as $title => $tab ) {
			if ( empty( $tab['content'] ) ) {
				unset( $tabs[ $title ] );
			}
		}
	}
	return $tabs;
}
