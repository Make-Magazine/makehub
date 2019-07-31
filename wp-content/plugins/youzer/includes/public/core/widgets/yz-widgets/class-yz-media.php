<?php

class YZ_Media_Widget {

    /**
     * # Portfolio Widget Arguments.
     */
    function args() {

        // Get Widget Args
        $args = array(
            'menu_order'    => 30,
            'display_title' => true,
            'load_effect'   => 'fadeIn',
            'widget_name'   => 'wall_media',
            'widget_icon'   => 'fas fa-photo-video',
            'widget_title'  => yz_options( 'yz_wg_media_title' ),
        );

        // Filter
        $args = apply_filters( 'yz_portfolio_widget_args', $args );

        return $args;
    }

    /**
     * # Content.
     */
    function widget() {

        // Init Vars.
        $options = '';
        $user_id = bp_displayed_user_id();
        $filters = yz_options( 'yz_wg_media_filters' );

        if ( ! empty( $filters ) ) {

            if ( false !== strpos( $filters, 'photos' ) ) {
                $options .= " photos_number='" . yz_options( 'yz_wg_max_media_photos' ) . "'";
            }

            if ( false !== strpos( $filters, 'videos' ) ) {
                $options .= " videos_number='" . yz_options( 'yz_wg_max_media_videos' ) . "'";
            }

            if ( false !== strpos( $filters, 'audios' ) ) {
                $options .= " audios_number='" . yz_options( 'yz_wg_max_media_audios' ) . "'";
            }

            if ( false !== strpos( $filters, 'files' ) ) {
                $options .= " files_number='" . yz_options( 'yz_wg_max_media_files' ) . "'";
            }

        }
        
        echo do_shortcode( "[youzer_media user_id='$user_id' box='small' filters='$filters' $options]" );

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
                'id'    => 'yz_wg_media_display_title',
                'desc'  => __( 'show widget title', 'youzer' ),
                'type'  => 'checkbox'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'widget title', 'youzer' ),
                'id'    => 'yz_wg_media_title',
                'desc'  => __( 'add widget title', 'youzer' ),
                'type'  => 'text'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'loading effect', 'youzer' ),
                'opts'  => $Yz_Settings->get_field_options( 'loading_effects' ),
                'desc'  => __( 'how you want the widget to be loaded?', 'youzer' ),
                'id'    => 'yz_media_load_effect',
                'type'  => 'select'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'Media filters', 'youzer' ),
                'id'    => 'yz_wg_media_filters',
                'desc'  => __( 'you can change the order of filters or remove some. The allowed filters names are photos, videos, audios, files', 'youzer' ),
                'type'  => 'text'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'media photos number', 'youzer' ),
                'id'    => 'yz_wg_max_media_photos',
                'desc'  => __( 'maximum shown items', 'youzer' ),
                'type'  => 'number'
            )
        );

        $Yz_Settings->get_field(
            array(
                'title' => __( 'media videos number', 'youzer' ),
                'id'    => 'yz_wg_max_media_videos',
                'desc'  => __( 'maximum shown items', 'youzer' ),
                'type'  => 'number'
            )
        );
        $Yz_Settings->get_field(
            array(
                'title' => __( 'media audios number', 'youzer' ),
                'id'    => 'yz_wg_max_media_audios',
                'desc'  => __( 'maximum shown items', 'youzer' ),
                'type'  => 'number'
            )
        );
        $Yz_Settings->get_field(
            array(
                'title' => __( 'media files number', 'youzer' ),
                'id'    => 'yz_wg_max_media_files',
                'desc'  => __( 'maximum shown items', 'youzer' ),
                'type'  => 'number'
            )
        );

        $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );
    }

}