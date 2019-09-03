<?php

class YZ_Groups_Widget {

    /**
     * # Groups Widget Arguments.
     */
    function args() {

        // Get Widget Args
        $args = array(
            'widget_icon'   => 'fas fa-users',
            'widget_name'   => 'groups',
            'widget_title'  => yz_options( 'yz_wg_groups_title' ),
            'load_effect'   => yz_options( 'yz_groups_load_effect' ),
            'display_title' => yz_options( 'yz_wg_groups_display_title' )
        );

        // Filter
        $args = apply_filters( 'yz_groups_widget_args', $args );

        return $args;
    }
    
    /**
     * # Content.
     */
    function widget() {

        if ( ! bp_is_active( 'groups' ) ) {
            return;
        }
        
        global $groups_template;

        // Back up the global.
        $old_groups_template = $groups_template;


        global $Youzer;

        // Get User Max Groups Number to show in the widget.
        $max_groups = yz_options( 'yz_wg_max_groups_items' );
        
        $group_args = array(
            'user_id'         => bp_displayed_user_id(),
            'per_page'        => $max_groups,
            'max'             => $max_groups
        );

        if ( bp_has_groups( $group_args ) ) :

        // Get Groups Number.
        $groups_nbr = bp_get_group_total_for_member();

        // Get User Data
        $avatar_border_style = yz_options( 'yz_wg_groups_avatar_img_format' );

        // Get Widget Class.
        $list_class = array( 'yz-items-list-widget', 'yz-profile-list-widget', 'yz-profile-groups-widget' );

        // Add Widgets Avatars Border Style Class.
        $list_class[] = 'yz-list-avatar-' . $avatar_border_style; ?>
        
        <div class="<?php echo yz_generate_class( $list_class ); ?>">

            <?php while ( bp_groups() ) : bp_the_group(); ?>

                <div class="yz-list-item">

                    <a href="<?php bp_group_permalink(); ?>" class="yz-item-avatar"><?php bp_group_avatar_thumb(); ?></a>

                    <div class="yz-item-data">
                        <a href="<?php bp_group_permalink(); ?>" class="yz-item-name"><?php bp_group_name() ?></a>
                        <div class="yz-item-meta">
                            <div class="yz-meta-item"><?php $Youzer->group->status( $groups_template->group ); ?></div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

                <?php if ( $groups_nbr > $max_groups ) : ?>
                    <div class="yz-more-items">
                        <a href="<?php echo bp_core_get_user_domain( bp_displayed_user_id() ) . bp_get_groups_slug();?>"><?php echo sprintf( __( 'Show All Groups ( %s )', 'youzer' ), $groups_nbr ); ?></a>
                    </div>
                <?php endif; ?>

        </div>

        <?php

        else: return;

        endif;

        // Back up the global.
        $groups_template = $old_groups_template;

    }

    /**
     * # Admin Settings.
     */
    function admin_settings() {

        global $Yz_Settings;

        $Yz_Settings->get_field(
            array(
                'title' => __( 'general Settings', 'youzer' ),
                'type'  => 'openBox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'display title', 'youzer' ),
                'id'    => 'yz_wg_groups_display_title',
                'desc'  => __( 'show widget title', 'youzer' ),
                'type'  => 'checkbox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'widget title', 'youzer' ),
                'id'    => 'yz_wg_groups_title',
                'desc'  => __( 'add widget title', 'youzer' ),
                'type'  => 'text'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'loading effect', 'youzer' ),
                'opts'  => $Yz_Settings->get_field_options( 'loading_effects' ),
                'desc'  => __( 'how you want the widget to be loaded ?', 'youzer' ),
                'id'    => 'yz_groups_load_effect',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'allowed groups number', 'youzer' ),
                'id'    => 'yz_wg_max_groups_items',
                'desc'  => __( 'maximum number of groups to display', 'youzer' ),
                'type'  => 'number'
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Groups Avatar border style', 'youzer' ),
                'type'  => 'openBox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'id'    => 'yz_wg_groups_avatar_img_format',
                'type'  => 'imgSelect',
                'opts'  => $Yz_Settings->get_field_options( 'image_formats' )
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );
    }

}