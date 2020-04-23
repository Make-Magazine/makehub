<?php

/***
 * Media Tab.
 */
class YZ_Media_Tab {

    /**
     * Constructor
     */
    function __construct() {

        add_action( 'bp_enqueue_scripts', array( $this, 'scripts' ) );
        
        // Add Account Settings Pages.
        $this->groups_media_tab();
        
        // Add Account Settings Pages.
        $this->add_sub_tabs();

    }

    /**
     * Group SubTabs
     */
    function group_media_tabs( $group = false ) {

        bp_get_options_nav( yz_group_media_slug() );

    }

    /**
     * Groups Media Tab
     */
    function groups_media_tab() {

        // Check if its a group page.
        if ( ! bp_is_groups_component() || ! bp_is_single_item() || 'off' == yz_option( 'yz_enable_groups_media', 'on' ) ) {
            return false;
        }

        global $bp;

        $group = $bp->groups->current_group;
        
        // Call Media Scripts
        $this->scripts();

        // Get Media Slug.        
        $media_slug = yz_group_media_slug();

        // Add Group 'Media' Nav.
        bp_core_new_subnav_item(
            array(
                'slug' => $media_slug,
                'parent_slug' => $group->slug,
                'name' => __( 'Media', 'youzer' ),
                'parent_url' => bp_get_group_permalink( $group ),
                'screen_function' => array( $this, 'groups_media_screen' ),
                'default_subnav_slug' => 'all',
                'position' => 12
            ), 'groups'
        );

        if ( bp_is_current_action( $media_slug ) ) {

            // Add Media Sub Pages.
            foreach ( $this->sub_tabs() as $page ) {

                if ( $page['slug'] != 'all' && 'on' != yz_option( 'yz_show_group_media_tab_' . $page['slug'], 'on' ) ) {
                    continue;
                }

                bp_core_new_subnav_item( array(
                        'slug' => $page['slug'],
                        'name' => $page['title'],
                        'parent_slug' => $media_slug,
                        'item_css_id' => 'media-' . $page['slug'],
                        'parent_url' => bp_get_group_permalink( $group )  . "$media_slug/",
                        'screen_function' => array( $this, 'groups_media_screen' ),
                    ), 'groups'
                );
            }

        }
    
    }

    /**
     * Get Group Tab.
     */
    function groups_media_screen() {

        // Call Media Tab Content.
        add_action( 'bp_template_content', array( $this, 'group_tab' ) );

        // Load Tab Template
        bp_core_load_template( 'buddypress/groups/single/plugins' );

    }

    /**
     * Group Tab.
     */
    function group_tab() {

        $current_tab = bp_action_variable();

        if ( empty( $current_tab ) ) {
            $layout = yz_option( 'yz_group_media_tab_layout', '4columns' );
            $limit = yz_option( 'yz_group_media_tab_per_page', 8 );
        } else {
            $layout = yz_option( 'yz_group_media_subtab_layout', '3columns' );
            $limit = yz_option( 'yz_group_media_subtab_per_page', 24 );
        }

        $args = array( 'group_id' => bp_get_current_group_id(), 'layout' => $layout, 'limit' => $limit, 'pagination' => true );

        ?>
        
        <div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Group secondary navigation', 'youzer' ); ?>" role="navigation">
            <ul><?php $this->group_media_tabs(); ?></ul>
        </div>

        <div class="yz-tab yz-media yz-media-<?php echo $args['layout']; ?>">
        
        <?php
        
        switch ( $current_tab ) {
            case 'photos':
                $this->get_photos( $args );
                break;
            case 'videos':
                $this->get_videos( $args );
                break;            
            case 'audios':
                $this->get_audios( $args );
                break;  
            case 'files':
                $this->get_files( $args );
                break;

            default:
                
                // Delete Pagination.
                unset( $args['pagination'] );

                if ( 'on' == yz_option( 'yz_show_group_media_tab_photos', 'on' ) ) $this->get_photos( $args ); 
                if ( 'on' == yz_option( 'yz_show_group_media_tab_videos', 'on' ) ) $this->get_videos( $args ); 
                if ( 'on' == yz_option( 'yz_show_group_media_tab_audios', 'on' ) ) $this->get_audios( $args ); 
                if ( 'on' == yz_option( 'yz_show_group_media_tab_files', 'on' ) ) $this->get_files( $args ); 

                break;
        }
        
        ?>

        </div>

        <?php
    }

    /**
     * Add Sub Tabs.
     */
    function add_sub_tabs() {

        // Get Media Slug.        
        $media_slug = yz_profile_media_slug();

        if ( ! bp_is_current_component( $media_slug ) ) {
            return;
        }

        // Call Media Scripts
        $this->scripts();

        // Add Media Sub Pages.
        foreach ( $this->sub_tabs() as $page ) {

            if (  $page['slug'] != 'all' && 'on' != yz_option( 'yz_show_profile_media_tab_' . $page['slug'], 'on' ) ) {
                continue;
            }

            bp_core_new_subnav_item( array(
                    'slug' => $page['slug'],
                    'name' => $page['title'],
                    'parent_slug' => $media_slug,
                    'parent_url' => bp_displayed_user_domain() . "$media_slug/",
                    'screen_function' => array( $this, 'get_profile_media_sub_tabs' ),
                )
            );
        }
    }

    /**
     * # Tab.
     */
    function tab() {

        $args = array(
            'user_id' => bp_displayed_user_id(),
            'limit' => yz_option( 'yz_profile_media_tab_per_page', 8 ),
            'layout' => yz_option( 'yz_profile_media_tab_layout', '4columns' )
        );

        ?>
        
        <div class="yz-tab yz-media yz-media-<?php echo $args['layout']; ?>">
            <?php if ( 'on' == yz_option( 'yz_show_profile_media_tab_photos', 'on' ) ) $this->get_photos( $args ); ?>
            <?php if ( 'on' == yz_option( 'yz_show_profile_media_tab_videos', 'on' ) ) $this->get_videos( $args ); ?>
            <?php if ( 'on' == yz_option( 'yz_show_profile_media_tab_audios', 'on' ) ) $this->get_audios( $args ); ?>
            <?php if ( 'on' == yz_option( 'yz_show_profile_media_tab_files', 'on' ) ) $this->get_files( $args ); ?>
        </div>

        <?php
    }

    /**
     * Subtab Content.
     */
    function subtab_content() {
        
        // Get Args Data.
        $args = array(
            'pagination' => true,
            'user_id' => bp_displayed_user_id(),
            'layout' => yz_option( 'yz_profile_media_subtab_layout', '3columns' ),
            'limit' => yz_option( 'yz_profile_media_subtab_per_page', 24 )
        );

        echo "<div class='yz-tab yz-media yz-media-{$args['layout']}'>";

        switch ( bp_current_action() ) {
            case 'photos':
                $this->get_photos( $args );
                break;
            case 'videos':
                $this->get_videos( $args );
                break;            
            case 'audios':
                $this->get_audios( $args );
                break;  
            case 'files':
                $this->get_files( $args );
                break;
        }
        
        echo '</div>';

    }


    /**
     * Add Media Sub Pages
     **/
    function sub_tabs() {

        $sub_tabs = array(
            'all' => array(
                'title' => __( 'All', 'youzer' ),
                'slug' => 'all'
            ),
            'photos' => array(
                'title' => __( 'Photos', 'youzer' ),
                'slug' => 'photos'
            ),
            'videos' => array(
                'title' => __( 'Videos', 'youzer' ),
                'slug' => 'videos'
            ),
            'audios' => array(
                'title' => __( 'Audios', 'youzer' ),
                'slug' => 'audios'
            ),
            'files' => array(
                'title' => __( 'Files', 'youzer' ),
                'slug' => 'files'
            )
        );

        return apply_filters( 'yz_profile_media_subtabs', $sub_tabs );

    }

    /**
     * Get Photos
     **/
    function get_photos( $args = null ) {

        ?>

        <div class="yz-media-group yz-media-group-photos">
            
            <div class="yz-media-group-head">
                <div class="yz-media-head-left">
                    <div class="yz-media-group-icon"><i class="fas fa-image"></i></div>
                    <div class="yz-media-group-title"><?php _e( 'photos', 'youzer' ); ?></div>
                </div>
                <div class="yz-media-head-right">
                    <?php if ( bp_current_action() != 'photos' ) : ?>
                    <a href="<?php echo yz_media()->get_media_by_type_slug( $args ) . '/photos'; ?>" class="yz-media-group-view-all"><?php _e( 'view all', 'youzer' ); ?></a> 
                    <?php endif; ?>             
                </div>
            </div>

            <div class="yz-media-group-content">
                <div class="yz-media-items">
                    <?php yz_media()->get_photos_items( $args ); ?>
                </div>
            </div>

        </div>
        <?php
    }

    /**
     * Get Videos
     **/
    function get_videos( $args = null ) {

        ?>

        <div class="yz-media-group yz-media-group-videos">
            
            <div class="yz-media-group-head">
                <div class="yz-media-head-left">
                    <div class="yz-media-group-icon"><i class="fas fa-film"></i></div>
                    <div class="yz-media-group-title"><?php _e( 'Videos', 'youzer' ); ?></div>
                </div>
                <div class="yz-media-head-right">
                    <?php if ( bp_current_action() != 'videos' ) : ?>
                    <a href="<?php echo yz_media()->get_media_by_type_slug( $args ). '/videos'; ?>" class="yz-media-group-view-all"><?php _e( 'view all', 'youzer' ); ?></a>
                    <?php endif; ?>             
                </div>
            </div>

            <div class="yz-media-group-content">
                <div class="yz-media-items">
                    <?php yz_media()->get_videos_items( $args ); ?>
                </div>
            </div>

        </div>

        <?php
    }

    /**
     * Get Audios
     **/
    function get_audios( $args = null ) {

        ?>

        <div class="yz-media-group yz-media-group-audios">
            
            <div class="yz-media-group-head">
                <div class="yz-media-head-left">
                    <div class="yz-media-group-icon"><i class="fas fa-volume-up"></i></div>
                    <div class="yz-media-group-title"><?php _e( 'Audios', 'youzer' ); ?></div>
                </div>
                <div class="yz-media-head-right">

                    <?php if ( bp_current_action() != 'audios' ) : ?>
                    <a href="<?php echo yz_media()->get_media_by_type_slug( $args ) . '/audios'; ?>" class="yz-media-group-view-all"><?php _e( 'view all', 'youzer' ); ?></a>
                    <?php endif; ?>             
                </div>
            </div>

            <div class="yz-media-group-content">
                <div class="yz-media-items">
                    <?php yz_media()->get_audios_items( $args ); ?>
                </div>
            </div>

        </div>

        <?php
    }

    /**
     * Get Files
     **/
    function get_files( $args = null ) {

        ?>

        <div class="yz-media-group yz-media-group-files">
            
            <div class="yz-media-group-head">
                <div class="yz-media-head-left">
                    <div class="yz-media-group-icon"><i class="fas fa-file-import"></i></div>
                    <div class="yz-media-group-title"><?php _e( 'Files', 'youzer' ); ?></div>
                </div>
                <div class="yz-media-head-right">

                    <?php if ( bp_current_action() != 'files' ) : ?>
                    <a href="<?php echo yz_media()->get_media_by_type_slug( $args ) . '/files'; ?>" class="yz-media-group-view-all"><?php _e( 'view all', 'youzer' ); ?></a>
                    <?php endif; ?>             
                </div>
            </div>

            <div class="yz-media-group-content">
                <div class="yz-media-items">
                    <?php yz_media()->get_files_items( $args ); ?>
                </div>
            </div>

        </div>

        <?php
    }

    /**
     * Scripts
     */
    function scripts() {
        yz_media()->scripts();
    }
}