<?php

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}
/**
 * Gravity Press Settings Page and Feed Settings
 *
 *
 * @package Gravity Press
 * @version 1.0.0
 * @since   1.0.0
 */

//Include Gravity Forms Addon Framework
GFForms::include_addon_framework();

//Determine which GF Addon class to extend
if (!class_exists('GFFeedAddOn')) {
    require_once(WP_PLUGIN_DIR . '/gravityforms/includes/addon/class-gf-feed-addon.php');
}

// Extend GFFeedAddon;
class GravityPressFeedAddOn extends GFFeedAddOn
{

    protected $_version = GRAVITYPRESS_VERSION;
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'gravitypress';
    protected $_path = 'gravitypress/gravitypress.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Press';
    protected $_short_title = 'Gravity Press';
    private static $_instance = null;

    /**
     * Get an instance of this class.
     *
     * @return GravityPress
     */
    public static function get_instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new GravityPressFeedAddOn();
        }
        return self::$_instance;
    }

    /**
     * Handles hooks and loading of language files.
     */
    public function init()
    {
        parent::init();
        add_filter('gform_predefined_choices', array($this, 'add_memberpress_level_to_gf_form'));
        add_filter('gform_is_delayed_pre_process_feed', array($this, 'delay_gp_feeds'), 10, 4);
    }


    // # DELAY FEED PROCESSING FOR USER REGISTRATION----------------------------------------------------------------------------------------

    /**
     * Normally the GP Feed processing would take place after User Registration feed has run, this makes it wait until after GP feed has
     * completed before starting the User Registration feed processing
     * Also, disable User Registration feed if user logged in (in both 1st and 2nd function)
     *
     * @param array $is_delayed Which feeds should ultimately be delayed
     * @param array $entry The entry object currently being processed.
     * @param array $form The form object currently being processed.
     * @param array $slug The slug of the feed currently being processed.
     *
     * @return bool|voi
     */

    public function delay_gp_feeds($is_delayed, $form, $entry, $slug)
    {
        $has_registration_feed_type_create = gf_user_registration()->has_feed_type('create', $form);
        if (is_user_logged_in() && $slug == 'gravityformsuserregistration' && !$has_registration_feed_type_create) {
            return gf_user_registration()->has_feed_type('create', $form);
        }
        return $is_delayed;
    }

    // # FEED PROCESSING -----------------------------------------------------------------------------------------------

    /**
     * Process the feed e.g. subscribe the user to a list.
     *
     * @param array $feed The feed object to be processed.
     * @param array $entry The entry object currently being processed.
     * @param array $form The form object currently being processed.
     *
     * @return bool|void
    */

    public function process_feed_enroll_user($feed, $entry, $form, $user_id)
    {
        $payment_settings = null;
        $feeds = GFAPI::get_feeds($feed_ids = '', $form["id"], $addon_slug = 'gravitypress', $is_active = true);

        $gateway_common = new GP_Payment_Gateways_Common();

        // Retrieve the name => value pairs for all fields mapped in the 'mappedFields' field map.
        foreach ($feeds as $feed) {

            $mp_level_ids = GP_Payment_Gateways_Common::get_mapped_field_ids( $feed, $form, $entry );

            // Evaluate the rules configured for the custom_logic setting.
            $feed_condition_meet = false;
            $result = $this->is_feed_condition_met($feed, $form, $entry);

            // Do something awesome because the rules were met.
            if ($result) {
                $feed_condition_meet = true;
                // get feed meta data
                $feed_meta = $feed['meta'];

                // get GravityPress feeds settings
                $feed_settings = $this->gp_feed_settings( $feed_meta, $entry, $form );

                if($payment_settings == null){
                    $payment_settings = $gateway_common->gp_validate_payment_method($entry, $form, $feed_settings);
                }

                if(!$payment_settings) return;

                $mp_level_price = $feed_settings['payment_amount'];
                $coupon_code    = $feed_settings['coupon_code'];

                //****** Add user to a MemberPress Level ********//
                //Loop through the fields from the field map setting building an array of values to be passed to the third-party service
                foreach ($mp_level_ids as $key => $mp_level_id) {

                    //Test that the level being evaluated was selected by the user during form submission
                    if ($mp_level_id != null) {

                        // membership detail
                        $product_detail = new MeprProduct($mp_level_id);
                        $product_price  = $product_detail->price;
                        $group_id = $product_detail->group_id;

                        //deactivate other subscription of the same group
                        //$this->gp_deactivate_all_others_subs_of_group($user_id, $group_id);

                        // gateway detail
                        $gateway_id     = $payment_settings['gateway_id'];
                        $gateway_method = $payment_settings['payment_method'];

                        // if gateway is FREE and amount is greater than 0, stop creating subscriptions and below process
                        if($gateway_id == MeprTransaction::$free_gateway_str && $product_price > 0.00){
                            break;
                        }

                        // check for payment amount, if empty get default amount from Membership detail
                        if(empty($mp_level_price)){
                            $mp_level_price = $product_price;
                        }

                        // transaction id
                        $transaction_id = $payment_settings['transaction_id'];

                        // Create a new transaction and set our new membership details
                        $txn             = new MeprTransaction();
                        $txn->user_id    = $user_id;
                        $txn->product_id = $mp_level_id; // Get selected membership in place
                        $prd             = $txn->product();
                        $txn->gateway    = $gateway_id;

                        // check if the selected level is recurring or non recurring
                        if($txn->gateway === MeprTransaction::$free_gateway_str) $signup_type = 'free';
                        if($prd->is_one_time_payment()) $signup_type = 'non-recurring';
                        if(!$prd->is_one_time_payment()) $signup_type = 'recurring';
                        // double confirmation, check the payment feed type if its transaction type is set to subscription or product
                        if(!empty($payment_settings['transaction_type']) && $payment_settings['transaction_type'] != 'subscription') $signup_type = 'non-recurring';

                        // check if offline enable and payment made by paypal for recurring then skip following condition otherwise return
                        global $paypal_customer;
                        global $stripe_customer;
                        $is_offline_enable = $feed_settings['is_offline_enable'];
                        if($feed_settings['payment_amount'] != 0 and empty($paypal_customer) and empty($stripe_customer) and $is_offline_enable and $signup_type == 'recurring'){
                            return;
                        }
                        // create memberpress subscription if type is recurring
                        if($signup_type == 'recurring'){
                            // Create the subscription from the gateway plan
                            $sub              = new MeprSubscription();
                            $sub->user_id     = $user_id;
                            $sub->gateway     = $gateway_id;
                            $sub->product_id  = $prd->ID;
                            $sub->price       = $mp_level_price;
                            $sub->period      = $prd->period;
                            $sub->period_type = $prd->period_type;
                            $sub->subscr_id   = $transaction_id;
                            // set status for all gateways except paypal,
                            // for paypal sub status will be set after IPN process
                            if( $gateway_method !== 'PayPal Standard'){
                                $sub->status   = $feed_settings['sub_status'];
                                $txn->status   = $feed_settings['trans_status'];
                            }
                            if( $gateway_method == 'PayPal Standard'){
                                $txn->status =  MeprTransaction::$complete_str;
                                $sub->status = MeprSubscription::$active_str;
                            }
                            $sub->store();
                            // Update the transaction with subscription id
                            $txn->subscription_id = $sub->id;

                        }

                        // set transaction status only for non-recurring subscriptions.
                        if($signup_type == 'non-recurring'){
                            $txn->status     = $feed_settings['trans_status'];
                            $txn->trans_num  = $transaction_id;
                        }

                        // save transaction
                        if($coupon_code !== false){
                            $cpn            = MeprCoupon::get_one_from_code(sanitize_text_field($coupon_code));
                            $mp_level_price = $prd->adjusted_price($cpn->post_title);
                            $txn->coupon_id = $cpn->ID;
                        }
                        //If Offline Method Enable
                        if($gateway_method == 'Offline Payment' and empty($entry['payment_method']) and empty($paypal_customer) and empty($stripe_customer)){

                            if($product_price == 0 and empty($paypal_customer)){
                                $txn->gateway = MeprTransaction::$free_gateway_str;
                                $txn->status =  MeprTransaction::$complete_str;
                            } else {
                                $txn->gateway = MeprTransaction::$manual_gateway_str;
                                /**
                                 * Commented by @kumaranup594
                                 * 
                                 * @since 3.4.2
                                 */
//                                $txn->status =  MeprTransaction::$pending_str;
                                /**
                                 * Added by @kumaranup594
                                 * 
                                 * @since 3.4.2
                                 */
                                $txn->status =  $feed_settings['trans_status'];
                            }
                        }

                        $txn->set_subtotal($mp_level_price);

                        $txn->store();

                        if($gateway_method !== 'PayPal Standard'){
                            $gateway_common->gp_send_notices('signup_notice', $txn);
                        }

                        // save transaction detail with related entry
                        gform_update_meta($entry['id'], 'gp_memberpress_trans', $txn, $form["id"]);

                        // attach entry ID with transaction ID
                        gform_update_meta($txn->id, 'gp_memberpress_trans_gf_entry', $entry['id'], $form["id"]);

                        // save if corporate feature is enabled or not
                        $corporate_feature = $this->gp_is_corporate_feature_enable($feed_meta, $mp_level_id);
                        $enable_corporate = false;
                        $num_sub_accounts = 0;


                        // manual method - add user corporate sub accounts based on quantity user entered in the form
                        if($corporate_feature['manual_method']){
                            $quantity_field_id  = rgar($feed_meta, 'corporate_quantity_field');
                            $max_sub_accounts   = $this->get_field_value($form, $entry, $quantity_field_id);
                            $num_sub_accounts   = (int) $max_sub_accounts - 1; // subtract 1, that is customer themselve account
                            $enable_corporate = true;
                        }

                        // default method - add user corporate sub accounts based on the Total added in membership Advance tab
                        if($corporate_feature['default_method']){
                            $num_sub_accounts = $corporate_feature['mp_sub_acccounts'];
                            $num_sub_accounts   = (int) $num_sub_accounts - 1;
                            $enable_corporate = true;
                        }

                        // if corporate feature is enabled, save data as meta data and use it in final process
                        $corporate_data = array('enable' => $enable_corporate, 'total_sub_accounts' => $num_sub_accounts);

                        gform_update_meta($txn->id, 'gp_memberpress_corporate', $corporate_data, $form["id"]);

                        // if txn id didn't save for some reason
                        if(empty($txn->id)) {
                            // Don't want any loose ends here if the $txn didn't save for some reason
                            if($signup_type == 'recurring' && ($sub instanceof MeprSubscription)) {
                              $sub->destroy();
                            }
                            $gateway_common->gp_debug_log(__METHOD__ . "(): Sorry, something went wrong in creating transaction for entry#".$entry['id']);
                            continue;
                        }

                        // cases for different payment methods subscription process
                        switch ($gateway_method) {

                            case 'Stripe':
                                $gpstripe       = new GPStripeGateway();
                                $gpstripe->gp_process_subscription( $txn, $feed_settings, $entry, $signup_type );
                                break;
                            case 'PayPal Standard':
                                $gp_paypal  = new GPPaypalStandardGateway();
                                $gp_paypal->gp_process_subscription( $txn, $feed_settings, $entry, $signup_type );
                                break;
                            case 'Authorize.net':
                                $gp_authorize = new GPAuthorizeGateway();
                                $gp_authorize->gp_process_subscription( $txn, $feed_settings, $entry, $signup_type, $transaction_id );
                                break;
                            case 'Offline Payment':
                                $gp_offline = new GPOfflineGateway();
                                $gp_offline->gp_process_subscription( $txn, $feed_settings, $entry, $signup_type, $transaction_id );
                                break;
                            
                            /**
                             * Added by @kumaranup594
                             * @since 3.3.1
                             * @description Process Gravity press process subscription
                             */
                            case 'PayPal Express Checkout':
                                $gp_paypal_checkout = new GPPaypalCheckoutGateway();
                                $gp_paypal_checkout->gp_process_subscription($txn, $feed_settings, $entry, $signup_type);
                                break;
                            default:
                            # code...
                            break;
                        }

                    }
                }
            }
        }

        // reset all global variables after processing all feeds
        $gateway_common->reset_global_vars();

        if( !$feed_condition_meet )
            $gateway_common->gp_debug_log(__METHOD__ . "(): no gravitypress feeds exist");

        return $feed_condition_meet;
    }

    /**
     * deactivate previous subscriptions in upgrade
     * @return void 
     */
    
    public function gp_deactivate_all_others_subs_of_group($user_id, $group_id){

        $user = new MeprUser( $user_id );
        $get_memberships = $user->active_product_subscriptions();

        $group_product = array();

        foreach($get_memberships as $product_id){
            $product_info = new MeprProduct($product_id);
            $product_group = $product_info->group_id;

            if($product_group === $group_id){
                $group_product[] = $product_id;
            }
        }

        $trans = MeprTransaction::get_all_by_user_id($user_id);

        foreach($trans as $txnn){
            $trans = new MeprTransaction($txnn->id);
            if(in_array($trans->product_id, $group_product)){
                $trans->expires_at = date('d.m.Y',strtotime("-1 days"));
                $trans->store();
            }
            
        }

    }

    /**
     * GET GP FEED SETTINGS
     */
    public function gp_feed_settings( $feed_meta, $entry, $form = null ){
        // Set status for new member added based upon feed settings for transactions
        $membershipstatus = rgar($feed_meta, 'membershipstatus');

        // If transaction, use "complete", if subscription use "active"
        // handle for membership/transaction status
        $membershipstatus_trans = MeprTransaction::$pending_str;
        $membershipstatus_subsc = MeprSubscription::$pending_str;

        if ( $membershipstatus == '$complete_str' ) {
            $membershipstatus_trans = MeprTransaction::$complete_str;
            $membershipstatus_subsc = MeprSubscription::$active_str;
        }

         // Set price field to use for populating MemberPress transactions and subscriptions
        $gp_priceField  = rgar($feed_meta, 'gp_selectpricefield');
        $mp_level_price = isset($entry['payment_amount']) ? $entry['payment_amount'] : 0;
        $gp_priceFieldPrice = 0;
        if(!empty($gp_priceField)){
            //If product field inputType is singleproduct
            if ( strpos($gp_priceField,".") !== false ) {
                $gp_priceFieldQuantity = rgar( $entry, floor($gp_priceField).'.3' );
                $gp_priceFieldPrice    = rgar( $entry, floor($gp_priceField).'.2');
                $gp_priceFieldPrice    = explode ( '$', $gp_priceFieldPrice );
                $gp_priceFieldPrice    = $gp_priceFieldPrice[1];
                $gp_priceFieldPrice    = $gp_priceFieldPrice * $gp_priceFieldQuantity;
            } else {
                //If product field inputType is anything but singleproduct
                $gp_priceFieldPrice = rgar( $entry, $gp_priceField);
                $gp_priceFieldPrice_arr = explode ( '|', $gp_priceFieldPrice);

                // default price if other than Product field
                $gp_priceFieldPrice = $gp_priceFieldPrice_arr[0];

                // if product field, set second index of product field value as field price
                if(isset($gp_priceFieldPrice_arr[1])){
                    $gp_priceFieldPrice = $gp_priceFieldPrice_arr[1];
                }
            }
        }

        //If Pricefield is set in advanced settings, use logic above, otherwise use form payment amount (default)
        if ( $gp_priceField != 0 && $gp_priceFieldPrice) {
            $mp_level_price = number_format($gp_priceFieldPrice, 2);
        }
        // handle for notifications, send notification from GF or MP
        $feed_notification = rgar($feed_meta, 'gpfeednotification');

        // handle for coupons support
        $coupon_code = $this->gp_validate_coupon($feed_meta, $form, $entry);

        // offline method
        $is_offline_method_enable  = rgar($feed_meta, 'gp_enable_offline_method');

        $settings = array(
            'trans_status'      => $membershipstatus_trans,
            'sub_status'        => $membershipstatus_subsc,
            'gp_notification'   => $feed_notification,
            'payment_amount'    => $mp_level_price,
            'coupon_code'       => $coupon_code,
            'is_offline_enable' => $is_offline_method_enable
        );

        return $settings;
    }


    // # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

    /**
     * Creates a custom page for this add-on.
     */
    public function plugin_page()
    {
        echo 'This page appears in the Forms menu';
    }

    /**
     * Configures the settings which should be rendered on the add-on settings tab.
     *
     * @return array
     */
    public function plugin_settings_fields()
    {

        //Run the EDD license settings function
        //edd_gravitypress_license_page();

        //Function to add subscription support info to settings page if addon plugin activated
        $html = gpPro_license_page_subscription_message();


        return array(
			array(
				'title'       => '',
				'description' => "",
				'fields'      => array(
					array(
						'name'    => 'edd_gravitypress_license_key',
						'label'   => esc_html__( 'License Key', 'gravitypress' ),
						'type'    => 'text',
                        'after_input' => 'Enter your license key, then press Save Settings.<br><br>'.$html,
                        'feedback_callback' => 'edd_check_license'
					)
				),
			),
		);

    }

    /**
     * add deactivate plugin and delete all plugin settings functions to Uninstall add-on button 
     * 
     * @return void
     */

    public function uninstall(){
        parent::uninstall();
        deactivate_plugins(GRAVITYPRESS_PLUGIN_DIR . '/gravitypress.php');
    }

    /**
     * Disable the Update Settings button
     *
     * @return array
     */
    public function settings_save($field, $echo = true)
    {
        $field['type'] = 'submit';
        $field['name'] = 'gform-settings-save';
        $field['class'] = 'button-primary gfbutton';
        $screen = get_current_screen();
        $screen_ID = $screen->id;
        $subview = $_GET['subview'];

        //If GF Gravity Press Settings Page, Disable Updated Settings button
        if ($screen_ID == 'forms_page_gf_settings' && $subview == 'gravitypress') {
            return;

        } else { //If NOT GF Gravity Press Settings Page, Display Updated Settings button


            if (!rgar($field, 'value')) {
                $field['value'] = esc_html__('Update Settings', 'gravityforms');
            }

            $attributes = $this->get_field_attributes($field);

            $html = '<input
                        type="' . esc_attr($field['type']) . '"
                        name="' . esc_attr($field['name']) . '"
                        value="' . esc_attr($field['value']) . '" ' . implode(' ', $attributes) . ' />';

            if ($echo) {
                echo $html;
            }

            return $html;
        }

    }

    /**
     * Configures the settings which should be rendered on the feed edit page in the Form Settings > Simple Feed Add-On area.
     *
     * @return array
     */
    public function feed_settings_fields()
    {

        $fields = array();

        $fields[]   =   array(
            'title' => esc_html__('Gravity Press Feed Settings', 'gravitypressfeedaddon'),
            'fields' => array(
                array(
                    'label' => esc_html__('Feed name', 'gravitypressfeedaddon'),
                    'type' => 'text',
                    'name' => 'feedName',
                    'tooltip' => esc_html__('This is the tooltip', 'gravitypressfeedaddon'),
                    'class' => 'small',
                ),
            ),
        );
        
        $fields[]   =   array(
            'title'       => esc_html__( 'Select Form Fields Associated with MemberPress', 'gravitypressfeedaddon' ),
            'fields'    => array(
                array(
                    'name' => 'mappedFields',
                    'label' => esc_html__('Add MemberPress level slug/ID to field(s) in your form, then select field(s) from drop down(s) on the right', 'gravitypressfeedaddon'),
                    'type' => 'dynamic_field_map',
                    'tooltip' => esc_html__('*Use one row per form field if you want to add a user to multiple levels at once. <br />*Product, Drop-down or Radio Button fields only. <br />*MP Level ID must be contained in field Values, not Labels', 'gravitypressfeedaddon'),
                    'field_map' => array(
                        array(
                            'name' => 'memberpressfield1',
                            'label' => esc_html__('Field Containing Level Slug/ID (1)', 'gravitypressfeedaddon'),
                            'required' => 1,
                        ),
                        array(
                            'name' => 'memberpressfield2',
                            'label' => esc_html__('Field Containing Level Slug/ID (2)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield3',
                            'label' => esc_html__('Field Containing Level Slug/ID (3)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield4',
                            'label' => esc_html__('Field Containing Level Slug/ID (4)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield5',
                            'label' => esc_html__('Field Containing Level Slug/ID (5)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield6',
                            'label' => esc_html__('Field Containing Level Slug/ID (6)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield7',
                            'label' => esc_html__('Field Containing Level Slug/ID (7)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield8',
                            'label' => esc_html__('Field Containing Level Slug/ID (8)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield9',
                            'label' => esc_html__('Field Containing Level Slug/ID (9)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                        array(
                            'name' => 'memberpressfield10',
                            'label' => esc_html__('Field Containing Level Slug/ID (10)', 'gravitypressfeedaddon'),
                            'required' => 0,
                        ),
                    ),
                ),
            ),
        );

        if(class_exists('MPCA_Corporate_Account')){
            $fields[]   =  array(
                'title'       => esc_html__( 'Corporate Accounts Addon Support', 'gravitypressfeedaddon' ),
                'fields'    => array(
                    array(
                        'label'   => esc_html__( 'Enable support for MemberPress Corporate Accounts addon', 'gravitypressfeedaddon' ),
                        'type'    => 'checkbox',
                        'name'    => 'gp_enablesubaccount',
                        'tooltip' => esc_html__( 'Select a Product-Quantity field in your form for customers to purchase sub-accounts using MemberPress Corporate Accounts Addon', 'gravitypressfeedaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Enabled', 'gravitypressfeedaddon' ),
                                'name'  => 'gp_enablesubaccount',
                                'default_value'    => 0,
                            ),
                        )
                    ),
                    array(
                        'label'   => esc_html__( 'Assign number of Max Sub-Accounts based on Quantity field?', 'gravitypressfeedaddon' ),
                        'type'    => 'checkbox',
                        'name'    => 'gp_assignmax_subaccounts',
                        'tooltip' => esc_html__( 'Select a Product-Quantity field in your form for customers to purchase sub-accounts using MemberPress Corporate Accounts Addon', 'gravitypressfeedaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Enabled', 'gravitypressfeedaddon' ),
                                'name'  => 'gp_assignmax_subaccounts',
                                'default_value'    => 0,
                            ),
                        ),
                        'hidden' => (!$this->get_setting( 'gp_enablesubaccount'))
                    ),
                    array(
                        'name' => 'corporate_quantity_field',
                        'label' => esc_html__('MemberPress Quantity Field(s)', 'gravitypressfeedaddon'),
                        'type' => 'field_select',
                        'tooltip' => esc_html__('Choose the field on your Gravity Form that you\'ve setup Product Quantity in', 'gravitypressfeedaddon'),
                        'args' => array(
                            'input_types' => array( 'number' )
                        ),
                        'hidden' => ((!$this->get_setting( 'gp_assignmax_subaccounts')) || (!$this->get_setting( 'gp_enablesubaccount')))
                    )
                ),
            );
        }

        if( function_exists( 'gf_coupons' ) ){
            $fields[]   =  array(
                'title'       => esc_html__( 'Coupons Addon Support', 'gravitypressfeedaddon' ),
                'fields'    => array(
                    array(
                        'label'   => esc_html__( 'Enable support for MemberPress Coupon', 'gravitypressfeedaddon' ),
                        'type'    => 'checkbox',
                        'name'    => 'gp_enablecoupons',
                        'tooltip' => esc_html__( 'Create coupons in Memberpress and Gravity Form that matches each other.', 'gravitypressfeedaddon' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Enabled', 'gravitypressfeedaddon' ),
                                'name'  => 'gp_enablecoupons',
                                'default_value'    => 0,
                            ),
                        )
                    ),

                    array(
                        'name'     => 'gp_all_coupons',
                        'label'    => esc_html__( 'Select Coupon', 'gravitypressfeedaddon' ),
                        'type'     => 'checkbox',
                        'multiple' => true,
                        'hidden' => (!$this->get_setting( 'gp_enablecoupons')),
                        'choices' => $this->gp_get_all_coupons()
                    ),

                ),
            );
        }

        $fields[]   =   array(
            'title'       => esc_html__( 'Advanced Options', 'gravitypressfeedaddon' ),
            'fields'    => array(
                array(
                    'label'   => esc_html__( 'Enable Offline Payment', 'gravitypressfeedaddon' ),
                    'type'    => 'checkbox',
                    'name'    => 'gp_enable_offline_method',
                    'tooltip' => esc_html__( 'This option will only work if you have enabled the Offline Method in Memberpress', 'gravitypressfeedaddon' ),
                    'choices' => array(
                        array(
                            'label' => esc_html__( 'Admin must manually complete the transactions', 'gravitypressfeedaddon' ),
                            'name'  => 'gp_enable_offline_method',
                            'default_value'    => 0,
                        ),
                    ),
                ),
                array(
                    'label'   => esc_html__( 'Auto login', 'gravitypressfeedaddon' ),
                    'type'    => 'checkbox',
                    'name'    => 'gp_autologin',
                    'tooltip' => esc_html__( 'Enable auto login after form submission, set redirect URL in form confirmation tab. <b>Does not work with PayPal Standard.</b>', 'gravitypressfeedaddon' ),
                    'choices' => array(
                        array(
                            'label' => esc_html__( 'Enabled', 'gravitypressfeedaddon' ),
                            'name'  => 'gp_autologin',
                            'default_value'    => 0,
                        ),
                    ),
                ),
            	array(
                    'type'    => 'select',
                    'name'    => 'membershipstatus',
                    'label'   => esc_html__( 'Membership Status', 'gravitypressfeedaddon' ),
                    'choices' => array(
                            array(
                                'label' => esc_html__( 'Complete/Active', 'gravitypressfeedaddon' ),
                                'value' => '$complete_str'
                            ),
                            array(
                                'label' => esc_html__( 'Pending', 'gravitypressfeedaddon' ),
                                'value' => '$pending_str'
                            )
                        )
                ),
                array(
                    'label'   => esc_html__( 'Different Price Field', 'gravitypressfeedaddon' ),
                    'type'    => 'checkbox',
                    'name'    => 'gp_selectdifferentpricefield',
                    'tooltip' => esc_html__( 'Change from default form Total to a specific field in your form <br />(NOT RECOMMENDED - better to set this in Stripe/PayPal/Authorize.net feed settings)', 'gravitypressfeedaddon' ),
                    'choices' => array(
                        array(
                            'label' => esc_html__( 'Select field to set membership price in MemberPress other than Payment Amount (Not Recommended)', 'gravitypressfeedaddon' ),
                            'name'  => 'gp_selectdifferentpricefield',
                            'default_value'    => 0,
                        ),
                    ),
                ),
                    array(
                        'label'    => esc_html__( 'Price Field', 'gravitypressfeedaddon' ),
                        'name'     => 'gp_selectpricefield',
                        'type'     => 'field_select',
                        'tooltip'  => '<h6>' . esc_html__( 'Price Field', 'gravitypressfeedaddon' ) . '</h6>' . esc_html__( 'Select which Gravity Form field will be used to set the price in the MemberPress transaction. <br /><b>Default: Payment Amount</b>', 'gravitypressfeedaddon' ),
                        'hidden' => ((!$this->get_setting( 'gp_selectpricefield') && !$this->get_setting( 'gp_selectdifferentpricefield')) || !$this->get_setting( 'gp_selectdifferentpricefield')),
                    ),
                array(
                    'name' => 'condition',
                    'label' => esc_html__('Condition', 'gravitypressfeedaddon'),
                    'type' => 'feed_condition',
                    'checkbox_label' => esc_html__('Enable Condition', 'gravitypressfeedaddon'),
                    'instructions' => esc_html__('Process this simple feed if', 'gravitypressfeedaddon'),
                )
            )
        );
            return $fields;
    }

    /**
     * Configures which columns should be displayed on the feed list page.
     *
     * @return array
     */
    public function feed_list_columns()
    {
        return array(
            'feedName' => esc_html__('Name', 'gravitypressfeedaddon'),
        );
    }

    /**
     * Format the value to be displayed in the mytextbox column.
     *
     * @param array $feed The feed being included in the feed list.
     *
     * @return string
     */
    public function get_column_value_mytextbox($feed)
    {
        return '<b>' . rgars($feed, 'meta/mytextbox') . '</b>';
    }

    /**
     * Prevent feeds being listed or created if an api key isn't valid.
     *
     * @return bool
     */
    public function can_create_feed() {
        // Get the plugin settings.
        $settings = $this->get_plugin_settings();
        // Access a specific setting e.g. an api key
        $key = rgar($settings, 'apiKey');
        return true;
    }

    //Allows admin to bulk-select all available MemberPress levels to add to Gravity Form drop-down or radio fields
    public function add_memberpress_level_to_gf_form($choices) {
        $MemberPressLevels = get_posts(array(
            'order' => 'ASC',
            'orderby' => 'title',
            'post_type' => 'memberpressproduct',
            'numberposts' => -1
        ));
        $options = array();

        // get currency format
        $new_currency = new RGCurrency(GFCommon::get_currency());

        if ($MemberPressLevels) {
            foreach ($MemberPressLevels as $MemberPressLevel){
                $product_detail = new MeprProduct($MemberPressLevel->ID);
                // $price = $product_detail->price;

                // change currency format to current currency format
                $price = $new_currency->to_money($product_detail->price);

                $options[] = $MemberPressLevel->post_title . ' |  ' . $MemberPressLevel->post_name.' |: '. $price;
            }
            $choices["MemberPress"] = $options;
            return $choices;
        }
    }

    public function is_feed_condition_met($feed, $form, $entry) {
        $feed_meta = $feed['meta'];
        $is_condition_enabled = rgar($feed_meta, 'feed_condition_conditional_logic') == true;
        $logic = rgars($feed_meta, 'feed_condition_conditional_logic_object/conditionalLogic');

        if (!$is_condition_enabled || empty($logic))
            return true;

        return GFCommon::evaluate_conditional_logic($logic, $form, $entry);
    }

    public static function gp_add_note() {
        self::add_note();
    }

    /**
     * check if corporate feature enabled in gravitypress feed
     * @param array $feed_meta
     * @param int $level_id
     * @return array
     */
    public function gp_is_corporate_feature_enable( $feed_meta, $level_id ){

        $enabled = array('default_method' => false, 'manual_method' => false, 'mp_sub_acccounts' => 0);

        if( !class_exists( 'MPCA_Corporate_Account' ) ) return $enabled;

        // check memberhip if  corporate feature enabled
        $is_corporate = get_post_meta( $level_id, 'mpca_is_corporate_product', true );
        if(!$is_corporate) return $enabled;

        // get max sub accounts of membership
        $corporate_max_accounts  = get_post_meta( $level_id, 'mpca_num_sub_accounts', true );

        // check if that section is enabled
        $is_mpca_enable = rgar($feed_meta, 'gp_enablesubaccount');
        if(!$is_mpca_enable) return $enabled;

        // check if confirm checkbox(quantity) is checked, if not checked set manual method to false
        $assign_quantity_check = rgar($feed_meta, 'gp_assignmax_subaccounts');
        if(!$assign_quantity_check) return array('default_method' => true, 'manual_method' => false, 'mp_sub_acccounts' => $corporate_max_accounts);

        // all OK, now check if any quantity field is selected
        $quantity_field_id = rgar($feed_meta, 'corporate_quantity_field');
        if(empty($quantity_field_id)) return $enabled;

        // Good to go, quantity field selected and both checkboxes are checked, apply manual method
        return array('default_method' => false, 'manual_method' => true, 'mp_sub_acccounts' => $corporate_max_accounts);

    }

    /**
     * Get all valid coupons form Memberpress
     * If MP coupons exist in GF then add it to dropdown
     *
     * @since 3.0
     */
    public function gp_get_all_coupons(){

        $form_id = rgget( 'id' );
        $form    = GFAPI::get_form( $form_id );
        // get all gravity form coupons
        $gf_coupons = gf_coupons()->get_feeds( $form_id );
        $current_date = date('Y-m-d');

        $cpns = [];
        // loop through GF coupons
        foreach ($gf_coupons as $code) {
            $coupon_code = $code['meta']['couponCode'];
            $end_date = $code['meta']['endDate'];
            $cpn = MeprCoupon::get_one_from_code(sanitize_text_field($coupon_code));

            // if coupon code is expire, skip iteration
            if(!empty($end_date) && strtotime($end_date) < strtotime($current_date)) continue;

            // check if coupon exist in Memberpress, active and not expire
            if($cpn !== false && $code['is_active'] == 1 ){
                $cpns[] = array('label' => $coupon_code, 'name' => 'coupon_'.$code['id'], 'default_value' => 0 );
            }
        }

        return $cpns;

    }

    /**
     * Validate coupon - check if applied coupon exist in form feed
     *
     * @param int $form_id
     * @param array $feed coupons - The checked coupons attached to the form feed
     * @param object $form
     * @param object $entry
     *
     * @return string $coupon_code
     *
     * @since 3.0
     */
    public function gp_validate_coupon($feed_meta, $form, $entry){

        $valid_coupon = false;

        // check if coupon ad-on is installed
        if( !function_exists( 'gf_coupons' ) ) return $valid_coupon;

        // check if coupon feature is enabled
        $enable_coupon = rgar($feed_meta, 'gp_enablecoupons');
        if(!$enable_coupon) return $valid_coupon;

        // get all active coupons displayed in feed
        $active_coupons = $this->gp_get_all_coupons();
        if(count($active_coupons) <= 0) return $valid_coupon;

        $applied_and_feed_coupon = false;
        // loop through coupons and get the selected coupon code name
        foreach ($active_coupons as $code) {
            $feed_coupon = rgar($feed_meta, $code['name']); // feed code
            if($feed_coupon){
                $coupon_label = $code['label'];
                // get form submited coupons
                $form_applied_coupons = gf_coupons()->get_submitted_coupon_codes( $form, $entry );
                // if no coupon is applied return false
                if(empty($form_applied_coupons)){
                    $valid_coupon =  false;
                    break;
                }

                // check if the same coupon is applied in form
                if(in_array($coupon_label, $form_applied_coupons)){
                    $cpn = MeprCoupon::get_one_from_code(sanitize_text_field($coupon_label));
                    $valid_coupon =  $coupon_label;
                    break;
                }
            }
        }

        return $valid_coupon;

    }

} // End Gravity Press Addon Class;
