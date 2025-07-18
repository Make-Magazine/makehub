<?php
/**
 * BuddyPress - Users Settings
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>

<div class="item-list-tabs no-ajax settings-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
	<ul>
		<?php if ( bp_core_can_edit_settings() ) : ?>

			<?php bp_get_options_nav(); ?>

		<?php endif; ?>
	</ul>
</div>

<?php

switch ( bp_current_action() ) :
	case 'notifications'  :
		bp_get_template_part( 'members/single/settings/notifications'  );
		break;
	case 'capabilities'   :
		bp_get_template_part( 'members/single/settings/capabilities'   );
		break;
	case 'delete-account' :
		bp_get_template_part( 'members/single/settings/delete-account' );
		break;
	case 'general'        :
		bp_get_template_part( 'members/single/settings/general'        );
		break;
	case 'profile'        :
		bp_get_template_part( 'members/single/settings/profile'        );
		break;
	case 'invites':
		bp_get_template_part( 'members/single/settings/group-invites' );
		break;
	case 'data'           :
		bp_get_template_part( 'members/single/settings/data'           );
		break;
	default:
		bp_get_template_part( 'members/single/plugins'                 );
		break;
endswitch;
