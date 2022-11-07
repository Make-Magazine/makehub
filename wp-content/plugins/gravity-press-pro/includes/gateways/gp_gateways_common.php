<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Gravity Payment Gateways Common CLasss
 *
 * all common methods
 *
 * @package GravityPress
 * @version 3.0
 * @since   3.0
 */
class GP_Payment_Gateways_Common {

    // this user ID used for creating subscriptions and transaction before new user registers,
    // later after user registration this ID will be replace
    public $temp_user_id = 9999;

    // temp user delay in running cron
    public $temp_user_delay = 3;

    /**
     * Default constructor.
     *
     * Initialize stripe payment
     *
     * @since   3.0.0
     */
    function __construct() {
        $this->add_actions();
    }

    /**
     * Adds action hooks.
     *
     * @since   3.0.0
     */
    private function add_actions() {

        add_action('gform_payment_details', array($this, 'gp_entry_detail'), 10, 2);
        // ajax request
        add_action('wp_ajax_gp_upgrade_subscription', array($this, 'gp_process_upgrade_subscription'));

        // this cron action remove all temp users attached to entry
        add_action('gp_remove_temp_users', array($this, 'gp_delete_temp_users'), 10);
        // call when pending user activated in GF
        add_action('gform_activate_user', array($this, 'gp_activate_user'), 10, 3);
    }

    /**
     * Get all Memberpress gateway options
     *
     * @param string $gateway = Stripe / Authorize.net / PayPal Standard
     * @return array of setting
     */
    public function gp_memberpress_settings($paymentgateway){

        $settings = array();
        $mepr_options = MeprOptions::fetch();
        $pm = $mepr_options->payment_methods();
        $gateway_ids = array_keys($mepr_options->payment_methods());

        foreach ($gateway_ids as $gateway_id) {
            $gateway = $mepr_options->payment_method($gateway_id);

            if($gateway->name == $paymentgateway || ($paymentgateway == 'Offline Payment' && $gateway->settings->gateway == "MeprArtificialGateway")){
                $settings  = $gateway->settings;
                return $settings;
            }
        }

        return $settings;

    }

    /**
     * GET ALL Mapped FIELD IDS IN SINGLE FEED OF FORM
     *
     * @param object $feed
     * @param object $form
     * @param object $entry
     */
    public static function get_mapped_field_ids( $feed, $form, $entry ){

        $mp_field_ids = array();

        // Evaluate the rules configured for the custom_logic setting.
        $result = gp_addon()->is_feed_condition_met($feed, $form, $entry);

        // Do something awesome because the rules were met.
        if ($result) {

            $field_map = gp_addon()->get_dynamic_field_map_fields($feed, 'mappedFields');

            //Loop through the fields from the field map setting building an array of values to be passed to the third-party service.
            foreach ($field_map as $name => $field_id) {
                // Get the field value for the specified field id
                $merge_vars[$name] = gp_addon()->get_field_value($form, $entry, $field_id);
                $level_field_id = explode(' ', $merge_vars[$name]);
                $level_id = $level_field_id[0]; // this can be slug or id
                // check if slug is given, find membership id using slug
                if(!is_numeric($level_id)){
                    $level_id = GP_Payment_Gateways_Common::gp_get_level_id_by_slug( $level_id );
                }
                $mp_field_ids[] = $level_id;
            }
        }

        return $mp_field_ids;
    }

    /**
     * Get level id by slug
     * @param string $slug
     * @return int $level_id
     */
    public static function gp_get_level_id_by_slug( $slug ){

        $level_id = 0;
        $args = array(
            'name'        => $slug,
            'post_type'   => 'memberpressproduct',
            'post_status' => 'publish',
            'numberposts' => 1
        );

        $levels  = get_posts( $args );
        if($levels){
            $level_id = $levels[0]->ID;
        }

        return $level_id;
    }

    /**
     * Writes an error message to the Gravity Press log.
     *
     * @param string $message
     * @return void
     */
    public function gp_debug_log( $message ){

        $gpadon = new GravityPressFeedAddOn();
        $gpadon->log_debug( $message );
        return;

    }

    /**
     * GET FORM PAYMENT METHOD
     */
    public function gp_get_all_global_vars( $entry = null ){
        global $stripe_customer;
        global $paypal_customer;
        global $authorize_customer;
        global $gp_transaction_type;
        global $stripe_checkout;

        /**
         * Added by @kumaranup594
         * @since 3.3.1
         * @description PayPal checkout customer
         */
        global $paypal_checkout_customer;
        
        // default payment method
        $payment_method = 'Manual';

        // get default transaction ID
        $transaction_id = ($entry != null && isset($entry['transaction_id']))?$entry['transaction_id']:'';

        // get transaction type of feed in process (product or subscription)
        $transaction_type = (!empty($gp_transaction_type))?$gp_transaction_type:'';

        // GF Stripe credit card field
        // For subscription feed use Stripe Customer Id as transaction & sub id
        // For product feed use entry transaction ID as Memberpress trans ID
        if( !empty( $stripe_customer ) ){
            $payment_method = 'Stripe';
            if( empty( $transaction_id ) &&  !empty( $stripe_customer ) && $transaction_type != 'product' ){
                $transaction_id = $stripe_customer->id;
            }
        }

        // GF Stripe Checkout method
        // global variable set in gform_stripe_session_data and stripe_fulfill hooks
        if( !empty( $stripe_checkout ) ){
            $payment_method      = 'Stripe';
            $transaction_type    = ( !empty($stripe_checkout['subscription']) )?'subscription':'product';
            if( empty( $transaction_id ) &&  !empty( $stripe_checkout ) && $transaction_type != 'product' ){
                $transaction_id = $stripe_checkout['subscription'];
            }
            if( empty( $transaction_id ) &&  !empty( $stripe_checkout ) && $transaction_type == 'product' ){
                $transaction_id      = $stripe_checkout['transaction_id'];
            }
            $gp_transaction_type = $transaction_type;
        }

        // For stripe if global variable is not set, detect it from transaction id prefix
        if( $payment_method == 'Manual' && !empty( $transaction_id )){
            // see if transaction id is correctly setup with sub_ or ch_
            if( strpos( $transaction_id, 'sub_' ) !== false ){
                $payment_method      = 'Stripe';
                $transaction_type = 'subscription';
            }

            // stripe credit card method use ch_ prefix
            // stripe checkout method use pi_ prefix
            if( strpos( $transaction_id, 'ch_' ) !== false ||  strpos( $transaction_id, 'pi_' ) !== false){
                $payment_method      = 'Stripe';
                $transaction_type    = 'product';
            }
        }

        // Paypal gateway
        if( !empty( $paypal_customer ) ){
            $payment_method = $paypal_customer;
        }

        // authorize gateway
        if( !empty( $authorize_customer ) ){
            $payment_method = $authorize_customer;
        }
        
        /**
         * Added by @kumaranup594
         * @since 3.3.1
         * @description Set Payment method PayPal checkout if PayPal checkout customer created        $paypal_checkout_customer
         * Paypal checkout gateway
         */
        if($payment_method == 'Manual' && !empty( $paypal_checkout_customer ) ){
            $payment_method = $paypal_checkout_customer;
        }
        

        // in case transaction id is empty, set it to random number
        if(empty($transaction_id)){
            $transaction_id   = 'mp-txn-' . uniqid();
        }


        // in case transaction type is empty, check it in entry detail
        if( empty($transaction_type) && isset($entry['transaction_type']) && !empty( $entry['transaction_type'] )){
            /**
             * Commented by @kumaranup594
             * @since 3.3.1
             * @description In this existing code the transaction_type was blank
             */
//            if( $entry['transaction_type'] == 1 ) $transaction_type == 'product';
//            if( $entry['transaction_type'] == 2 ) $transaction_type == 'subscription';
            
            /**
             * Added by @kumaranup594
             * @since 3.3.1
             * @description Assigned the transaction type
             * When $entry[transaction_type] = 1 then set the $transaction_type = product
             * When $entry[transaction_type] = 2 then set the $transaction_type = subscription
             */
            if( $entry['transaction_type'] == 1 ) 
                $transaction_type = 'product';
            
            if( $entry['transaction_type'] == 2 ) 
                $transaction_type = 'subscription';
        }

        $data = [
            'payment_method'   => $payment_method,
            'transaction_id'   => $transaction_id,
            'transaction_type' => $transaction_type
        ];

        return $data;
    }

    /**
     * validate payment method
     * check if this gateway is activated on memberpress
     */
    public function gp_validate_payment_method($entry, $form = null, $gp_feed_setting){

        global $paypal_customer;
        global $stripe_customer;
        $is_offline_enable = $gp_feed_setting['is_offline_enable'];
        $global_settings  = $this->gp_get_all_global_vars($entry);

        if( $is_offline_enable and empty($paypal_customer) and empty($stripe_customer) and $global_settings['payment_method'] != 'PayPal Standard'){

            $payment_method   = 'Offline Payment'; // Don't rename, this is taken from Memberpress
            $transaction_id   = 'mp-txn-' . uniqid(); // random ID
            $transaction_type = ''; // we don't need this as it will be fetched from original Memberpress membership
        } else {
            // get payment method and transaction ID in form submission
            $global_settings  = $this->gp_get_all_global_vars($entry);
            $payment_method   = $global_settings['payment_method'];
            $transaction_id   = $global_settings['transaction_id'];
            $transaction_type = $global_settings['transaction_type'];
        }


        // gateway settings
        $gateway_settings  = $this->gp_memberpress_settings($payment_method);

        if($is_offline_enable and sizeof( (array) $gateway_settings ) == 0 and $entry['payment_method'] != 'visa' and empty($paypal_customer) and empty($stripe_customer)){
            // payment method valid, return settings
            $settings = [
                'gateway_id'       => 'check',
                'payment_method'   => $payment_method,
                'transaction_id'   => $transaction_id,
                'transaction_type' => $transaction_type
            ];
            return $settings;
        }

        $gateway_id   = (!empty($gateway_settings->id)?$gateway_settings->id:MeprTransaction::$free_gateway_str);

        // validate payment method in memberpress
        $mepr_options = MeprOptions::fetch();
        $pm           = $mepr_options->payment_method($gateway_id);
        if(!$pm instanceof MeprBaseRealGateway){
            $this->gp_debug_log(__METHOD__ . "(): invalid payment method");
            return false;
        }

        // payment method valid, return settings
        $settings = [
            'gateway_id'       => $gateway_id,
            'payment_method'   => $payment_method,
            'transaction_id'   => $transaction_id,
            'transaction_type' => $transaction_type
        ];

        return $settings;

    }


    /**
     * Check if any Gravity Press Feeds are active on the form
     * @param $form_id int
     *
     * @return boolean
     */
    public function are_GP_feeds_active($form_id) {

        $gp_feed_active = false;

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
     * GET Payment gateway detail By ID
     *
     * @param int $gateway_id
     */
    public function gp_get_gateway_detail($gateway_id){
        $mepr_options = MeprOptions::fetch();
        $gateway = $mepr_options->payment_method($gateway_id);
        return $gateway;
    }

    /**
     * Add upgrade subscription button on entry detail page for old version subscriptions
     *
     * @param int $form_id
     * @param object $entry
     */
    public function gp_entry_detail( $form_id, $entry ){
        $transaction_id     = $entry['transaction_id'];
        $transaction_detail = gform_get_meta($entry['id'], 'gf_memberpress_txn');  // old version meta data

        // for older version, show upgrade button if transaction id exist
        if($this->gp_check_old_transaction_exist($transaction_detail, $transaction_id)):
            $recurring_type  = ($transaction_detail->subscription_id > 0)?'recurring':'non-recurring';
            $txn_id          = $transaction_detail->id;
            ?>
            <div class="gp_entry_sub_wrapper">
                <input id="upgrade_mepr_sub" type="button" name="upgrade_mepr_sub" value="Upgrade MemberPress Subscription" class="button" data-txn="<?php echo $txn_id; ?>" data-type="<?php echo $recurring_type; ?>" data-gateway="" data-entry="<?php echo $entry['id']; ?>" data-nonce="<?php echo wp_create_nonce('wp_nonce_gravitypress'); ?>">
                <div class="spinner" style="visibility: hidden; position: absolute;"></div>
            </div>
            <?php
        endif;

    }

    public function gp_check_old_transaction_exist($txn_detail, $gf_txn_id){

        // if entry has no transaction id
        if(empty($gf_txn_id)) return false;

        // if meta data is empty, no transaction exist
        if(empty($txn_detail)) return false;

        // process transaction detail
        $txn_id = $txn_detail->id;
        $sub_id = $txn_detail->subscription_id;

        // check if the transaction exist in Memberpress / recurring subscri
        if($sub_id > 0){
            $sub = new MeprSubscription($sub_id);
            if($sub) return true;
        }

        // non recurring transaction
        $txn = new MeprTransaction($txn_id);
        if($txn) return true;

        return false;

    }

    /**
     * This method trigger when click on "upgrade subscription" on gf entry detail page.
     * Process old subscription, upgrade memberpress subscription with payment gateway
     * Ajax Request
     */
    public function gp_process_upgrade_subscription(){

        // Check Ajax Nonce
        $nonce = esc_attr($_POST['wp_nonce_encrypto']);
        if ( ! wp_verify_nonce( $nonce, 'wp_nonce_process_gpajax' ) ){
            wp_die(); // Stop the anonymous
        }

        $entry_detail   = GFAPI::get_entry( $_POST['entry'] );
        $gf_transaction_id = $entry_detail['transaction_id'];
        $mp_txn_id  = $_POST['txn_id'];
        $recurring_type = $_POST['type'];
        $entry_id = $_POST['entry'];

        $payment_gateway = $this->gp_get_payment_gateway_old($entry_id);

        // only works for Paypal and Stripe
        if($payment_gateway != 'Stripe' && $payment_gateway != 'PayPal'){
            http_response_code(404);
            echo 'Something went wrong in payment gateway '.$payment_gateway;
            exit;
        }


        // rename gateway name as in Memberpress
        if($payment_gateway == 'PayPal') $payment_gateway = 'PayPal Standard';

        // get payment detail
        $mp_settings = $this->gp_memberpress_settings($payment_gateway);
        $secret_key  = $mp_settings->secret_key;
        $gateway_id  = $mp_settings->id;

        if($payment_gateway == 'Stripe' && empty($secret_key)){
            http_response_code(404);
            echo 'Memberpress payment gateway failed: secret key not found';
            exit;
        }

        // if no gateway configured in memberpress
        if(empty($gateway_id)){
            http_response_code(404);
            echo 'Something went wrong in payment gateway integration in Memberpress: gateway id not found. ';
            exit;
        }

        // handle for stripe
        if($payment_gateway == 'Stripe'){
            $stripe_obj = new MeprStripeGateway();
            $stripe_obj->settings->secret_key = $secret_key;
            if($recurring_type == 'recurring'){
                try{
                    $subscription = $stripe_obj->send_stripe_request("subscriptions/".$gf_transaction_id, array(), 'get');
                }
                catch(Exception $e){
                    http_response_code(404);
                    echo 'Something went wrong in stripe api request: '.$e->getMessage();
                    exit;
                }
                if(!empty($subscription['customer'])){
                    $customer_id = $subscription['customer'];
                    $sub = $this->gp_update_subscriptions_old($gateway_id, $customer_id, $entry_id);
                    if(!$sub){
                        http_response_code(404);
                        echo 'Something went wrong in updating subscriptions.';
                        exit;
                    }

                    echo 'success';
                    exit;
                }
            }
        }

        if($payment_gateway == 'PayPal Standard'){
            $sub = $this->gp_update_subscriptions_old($gateway_id, $gf_transaction_id, $entry_id);
            echo 'success';
            exit;
        }

        echo "something went wrong..";
        exit;
    }

    /**
     * Update old subscriptions/transaction to new version by updating customer ID and Payment Gateway
     */
    public function gp_update_subscriptions_old($gateway_id, $subscr_id, $entry_id){

        $old_trans_detail = gform_get_meta($entry_id, 'gf_memberpress_txn');
        if(empty($old_trans_detail)) return false;

        $txn_id = $old_trans_detail->id;
        $sub_id = $old_trans_detail->subscription_id;

        // if recurring subsciption
        if($sub_id > 0 && $subscr_id != ''){
            try{
                $sub = new MeprSubscription($sub_id);
                $sub->subscr_id = $subscr_id;
                $sub->gateway = $gateway_id;
                $sub->store();
            }catch(Exception $e){
                return false;
            }
        }

        // non recurring transaction
        $txn = new MeprTransaction($txn_id);
        // for non recurring update transactions
        if($sub_id <= 0){
            $txn->trans_num = $subscr_id;
            $txn->store();
            $txn->gateway    = $gateway_id;
        }

        // update lates meta data
        gform_update_meta($entry_id, 'gp_memberpress_trans', $txn);
        gform_update_meta($txn_id, 'gp_memberpress_trans_gf_entry', $entry_id);

        // delete old version meta
        gform_delete_meta( $entry_id,  'gf_memberpress_txn');

        return true;
    }

    /**
     * Get old version transactions payment detail fron entry notes
     */
    public function gp_get_payment_gateway_old($entry_id){

        $payment_gateway = '';
        $entry_notes = RGFormsModel::get_lead_notes( $entry_id );
        foreach ($entry_notes as $note) {
            if(!empty($note->user_name)){
                $payment_gateway = $note->user_name;
                break;
            }
        }

        return $payment_gateway;
    }

    /**
     * save user corporate account
     *
     * @param int $obj_id - The subscription id for recurring and transaction id for non-recurring
     * @param object $txn
     * @param array $corporate - Having sub account & disable/enable flag
     */
    public function gp_save_corporate_accounts($obj_id, $txn, $corporate,$user_id=null){
        // if not enabled return this
        if(!$corporate['enable']) return;
        $txn_type = ($txn->subscription_id > 0)?'subscriptions':'transactions';

        // save corporate sub account
        $mcpa_obj                   =   new MPCA_Corporate_Account();
        $mcpa_obj->user_id          = $user_id == null ? $txn->user_id : $user_id;
        $mcpa_obj->obj_type         = $txn_type;
        $mcpa_obj->obj_id           = $obj_id;
        $mcpa_obj->num_sub_accounts = $corporate['total_sub_accounts'];
        $mcpa_obj->store();

        return $mcpa_obj->id;
    }

    /**
     * Trigger Memberpress Emails
     */
    public function gp_send_notices($type, $txn){

        $this->gp_debug_log(__METHOD__ . "(): sending emails ...");
        switch ($type) {
            case 'payment_receipt':
                MeprUtils::send_transaction_receipt_notices( $txn );
                break;
            case 'signup_notice':
                MeprUtils::send_signup_notices( $txn, true, $admin_notice = true );
                break;
            default:
                # code...
                break;
        }

        return;
    }

    /**
     * check if there is any payment feeds active
     */
    public function gp_active_payment_feeds($form){

        $payment_feed_found = false;
        $stripe_feeds       = GFAPI::get_feeds(null, $form["id"], 'gravityformsstripe');
        $paypal_feeds       = GFAPI::get_feeds(null, $form["id"], 'gravityformspaypal');
        $authorize_feeds    = GFAPI::get_feeds(null, $form["id"], 'gravityformsauthorizenet');

        if ( !is_wp_error( $stripe_feeds ) || !is_wp_error( $paypal_feeds ) || !is_wp_error( $authorize_feeds ) ) {
            $payment_feed_found = true;
        }

        return $payment_feed_found;
    }

    /**
     * Process subscriptions after creating pending subscription
     * validate subscription
     * process corporate account feature if enabled
     */
    public function process_subscription($entry, $txn, $signup_type){

        $this->gp_debug_log(__METHOD__ . "(): executing subscription/transaction process");

        if(isset($txn) and $txn instanceof MeprTransaction) {
            $txn_id = $txn->id;
        }
        else {
            $txn->destroy();
            $this->gp_debug_log(__METHOD__ . "(): Payment was unsuccessful, please check your payment details and try again.");
            return false;
        }

        // enable corporate sub accounts for this user if enabled in membership
        $obj_id     = ($signup_type == 'recurring')?$txn->subscription_id:$txn_id;
        $corporate  = gform_get_meta($txn->id, 'gp_memberpress_corporate');
        $this->gp_save_corporate_accounts($obj_id, $txn, $corporate);

        // send email if non recurring subscriptions
        if($signup_type == 'non-recurring'){
            // send payment reciept to user
            $this->gp_send_notices('payment_receipt', $txn);
            // save entry note
            GFFormsModel::add_note($entry['id'], 0, 'Gravity Press', 'MemberPress Transaction has been created. Transaction Id: ' . $txn->trans_num . '.', 'success');
            return true;
        }

        $sub = $txn->subscription();
        return $sub;
    }

    public function reset_global_vars(){

        // reset authorize.net vars
        global $authorize_customer;
        $authorize_customer = '';

        // reset stripe
        global $stripe_customer;
        $stripe_customer = '';

        global $gp_transaction_type;
        $gp_transaction_type = '';

        global $stripe_checkout;
        $stripe_checkout = '';
        
        /**
         * Added by @kumaranup594
         * @since 3.3.1
         * @description Reset PayPal checkout customer $paypal_checkout_customer
         * 
         */
        global $paypal_checkout_customer;
        $paypal_checkout_customer = '';

    }

    /**
     * If delay registration checkbox under Paypal feed is checked, create new user using GF Registration ad-on methods
     *
     * @param object $entry - Entry object currently being processed
     */
    public function gp_process_create_user( $entry, $feed ){

        // Log the user creation is being in process
        $this->gp_debug_log(__METHOD__ . "(): starting user creation..");

        // get form object
        $form =  GFAPI::get_form( $entry['form_id'] );

        // Ensure that user registration active feed exist
        if( isset( $feed ) ){

            $this->gp_debug_log(__METHOD__ . "(): calling create user");

            // process user registration
            $user = gf_user_registration()->create_user( $entry, $form, $feed );

            if($user){
                $user_id = $user['user_id'];
                $this->gp_debug_log(__METHOD__ . "(): user created successfully, user ID: ".$user_id."");
            }
        }

        return $user_id;
    }

    /**
     * If Enable User Activation is checked under User Registration feed, save txn/sub meta data for later when user get activated.
     *
     * @param object $entry - Entry object in process
     * @param $feed
     *
     * @return int $user_id = 0
     */
    public function gp_process_user_activation( $entry, $feed, $sub, $txn ){

        $this->gp_debug_log(__METHOD__ . "(): calling user activation..");

        // get form object
        $form =  GFAPI::get_form( $entry['form_id'] );

        $user_id = 0;
        // set user activation process
        gf_user_registration()->handle_user_activation( $entry, $form, $feed );

        $this->gp_debug_log(__METHOD__ . "(): user set as pending");

        // temporarily save sub/transaction info to use it in GF User activation hook.
        gform_update_meta($entry['id'], 'gp_mp_sub_activated', $sub, $entry['form_id']);
        gform_update_meta($entry['id'], 'gp_mp_txn_activated', $txn, $entry['form_id']);

        return $user_id;
    }

    /**
     * Delete temporary user from wordpress
     */
    public function delete_temp_user( $entry ){

        $this->gp_debug_log(__METHOD__ . "(): deleting temp user.. ");

        if( empty($entry['id']) ) return false;

        // get user ID of temp user, if created
        $temp_user_id = gform_get_meta($entry['id'], 'gp_temp_user_id');
        if(empty( $temp_user_id )) return false;

        $user_data = get_user_by( 'id', $temp_user_id );

        // check if id exist already
        if( !empty( $user_data ) ){
            require_once(ABSPATH.'wp-admin/includes/user.php' );
            $delete_user = wp_delete_user( $temp_user_id );
            gform_delete_meta( $entry['id'], 'gp_temp_user_id' );
            $this->gp_debug_log(__METHOD__ . "(): temp user deleted: ");
            return $delete_user;
        }else{
            $this->gp_debug_log(__METHOD__ . "(): no temp user exist ");
        }

        return false;
    }

    /**
     * Create temporary user
     */
    public function create_temp_user( $entry ){

        $form_id        = $entry['form_id'];
        $user_name = $this->generate_temp_username( $entry );
        $this->gp_debug_log(__METHOD__ . "(): creating temp user: ".$user_name."");

        $user_id = username_exists( $user_name );

        if ( ! $user_id ) {
            $random_pw = wp_generate_password( $length = 12, $include_standard_special_chars = false );
            $user_id   = wp_create_user( $user_name, $random_pw, $this->generate_user_temp_email( $entry ) );
            if( !is_wp_error( $user_id ) ){
                gform_update_meta( $entry['id'], 'gp_temp_user_id', $user_id, $form_id );
                $this->gp_debug_log(__METHOD__ . "(): user created successfully, User ID: ".$user_id.", User Login: ".$user_name.".");
            }
        }

        return $user_id;
    }

    /**
     * generate temp user name,
     * concatenate User ID with temp username to make it unique for each user.
     */
    public function generate_temp_username( $entry ){
        $user_name = 'gptempuser_'.$entry['id'];
        return $user_name;
    }

    /**
     * check if temporary user of specific entry exist in the systed
     */
    public function temp_user_exist( $entry ){
        $user_name = $this->generate_temp_username( $entry );
        if( username_exists( $user_name ) ) return true;

        return false;
    }

    /**
     * Generate user email
     * concatenate entry with user temp email
     */
    public function generate_user_temp_email( $entry ){
        $user_email = 'gptemp'.$entry['id'].'@example.com';
        return $user_email;
    }

    /**
     * set cron that will delete temp users after 3 minutes of final process
     */
    public function set_temp_user_cron( $entry_id ){
        $time_delay = current_time('timestamp') + ($this->temp_user_delay * MINUTE_IN_SECONDS);
        $time_delay = $time_delay - (get_option('gmt_offset') * 60 * 60);
        wp_schedule_single_event($time_delay, 'gp_remove_temp_users', array($entry_id));
    }

    /**
     * Delete temp users
     */
    public function gp_delete_temp_users( $entry_id ){

        $this->gp_debug_log(__METHOD__ . "(): cron running to delete temp user of entry id: ".$entry_id." ");
        // get entry detail
        $entry = GFAPI::get_entry($entry_id);
        $this->delete_temp_user( $entry );
    }

    /*
    * This hook call when pending user get activated.
    * Process in this method is only added for Paypal gateway, for other gateways gform_registered hook calls by default.
    */
    public function gp_activate_user( $user_id, $user_data, $signup_meta ){

        $entry_id = $signup_meta['entry_id'];
        // get all vars
        $entry   = GFAPI::get_entry($entry_id); // get entry detail
        $form_id = $entry['form_id'];

        $gp_feed_active = $this->are_GP_feeds_active($form_id);
        if(!$gp_feed_active) return;

        $sub_data = gform_get_meta($entry_id, 'gp_mp_sub_activated');
        $txn_data = gform_get_meta($entry_id, 'gp_mp_txn_activated');

        $sub_id  = 0;

        // store subscription / For Paypal
        if( !is_wp_error( $sub_data ) && !empty($sub_data) && $txn_data->subscription_id > 0 ){
            $sub              = new MeprSubscription();
            $sub->user_id     = $user_id;
            $sub->gateway     = $sub_data->gateway;
            $sub->product_id  = $sub_data->product_id;
            $sub->price       = $sub_data->price;
            $sub->period      = $sub_data->period;
            $sub->period_type = $sub_data->period_type;
            $sub->subscr_id   = $sub_data->subscr_id;
            $sub->status      = $sub_data->status;
            $sub->store();
            $sub_id = $sub->id;
            gform_update_meta( $entry_id, 'gp_memberpress_sub', $sub, $form_id );
            GFFormsModel::add_note($entry_id, 0, 'Gravity Press', 'Subscription process complete. Subscription ID: '.$sub->subscr_id, 'success');
        }

        // create transation / For Paypal
        if( !is_wp_error( $txn_data ) && !empty($txn_data) ){
            $txn             = new MeprTransaction();
            $txn->user_id    = $user_id;
            $txn->product_id = $txn_data->product_id; // Get selected membership in place
            $txn->gateway    = $txn_data->gateway;
            $txn->status     = $txn_data->status;
            $txn->trans_num  = $txn_data->trans_num;
            $txn->txn_type   = $txn_data->txn_type;
            $txn->set_subtotal($txn_data->amount);
            if( $txn_data->subscription_id > 0 && $sub_id > 0){
                $txn->subscription_id = $sub_id;
                $txn->txn_type   = MeprTransaction::$payment_str;
                $txn->status     = MeprTransaction::$complete_str;
                $txn->set_subtotal($sub->price);
            }
            $txn->store();
            GFFormsModel::add_note($entry_id, 0, 'Gravity Press', 'Memberpress transaction process completed. Transaction ID: '.$txn->trans_num, 'success');
            $this->gp_send_notices('signup_notice', $txn);
            $this->gp_send_notices('payment_receipt', $txn);
            // update entry status
            GFAPI::update_entry_property( $entry_id, 'payment_status', 'Paid' );
            // save new info meta data
            gform_update_meta( $entry_id, 'gp_memberpress_trans', $txn, $form_id );
        }

        /**
         * For other gateway, after activation of user GF calls gform_user_registered hook
         * which is already added in gp_core_functions.php file to process the GP feed.
         */

        // delete temp info that we no longer need.
        gform_delete_meta( $entry_id, 'gp_mp_sub_activated' );
        gform_delete_meta( $entry_id, 'gp_mp_txn_activated' );

    }

}
new GP_Payment_Gateways_Common();
?>
