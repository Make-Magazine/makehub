<?php

class Youzer_Member_types {

    public $version;

    public function __construct() {

        // Init Data.
        $this->version = '1.0.0';

        // Load Functions.
        $this->init();

        // Default Options.
        add_filter( 'yz_default_options', array( &$this, 'default_options' ) );

        // Load Text Domain
        add_action( 'plugins_loaded', array( &$this, 'load_youzer_textdomain' ) );
        
        // Load Xprofile Files.
        add_action( 'bp_loaded', array( &$this, 'init_xprofile_field_types' ) );

        // Add Plugin Links.
        add_filter(
            'plugin_action_links_' . YZMT_BASENAME,
            array( $this, 'plugin_action_links' )
        );

        // Add Plugin Links in Multisite..
        add_filter(
            'network_admin_plugin_action_links_' . YZMT_BASENAME,
            array( $this, 'plugin_action_links' )
        );

    }

    /**
     * # Init Youzer Files
     */
    private function init() {

        // Functions.
        require_once YZMT_PUBLIC . 'functions/yz-member-types-functions.php';

    }
        
    /**
     * Include Xprofiles Classes
     */
    function init_xprofile_field_types() {

        if ( ! bp_is_active( 'xprofile' ) ) {
            return;
        }

        // Xprofile Fields.
        require_once YZMT_PUBLIC . 'classes/yz-xprofile-member-types-field.php';
    
    }

    /**
     * # Default Options 
     */
    function default_options( $options ) {

        // Options.
        $member_types_options = array(
            'yz_enable_member_types' => 'off',
            'yz_allow_no_member_type' => 'off',
            'yz_enable_member_types_in_infos' => 'on',
            'yz_enable_member_types_modification' => 'on',
            'yz_enable_member_types_registration' => 'on',
        );
        
        $options = array_merge( $options, $member_types_options );

        return $options;
    }
    
    /**
     * Text Domain
     */
    function load_youzer_textdomain() {
        load_plugin_textdomain( 'youzer-member-types', FALSE, dirname( YZMT_BASENAME ) . '/languages' );
    }

    /**
     * Action Links
     */
    function plugin_action_links( $links ) {
        // Get Youzer Plugin Pages. 
        $panel_url = esc_url( add_query_arg( array( 'page' => 'youzer-panel&tab=member-types' ), admin_url( 'admin.php' ) ) );
        $plugin_url = 'https://www.kainelabs.com/downloads/member-types/';
        $documentation_url = 'https://kainelabs.ticksy.com/articles/100012863';
        // Add a few links to the existing links array.
        return array_merge( $links, array(
            'settings' => '<a href="' . $panel_url . '">' . esc_html__( 'Settings', 'youzer-member-types' ) . '</a>',
            'documentation' => '<a href="' . $documentation_url . '">' . esc_html__( 'Documentation', 'youzer-member-types' ) . '</a>',
            'about'    => '<a href="' . $plugin_url . '">' . esc_html__( 'About',    'youzer-member-types' ) . '</a>'
        ) );
    }

}