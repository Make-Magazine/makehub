<?php

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/**
 * Gravity Press Add User class.
 *
 * Adds User to MemberPress Membership Level
 *
 * @package Gravity Press
 * @version 1.0.0
 * @since   1.0.0
 */

class GravityPressAddMembership {

    protected $_slug = "gravitypress";
    protected $_path = "gravitypress/gravitypress.php";
    protected $_full_path = __FILE__;
    protected $_title = "Gravity Press";


    public function __construct() {
        add_filter("gform_validation", array( $this, "check_enrollment_status"));
        add_filter("gform_confirmation", array( $this, "pre_enroll_existing_user"), 10, 4);
        add_action( "gform_user_registered", array( $this, "pre_enroll_new_user"), 10, 4 );
        add_action('admin_enqueue_scripts', array($this, 'gp_feed_enqueue_scripts'));
        
        /**
         * Added by @kumaranup594
         * 
         * @since 3.4.1
         */
        add_filter( 'gform_user_registration_meta_value', array($this,'user_registration_meta_value'), 10, 3 );
    }

    /*-------[ Add User to MemberPress for Existing Users - All Payment Addons except PayPal Standard ]-------*/
    public function pre_enroll_existing_user ( $confirmation, $form, $entry, $ajax ){     
        $form_id = $form['id'];
        $gp_feed_active = $this->are_GP_feeds_active($form_id);
        $payment_status = $entry['payment_status'];
        $user_logged_in = is_user_logged_in();
        $has_registration_feed_type_create = gf_user_registration()->has_feed_type('create', $form);
       
        // stop running this hook for Stripe Payment Form method, either delay registration option is enabled or disabled
        $stripe_delay_register = gform_get_meta($entry['id'], 'gp_stripe_delay_register', true);
        global $stripe_checkout;
        if (($stripe_delay_register == true) || (!empty($stripe_checkout))) {
            return $confirmation;
        }

        //Ensure Gravity Press feeds are active and user logged in
        if ( $gp_feed_active == 1 &&  $user_logged_in == 1) {

            //Ensure payment is either not collected or Paid but not in processing status
            if ( $payment_status != 'Processing' ) {

                //Now grab remaining important variables
                $feed = GFAPI::get_feeds($feed_ids = '', $form_id = '', $addon_slug = 'gravitypress', $is_active = true);
                $user_id = $entry['created_by'];

                //Send data to gp_admin.php process_feed function to enroll user
                $enroll_user = new GravityPressFeedAddOn();
                $gateway_common = new GP_Payment_Gateways_Common();
                $global_settings  = $gateway_common->gp_get_all_global_vars($entry);

                $enroll_user->process_feed_enroll_user( $feed, $entry, $form, $user_id );
            }
        }

        return $confirmation;
    }

    /*-------[ Add User to MemberPress for New Users - All Payment Addons INCLUDING PayPal Standard ]-------*/
    //After user registered through GF User Registration Addon, update user profile in MemberPress to add appropriate level
    public function pre_enroll_new_user ( $user_id, $feed, $entry, $user_pass ) {
        ////---------------//
        $message = 'entryFROMCOREFUNCTION=' . print_r($entry,true);
        //file_put_contents('pressklog.txt', PHP_EOL . $message, FILE_APPEND); //debug file
        //---------------//
        $form_id = $entry['form_id'];
        $gp_feed_active = $this->are_GP_feeds_active($form_id);
        $payment_status = $entry['payment_status'];

        // stop running this hook for Stripe Payment Form method, either delay registration option is enabled or disabled
        $stripe_delay_register = gform_get_meta($entry['id'], 'gp_stripe_delay_register', true);

        global $stripe_checkout;
        if( ($stripe_delay_register == true) || (!empty($stripe_checkout)) ){
            return;
        }
        //Ensure payment is either not collected or Paid but not in processing status
        if ( $gp_feed_active == 1 && ( $payment_status != 'Processing' ) ) {
            //Now grab remaining important variables
            $form = GFAPI::get_form($entry['form_id']);

            //Send data to gp_admin.php process_feed function to enroll user
            $enroll_user = new GravityPressFeedAddOn();
            $enroll_user->process_feed_enroll_user($feed, $entry, $form, $user_id);

            //Automatically login new user if setting enabled
            $auto_login_enable = $this->gp_process_autologin($user_id, $form_id);
            if ( $auto_login_enable ) {
                $this->gp_auto_login_user($user_id, $user_pass, $feed);
            }
        }
    }

    //Check if any Gravity Press Feeds are active on the form
    public function are_GP_feeds_active($form_id) {
        $gp_feed_active = null;

        $feeds = GFAPI::get_feeds(null, $form_id);
                foreach ($feeds as $feed) {
                    if (array_key_exists("addon_slug", $feed)) {
                        if ($feed['addon_slug'] == 'gravitypress') {
                            $gp_feed_active = true;
                        }
                    }
                }
        return $gp_feed_active;
    }

    /**
     * check if auto login is enabled in any of the feed
     * @param $user_id
     * @param $form_id
     * @return boolean
    */
    public function gp_process_autologin( $user_id, $form_id ){

        $gp_feeds       = GFAPI::get_feeds($feed_ids = '', $form_id, $addon_slug = 'gravitypress', $is_active = true);
        $auto_login = false;
        foreach ($gp_feeds as $feed) {
            $feed_meta = $feed['meta'];
            $auto_login_enabled       = rgar($feed_meta, 'gp_autologin');
            if($auto_login_enabled){
                $auto_login = true;
                break;
            }
        }

        return $auto_login;

    }

    /**
     * auto login user
     * @param $user_id
     * @param @user_pass
     * @param $feed Registration fee
     * @return void
     */
     public function gp_auto_login_user( $user_id, $user_pass, $registration_feed){

        // prevent logging in for admin
        if(is_admin()) return;

        $user           = get_userdata( $user_id );
        $user_login     = $user->user_login;
        $user_password  = $user_pass;

        $user_role = 'subscriber';
        // get role from registration feed
        if(!empty($registration_feed)){
            $feed_meta = $registration_feed['meta'];
            $user_role = rgar($feed_meta, 'role');
        }

        $user->set_role($user_role);

        wp_signon( array(
            'user_login'    => $user_login,
            'user_password' =>  $user_password,
            'remember'      => false
        ) );
    }

    //Check if any PayPal Feeds are active on the form
    public function are_PP_feeds_active($form_id) {
        $pp_feed_active = null;

        $feeds = GFAPI::get_feeds($feed_ids = null, $form_id, $addon_slug = 'gravityformspaypal', $is_active = true);

        if ( !is_wp_error( $feeds ) ) {
            //plugin is activated
            $pp_feed_active = true;
        }

        return $pp_feed_active;
    }

    //****** Ensure User Being Added Isn't Already Active for Specified Membership Level *****//
    public function check_enrollment_status( $validation_result ) {
        $form  = $validation_result['form'];
       
        // If the form is GF login Adon  login form then stop processing below code.
        $form_id = $form['id'];
        if($form_id==0)  return $validation_result;

        $gp_feed_active = $this->are_GP_feeds_active($form_id);

        //If Gravity Press Feed not Active, end function and return validation results as they came in
        if (!$gp_feed_active) {
          return $validation_result;
        }

        //Go through each field and ensure that field being checked in on current page
        foreach( $form['fields'] as &$field ) {
            $current_page = rgpost( 'gform_source_page_number_' . $form['id'] ) ? rgpost( 'gform_source_page_number_' . $form['id'] ) : 1;
            $field_page = $field->pageNumber;
            if ( $field_page != $current_page ) {
                continue;
            }

            $entry = GFFormsModel::get_current_lead();
            $user_id = $entry['created_by'];

            $validated = $validation_result['is_valid'];
            $mp_level_ids = array();
            // get feeds for the current form and other data
            $feed                 = GFAPI::get_feeds($feed_ids = '', $form_id, $addon_slug = 'gravitypress', $is_active = true);
            $feed_meta            = $feed[0]['meta'];
            $mp_level_ids         = GP_Payment_Gateways_Common::get_mapped_field_ids( $feed[0], $form, $entry );
            $enable_coupon        = rgar($feed_meta, 'gp_enablecoupons');
            $is_coupon_valid      = true;

            // coupon code feature validation
            if($enable_coupon == 1 && function_exists( 'gf_coupons' )){
                $form_applied_coupons = gf_coupons()->get_submitted_coupon_codes( $form, $entry );
                $gp_feed = new GravityPressFeedAddOn();
                // validate the coupon code if used
                $coupon_code = $gp_feed->gp_validate_coupon($feed_meta, $form, $entry);
                // coupon code is valid, now see if the same coupon is valid for the selected membership
                if($coupon_code !== false){
                    $cpn = MeprCoupon::get_one_from_code(sanitize_text_field($coupon_code));
                    $valid_memberships = $cpn->valid_products;
                    // compare selected Levels and the valid products
                    $mp_valid_array = array_intersect($mp_level_ids, $valid_memberships);
                    // if selected level is not valid and not found in valid products, trigger error
                    if(count($mp_valid_array) <=0 ){
                        $is_coupon_valid = false;
                        //Change Validation message
                        add_filter( "gform_validation_message", array( $this, "validation_message_coupon_validation"), 10, 2 );
                    }
                }
                // coupon code is not valid
                if( $coupon_code === false){
                    // if there is some applied coupons but not valid due to its not created in MP or not selected in GP feed.
                    if(!empty($form_applied_coupons)){
                        $is_coupon_valid = false;
                        //Change Validation message
                        add_filter( "gform_validation_message", array( $this, "validation_message_coupon_invalid"), 10, 2 );
                    }
                }

                if(!$is_coupon_valid){
                    $validation_result['is_valid']  = false;
                    $validation_result['form']      = $form;
                    return $validation_result;
                }
            }

            //Assign modified $form object back to the validation result
            $validation_result['form'] = $form;
            return $validation_result;
        }
    }

    public function validation_message_coupon_validation ( $message, $form ) {
        return "<div class='validation_error'>There was a problem with your submission. <br />Applied coupon is not valid for the selected level. Please try another coupon.</div>";
    }

    public function validation_message_coupon_invalid ( $message, $form ) {
        return "<div class='validation_error'>There was a problem with your submission. <br />Something went wrong in coupon code applied to the form.</div>";
    }

    /**
    * Enqueue JS admin scripts for various Gravity Press funtions
    * @param $hook
    */
    function gp_feed_enqueue_scripts($hook){
        wp_enqueue_script('gp-admin-js', plugins_url('/js/gp-admin.js', __FILE__), array('jquery'), GRAVITYPRESS_VERSION);
        wp_enqueue_style( 'gp-style', plugins_url('/css/style.css', __FILE__),array(), GRAVITYPRESS_VERSION);

        wp_enqueue_script('gp-admin-ajax',plugins_url('/js/gp_ajax.js', __FILE__),array('jquery'), GRAVITYPRESS_VERSION);

        // ajax script for EDD pricing metabox
        wp_localize_script('gp-admin-ajax', 'gpajax', array(
               // URL to wp-admin/admin-ajax.php to process the request
               'ajaxurl' => admin_url('admin-ajax.php'),
               // generate a nonce with a unique ID "wp_nonce_process_gpajax"
               // so that you can check it later when an AJAX request is sent
               'wp_nonce_encrypto' => wp_create_nonce('wp_nonce_process_gpajax'),
           )
        );
    }
    
    /**
     * Added by @kumaranup594
     * 
     * @since 3.4.1
     * 
     * @param type $value
     * @param type $meta_key
     * @param type $meta
     * @param type $form
     * @param type $entry
     * @param type $is_username
     * @return string
     */
    public function user_registration_meta_value( $value = null, $meta_key = null, $meta = null, $form = null, $entry = null, $is_username = null ){
        
        $mepr_options = MeprOptions::fetch();
        $custom_fields = $mepr_options->custom_fields;
        
        if($value){
            foreach ($custom_fields as $eachField){
                if($eachField->field_key == $meta_key && $eachField->field_type == 'checkboxes'){
                    $tempValue =  array_map('trim', explode(',', $value));
                    
                    $value = [];
                    
                    foreach ($tempValue as $eachValue) {
                        $value[$eachValue] = 'on';
                    }
                    
                }
            }
       }
        
        return $value;
    }

} new GravityPressAddMembership();

?>
