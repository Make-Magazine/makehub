<?php

/**
 * @package WordPress
 * @subpackage BuddyBoss BPLA
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BuddyBoss_BPLA_BP_Component' ) ):

	/**
	 *
	 * BuddyBoss BPLA BuddyPress Component
	 * ***********************************
	 */
	class BuddyBoss_BPLA_BP_Component extends BP_Component {

		/**
		 * INITIALIZE CLASS
		 *
		 * @since BuddyBoss BPLA 1.0
		 */
		public function __construct() {
			parent::start(
                                'bpla', __( 'BPLA', 'bp-location-autocomplete' ), dirname( __FILE__ )
			);
		}

		/**
		 * Convenince method for getting main plugin options.
		 *
		 * @since BuddyBoss BPLA (1.0.0)
		 */
		public function option( $key ) {
			return bp_bpla()->option( $key );
		}

		/**
		 * SETUP BUDDYPRESS GLOBAL OPTIONS
		 *
		 * @since	BuddyBoss BPLA 1.0
		 */
		public function setup_globals( $args = array() ) {
			
		}

		/**
		 * SETUP ACTIONS
		 *
		 * @since  BuddyBoss BPLA 1.0
		 */
		public function setup_actions() {
			// Add body class
			add_filter( 'body_class', array( $this, 'body_class' ) );

			// Front End Assets
			if ( ! is_admin() && ! is_network_admin() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
			}
			
			parent::setup_actions();
		}

		/**
		 * Add active BPLA class
		 *
		 * @since BuddyBoss BPLA (0.1.1)
		 */
		public function body_class( $classes ) {
			$classes[] = apply_filters( 'bp_bpla_body_class', 'bp-location-autocomplete' );
			return $classes;
		}

		/**
		 * Load CSS/JS
		 * @return void
		 */
		public function assets() {
                    
                    //For Profile
                    if ( 'profile' == bp_current_component() || 'register' == bp_current_component() ) {
                        
                        if ( 'yes' != bp_bpla()->option('enable-for-profiles') ) {
                            return;
                        }
                        
			//wp_enqueue_script( 'bp-location-autocomplete-main-js', bp_bpla()->assets_url . '/js/bp-location-autocomplete.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'bp-location-autocomplete-main-js', bp_bpla()->assets_url . '/js/bp-location-autocomplete.min.js', array( 'jquery' ), '1.0.0', true );
                        
                        $data = array();
                        $address_field = $this->option( 'location-field-address-selection' );
                        
                        if ( $address_field && 'single' == $address_field ) {
                            $data = array(
                                'address' => $this->option('location-field-address')
                            );
                        } else {
                            $data = array(
                                'street' => $this->option('location-field-street'),
                                'city' => $this->option('location-field-city'),
                                'state' => $this->option('location-field-state'),
                                'country' => $this->option('location-field-country'),
                                'zipcode' => $this->option('location-field-zipcode')
                            );
                        }
                        
                        wp_localize_script( 'bp-location-autocomplete-main-js', 'BPLA_data', $data );
                        
                        $location_api_key = $this->option('location-api-key');
                        wp_enqueue_script( 'bpla-googlemap', 'https://maps.googleapis.com/maps/api/js?key='.esc_attr($location_api_key).'&libraries=places&callback=bp_location_profile.initMap', array( 'jquery' ), '', true);
                        
                    }
                    
                    //For Groups
                    if ( 'groups' == bp_current_component() ) {
                        
                        if ( 'yes' != bp_bpla()->option('enable-for-groups') ) {
                            return;
                        }

                        //wp_enqueue_script( 'bp-location-autocomplete-main-js', bp_bpla()->assets_url . '/js/bp-group-autocomplete.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'bp-group-autocomplete-main-js', bp_bpla()->assets_url . '/js/bp-group-autocomplete.min.js', array( 'jquery' ), '1.0.0', true );
                     
                        $location_api_key = $this->option('location-api-key');
                        wp_enqueue_script( 'bpla-googlemap', 'https://maps.googleapis.com/maps/api/js?key='.esc_attr($location_api_key).'&libraries=places&callback=bp_group_profile.initMap', array( 'jquery' ), '', true);
                        
                        // FontAwesome icon fonts. If browsing on a secure connection, use HTTPS.
                        // We will only load if our is latest.
                        $recent_fwver = (isset(wp_styles()->registered["fontawesome"])) ? wp_styles()->registered["fontawesome"]->ver : "0";
                        $current_fwver = "4.5.0";
                        if (version_compare($current_fwver, $recent_fwver, '>')) {
                            wp_deregister_style('fontawesome');
                            wp_register_style('fontawesome', "//maxcdn.bootstrapcdn.com/font-awesome/{$current_fwver}/css/font-awesome.min.css", false, $current_fwver);
                            wp_enqueue_style('fontawesome');
                        }
                    }
                        
		}

	}

	 //End of class BuddyBoss_BPLA_BP_Component
	

endif;

