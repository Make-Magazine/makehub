<?php
/**
 * View: Filter Type Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2/components/filter-type.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @var array $data Data of filter type, should contain `type` key.
 *
 * @version TBD
 *
 */

if ( empty( $data['type'] ) ) {
	return;
}

switch ( $data['type'] ) {
	case 'checkbox':
		$this->template( 'components/checkbox', $data );
		break;
	case 'dropdown':
		$this->template( 'components/dropdown', $data );
		break;
	case 'multiselect':
		$this->template( 'components/multiselect', $data );
		break;
	case 'radio':
		$this->template( 'components/radio', $data );
		break;
	case 'range':
		$this->template( 'components/range', $data );
		break;
	default:
		break;
}
