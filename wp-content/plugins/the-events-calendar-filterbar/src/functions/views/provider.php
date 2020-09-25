<?php
use Tribe\Events\Views\V2\Manager;

/* @todo Remove this and replace all calls to it with tribe_events_views_v2_is_enabled() */

/**
 * Checks whether V2 of the Filter Bar Views is enabled or not.
 *
 * In order the function will check the `TRIBE_EVENTS_FILTERBAR_V2_VIEWS` constant,
 * the `TRIBE_EVENTS_FILTERBAR_V2_VIEWS` environment variable.
 *
 * @since TBD
 *
 * @return bool Whether V2 of the Views are enabled or not.
 */
function tribe_events_filterbar_views_v2_is_enabled() {
	if ( ! tribe_events_views_v2_is_enabled() ) {
		return false;
	}

	return (bool) defined( 'TRIBE_EVENTS_FILTERBAR_V2_VIEWS' ) && TRIBE_EVENTS_FILTERBAR_V2_VIEWS;
}
