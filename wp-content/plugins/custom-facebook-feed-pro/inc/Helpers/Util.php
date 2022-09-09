<?php

namespace CustomFacebookFeed\Helpers;

class Util {
	public static function isFBPage() {
		return get_current_screen() !== null && ! empty( $_GET['page'] ) && strpos( $_GET['page'], 'cff-' ) !== false;
	}

	public static function currentPageIs( $page ) {
		$current_screen = get_current_screen();
		return $current_screen !== null && ! empty( $current_screen ) && strpos( $current_screen->id, $page ) !== false;
	}
}
