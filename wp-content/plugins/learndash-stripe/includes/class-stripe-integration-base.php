<?php

/**
* Stripe checkout integration base class
*/
abstract class LearnDash_Stripe_Integration_Base {
    
    /**
     * Plugin options
     * @var array
     */
    protected $options;

    /**
     * Stripe secret key
     * @var string
     */
    protected $secret_key;

    /**
     * Stripe publishable key
     * @var string
     */
    protected $publishable_key;

    /**
     * Stripe endpoint secret
     * @var string
     */
    protected $endpoint_secret;

    /**
     * Plugin default payment button
     * @var string
     */
    protected $default_button;

    /**
     * Variable to hold the Stripe Button HTML. This variable can be checked from other methods.
     * @var string
     */
    protected $stripe_button;

    /**
     * Variable to hold the Course object we are working with.
     * @var object
     */
    protected $course;

    /**
     * Stripe checkout session id
     * @var string
     */
    protected $session_id;

    /**
     * Stripe checkout session object if successful|WP_Error otherwise
     * @var object
     */
    protected $session;

    /**
     * Stripe customer id meta key name
     *
     * @var string
     */
    protected $stripe_customer_id_meta_key;

    /**
     * Class construction function
     */
    public function __construct() {
        $this->options                  =   get_option( 'learndash_stripe_settings', array() );

        $this->set_stripe_customer_id_meta_key();
    
        $this->secret_key               =   $this->get_secret_key();
        $this->publishable_key          =   $this->get_publishable_key();
        $this->endpoint_secret          =   $this->get_endpoint_secret();

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_filter( 'learndash_payment_button', array( $this, 'payment_button' ), 10, 2 );
        add_action( 'init', array( $this, 'process_webhook' ) );
        add_action( 'wp_footer', array( $this, 'output_transaction_message' ) );
    }
    
    public function set_stripe_customer_id_meta_key()
    {
        if ( $this->is_test_mode() ) {
            $this->stripe_customer_id_meta_key = 'stripe_test_customer_id';
        } else {
            $this->stripe_customer_id_meta_key = $this->stripe_customer_id_meta_key;
        }
    }

    public function get_stripe_customer_id_meta_key()
    {
        return $this->stripe_customer_id_meta_key;
    }

    /**
     * Stripe config function
     */
    public function config() {
        require_once LEARNDASH_STRIPE_PLUGIN_PATH . 'vendor/autoload.php';

        \Stripe\Stripe::setApiKey( $this->secret_key );
    }

    /**
     * Check if the integration is in test mode
     *
     * @return boolean True if in test mode|false otherwise
     */
    public function is_test_mode()
    {
        return isset( $this->options['test_mode'] ) && 1 == $this->options['test_mode'];
    }

    /**
     * Get course button args
     * @param  int    $course_id Course ID
     * @return array             Course args
     */
    public function get_course_args( $course_id = null ) {
        if ( ! isset( $course_id ) ) {
            $course = $this->course;
        } else {
            $course = get_post( $course_id );
        }

        if ( ! $course ) {
            return false;
        }

        $user_id    = get_current_user_id();
        $user_email = null;

        if ( 0 != $user_id ) {
            $user = get_userdata( $user_id );
            $user_email = ( '' != $user->user_email ) ? $user->user_email : '';
        }

        if ( learndash_get_post_type_slug( 'course' ) === $course->post_type ) {
            $course_price      = learndash_get_setting( $course->ID, 'course_price' );
            $course_price_type = learndash_get_setting( $course->ID, 'course_price_type' );
            $course_plan_id = 'learndash-course-' . $course->ID;
            $course_interval_count = get_post_meta( $course->ID, 'course_price_billing_p3', true );
            $course_interval       = get_post_meta( $course->ID, 'course_price_billing_t3', true );
        } elseif ( learndash_get_post_type_slug( 'group' ) === $course->post_type ) {
            $course_price      = learndash_get_setting( $course->ID, 'group_price' );
            $course_price_type = learndash_get_setting( $course->ID, 'group_price_type' );
            $course_plan_id = 'learndash-group-' . $course->ID;
            $course_interval_count = get_post_meta( $course->ID, 'group_price_billing_p3', true );
            $course_interval       = get_post_meta( $course->ID, 'group_price_billing_t3', true );
        }

        switch ( $course_interval ) {
            case 'D':
                $course_interval = 'day';
                break;

            case 'W':
                $course_interval = 'week';
                break;

            case 'M':
                $course_interval = 'month';
                break;

            case 'Y':
                $course_interval = 'year';
                break;
        }

        $currency       = strtolower( $this->options['currency'] );
        $course_image   = get_the_post_thumbnail_url( $course->ID, 'medium' );
        $course_name    = $course->post_title;
        $course_id      = $course->ID;

        $course_price = preg_replace( '/.*?(\d+(?:\.?\d+))/', '$1', $course_price );

        if ( ! $this->is_zero_decimal_currency( $this->options['currency'] ) ) {
            $course_price = $course_price * 100;
        }

        $args = compact( 'user_id', 'user_email', 'course_id', 'currency', 'course_image', 'course_name', 'course_plan_id', 'course_price', 'course_price_type', 'course_interval', 'course_interval_count' );

        return $args;
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'jquery' );
    }

    /**
     * Output modified payment button
     * @param  string $default_button Learndash default payment button
     * @param  array  $params         Button parameters
     * @return string                 Modified button
     */
    public function payment_button( $default_button, $params = null ) {
        if ( $this->key_is_empty() || empty( $this->endpoint_secret ) ) {
            return $default_button;
        }

        // Also ensure the price it not zero
        if ( ( ! isset( $params['price'] ) ) || ( empty( $params['price'] ) ) ) {
            return $default_button;
        }

        $this->default_button = $default_button;

        if ( isset( $params['post'] ) ) {
            $this->course = $params['post'];
        } else {
            $this->course = get_post( get_the_ID() );
        }

        $this->stripe_button = $this->stripe_button();

        if ( ! empty( $this->stripe_button ) ) {
            return $default_button . $this->stripe_button;
        } else {
            return $default_button;
        }
    }

    /**
     * Stripe payment button
     * @return string         Payment button
     */
    public function stripe_button() {
        global $learndash_stripe_script_loaded;

        if ( ! isset( $learndash_stripe_script_loaded ) ) {
            $learndash_stripe_script_loaded = false;
        }

        extract( $this->get_course_args() );

        ob_start();
        
        if ( $this->is_paypal_active() ) {
            $stripe_button_text  = apply_filters( 'learndash_stripe_purchase_button_text', __( 'Use a Credit Card', 'learndash-stripe' ) );     
        } else {
            if ( class_exists( 'LearnDash_Custom_Label' ) ) {
                if ( $this->course->post_type === 'sfwd-courses' ) {
                    $stripe_button_text  = apply_filters( 'learndash_stripe_purchase_button_text', LearnDash_Custom_Label::get_label( 'button_take_this_course' ) );
                } elseif ( $this->course->post_type === 'groups' ) {
                    $stripe_button_text  = apply_filters( 'learndash_stripe_purchase_button_text', LearnDash_Custom_Label::get_label( 'button_take_this_group' ) );
                }
            } else {
                if ( $this->course->post_type === 'sfwd-courses' ) {
                    $stripe_button_text  = apply_filters( 'learndash_stripe_purchase_button_text', __( 'Take This Course', 'learndash-stripe' ) );
                } elseif ( $this->course->post_type === 'groups' ) {
                    $stripe_button_text  = apply_filters( 'learndash_stripe_purchase_button_text', __( 'Enroll in Group', 'learndash-stripe' ) );
                }
            }
        }

        $stripe_button = '';
        $stripe_button .= '<div class="learndash_checkout_button learndash_stripe_button">';
            $stripe_button .= '<form class="learndash-stripe-checkout" name="" action="" method="post">';
            $stripe_button .= '<input type="hidden" name="action" value="ld_stripe_init_checkout" />';
            $stripe_button .= '<input type="hidden" name="stripe_email" value="' . esc_attr( $user_email ) . '" />';
            $stripe_button .= '<input type="hidden" name="stripe_user_id" value="' . esc_attr( $user_id ) . '" />';
            $stripe_button .= '<input type="hidden" name="stripe_course_id" value="' . esc_attr( $course_id ) . '" />';
            $stripe_button .= '<input type="hidden" name="stripe_plan_id" value="' . esc_attr( $course_plan_id ) . '" />';
            $stripe_button .= '<input type="hidden" name="stripe_name" value="' . esc_attr( $course_name ) . '" />';
            $stripe_button .= '<input type="hidden" name="stripe_currency" value="' . esc_attr( $currency ) . '" />';
            $stripe_button .= '<input type="hidden" name="stripe_price" value="' . esc_attr( $course_price ) . '" />';
            $stripe_button .= '<input type="hidden" name="stripe_price_type" value="' . esc_attr( $course_price_type ) . '" />';
            
            if ( 'subscribe' == $course_price_type ) {
                $stripe_button .= '<input type="hidden" name="stripe_interval_count" value="' . esc_attr( $course_interval_count ) . '" />';
                $stripe_button .= '<input type="hidden" name="stripe_interval" value="' . esc_attr( $course_interval ) . '" />';
            }

            $stripe_button_nonce = wp_create_nonce( 'stripe-nonce-' . $course_id . $course_price . $course_price_type );
            $stripe_button .= '<input type="hidden" name="stripe_nonce" value="' . esc_attr( $stripe_button_nonce ) . '" />';

            $stripe_button .= '<input class="learndash-stripe-checkout-button btn-join button" type="submit" value="'. esc_attr( $stripe_button_text ) .'">';
            $stripe_button .= '</form>';
        $stripe_button .= '</div>';
        
        ?>
        <style type="text/css">
            .learndash-error {
                color: red;
                font-weight: bold;
            }
            .learndash-success {
                color: green;
                font-weight: bold;
            }

            .checkout-dropdown-button .learndash_checkout_button .btn-join {
                background-color: #fff !important;
                color: #000 !important; 
                font-weight: normal !important;
                font-size: 16px !important;
            }

            .checkout-dropdown-button .learndash_checkout_button .btn-join:hover {
                background-color: #F5F5F5 !important;
                color: #000 !important;
            }

            /* Style for the dropdown menu Stripe button */
            .checkout-dropdown-button .learndash-stripe-checkout-button {
                border: 0px;
                border-radius: 0 !important;
                display: inline-block;
                font-size: 14px !important;
                margin: 0;
                text-align: center;
                width: 100%;
            }
        </style>
        <?php

        if ( ! $learndash_stripe_script_loaded ) {
            $this->button_scripts();
            $learndash_stripe_script_loaded = true;
        }
        
        $stripe_button .= ob_get_clean();

        return $stripe_button;
    }

    /**
     * Specific integration scripts
     * @return void
     */
    abstract function button_scripts();

    /**
     * Check if Stripe transaction is legit
     * @param  array  $post     Transaction form submit $_POST
     * @return boolean          True if legit, false otherwise
     */
    public function is_transaction_legit() {
        if ( wp_verify_nonce( $_POST['stripe_nonce'], 'stripe-nonce-' . $_POST['stripe_course_id'] . $_POST['stripe_price'] . $_POST['stripe_price_type'] ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process Stripe new checkout
     * @return void
     */
    public function process_webhook() {
        if ( ! isset( $_GET['learndash-integration'] ) || $_GET['learndash-integration'] != 'stripe' ) {
            return;
        }

        $this->config();

        $payload = @file_get_contents( 'php://input' );
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $event = null;

        if ( empty( $payload ) || empty( $sig_header ) ) {
            exit();
        }

        if ( ! apply_filters( 'learndash_stripe_process_webhook', true, json_decode( $payload ) ) ) {
            return;
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $this->endpoint_secret
            );
        } catch( \Exception $e ) {
            $error_message = __( 'Error ', 'learndash-stripe' ) . $e->getCode() . ': - ' . $e->getMessage();
            error_log( 'LearnDash Stripe Error: ' . print_r( $error_message, true ) );
            wp_die( $error_message, __( 'Error', 'learndash-stripe' ), $e->getCode() );
            exit();
        } 

        $session = $event->data->object;

        try {
            if ( ! empty( $session->customer ) ) {
                $customer = \Stripe\Customer::retrieve( $session->customer );
            } else {
                http_response_code(200);
                exit();
            }
        } catch ( \Exception $e) {
            $error_message = __( 'Error ', 'learndash-stripe' ) . $e->getCode() . ': - ' . $e->getMessage();
            error_log( 'LearnDash Stripe Error: ' . print_r( $error_message, true ) );
            http_response_code(200);
            exit();
        }

        $user_id = null;

        $client_reference_id = explode( ';', $session->client_reference_id );
        foreach ( $client_reference_id as $value ) {
            if ( strpos( $value, 'user_id' ) !== false ) {
                preg_match( '/user_id=(\d+)/', $value, $match );
                $user_id = $match[1];
            }
        }

        $email_address = $customer->email;
        $user_id = $this->get_user( $email_address, $customer->id, $user_id );

        // Handle the checkout.session.completed event
        if ( $event->type == 'checkout.session.completed' ) {
            $course_id = null;
            foreach ( $client_reference_id as $value ) {
                if ( strpos( $value, 'course_id' ) !== false ) {
                    preg_match( '/course_id=(\d+)/', $value, $match );
                    $course_id = $match[1];
                }
            }

            // Associate course with user
            $this->add_course_access( $course_id, $user_id );

            if ( ! $this->is_zero_decimal_currency( $session['stripe_currency'] ) && $session['stripe_price'] > 0 ) { 
                $session['stripe_price'] = number_format( $session['stripe_price'] / 100, 2 );
            }

            // Log transaction
            $this->record_transaction( $session, $course_id, $user_id, $email_address );
        } elseif ( $event->type == 'invoice.payment_succeeded' || $event->type == 'invoice.paid' ) {
            foreach ( $session->lines->data as $item ) {
                $plan_id   = $item->plan->id;
                $course_id = $this->get_course_id_by_plan_id( $plan_id );
                if ( ! empty( $course_id ) ) {
                    $this->add_course_access( $course_id, $user_id );
                }
            }
        } elseif ( $event->type == 'invoice.payment_failed' ) {
            foreach ( $session->lines->data as $item ) {
                $plan_id   = $item->plan->id;
                $course_id = $this->get_course_id_by_plan_id( $plan_id );
                if ( ! empty( $course_id ) ) {
                    $this->remove_course_access( $course_id, $user_id );
                }
            }
        } elseif ( $event->type == 'customer.subscription.deleted' ) {
            foreach ( $session->items->data as $item ) {
                $plan_id   = $item->plan->id;
                $course_id = $this->get_course_id_by_plan_id( $plan_id );
                if ( ! empty( $course_id ) ) {
                    $this->remove_course_access( $course_id, $user_id );
                }
            }
        }

        http_response_code( 200 );
        exit();
    }

    /**
     * Get LearnDash course ID by Stripe plan ID
     * @param  string $plan_id Stripe plan ID
     * @return int|empty       LearnDash course ID or empty
     */
    public function get_course_id_by_plan_id( $plan_id ) {
        global $wpdb;
        $course_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'stripe_plan_id' AND meta_value = %s", $plan_id ) );
        return $course_id;
    }

    abstract public function output_transaction_message();

    /**
     * Get user ID of the customer
     * @param  string $email       User email address
     * @param  string $customer_id Stripe customer ID
     * @param  int    $user_id     WP user ID
     * @return int                 WP_User ID
     */
    public function get_user( $email, $customer_id, $user_id = null ) {
        if ( ! empty( $user_id ) && is_numeric( $user_id ) ) {
            $user = get_user_by( 'ID', $user_id );
        } else {
            $user = get_user_by( 'email', $email );
        }

        if ( false === $user ) {
            $password = wp_generate_password( 18, true, false );
            $new_user = $this->create_user( $email, $password, $email );

            if ( ! is_wp_error( $new_user ) ) {
                $user_id     = $new_user;
                $user        = get_user_by( 'ID', $user_id );
                update_user_meta( $user_id, $this->stripe_customer_id_meta_key, $customer_id );

                // Need to allow for older versions of WP. 
                global $wp_version;
                if ( version_compare( $wp_version, '4.3.0', '<' ) ) {
                    wp_new_user_notification( $user_id, $password );
                } else if ( version_compare( $wp_version, '4.3.0', '==' ) ) {
                    wp_new_user_notification( $user_id, 'both' );                       
                } else if ( version_compare( $wp_version, '4.3.1', '>=' ) ) {
                    wp_new_user_notification( $user_id, null, 'both' );
                }
            }
        } else {
            $user_id = $user->ID;
            update_user_meta( $user_id, $this->stripe_customer_id_meta_key, $customer_id );
        }

        return $user_id;
    }

    /**
     * Create user if not exists
     * 
     * @param  string $username 
     * @param  string $password 
     * @return int               Newly created user ID
     */
    public function create_user( $email, $password, $username ) {
        if ( apply_filters( 'learndash_stripe_create_short_username', false ) ) {
            $username = preg_replace( '/(.*)\@(.*)/', '$1', $email );
        }

        if ( username_exists( $username ) ) {
            $random_chars = str_shuffle( substr( md5( time() ), 0, 5 ) );
            $username = $username . '-' . $random_chars;
        }

        $user_id = wp_create_user( $username, $password, $email );

        do_action( 'learndash_stripe_after_create_user', $user_id );

        return $user_id;
    }

    /**
     * Associate course with user
     * @param  int $course_id Post ID of a course
     * @param  int $user_id   ID of a user
     */
    public function add_course_access( $course_id, $user_id ) {
        $course_id = absint( $course_id );
        $user_id   = absint( $user_id );

        if ( ( ! empty( $course_id ) ) && ( ! empty( $user_id ) ) ) {
            if ( learndash_get_post_type_slug( 'course' ) === get_post_type( $course_id ) ) {
                ld_update_course_access( $user_id, $course_id );
            } elseif ( learndash_get_post_type_slug( 'group' ) === get_post_type( $course_id ) ) {
                ld_update_group_access( $user_id, $course_id );
            }
        }
    }

    /**
     * Remove course access from user
     * @param  int    $course_id LearnDash course ID
     * @param  int    $user_id   User ID
     */
    public function remove_course_access( $course_id, $user_id ) {
        $course_id = absint( $course_id );
        $user_id   = absint( $user_id );

        if ( ( ! empty( $course_id ) ) && ( ! empty( $user_id ) ) ) {
            if ( learndash_get_post_type_slug( 'course' ) === get_post_type( $course_id ) ) {
                ld_update_course_access( $user_id, $course_id, true );
            } elseif ( learndash_get_post_type_slug( 'group' ) === get_post_type( $course_id ) ) {
                ld_update_group_access( $user_id, $course_id, true );
            }
        }
    }

    abstract function record_transaction( $transaction, $course_id, $user_id, $user_email );

    /**
     * Set secret key used in this class
     */
    public function set_secret_key() {
        if ( isset( $this->options['test_mode'] ) && 1 == $this->options['test_mode'] && ! empty( $this->options['secret_key_test'] ) ) {
            $key = $this->options['secret_key_test'];

        } elseif ( ( ! isset( $this->options['test_mode'] ) || 1 != $this->options['test_mode'] ) && ! empty( $this->options['secret_key_live']) ) {
            $key = $this->options['secret_key_live'];

        } else {
            return $key = '';
        }       

        return $key;
    }

    /**
     * Return secret key used in this class
     */
    public function get_secret_key() {
        return $this->set_secret_key();
    }

    /**
     * Set publishable key used in this class
     */
    public function set_publishable_key() {
        if ( isset( $this->options['test_mode'] ) && 1 == $this->options['test_mode'] && ! empty( $this->options['publishable_key_test'] ) ) {

            $key = $this->options['publishable_key_test'];

        } elseif ( ( ! isset( $this->options['test_mode'] ) || 1 != $this->options['test_mode'] ) && ! empty( $this->options['publishable_key_live'] ) ) {

            $key = $this->options['publishable_key_live'];

        } else {
            return $key = '';
        }       

        return $key;
    }

    /**
     * Return publishable key used in this class
     */
    public function get_publishable_key() {
        return $this->set_publishable_key();
    }

    /**
     * Set endpoint secret used in this class
     */
    public function set_endpoint_secret() {
        if ( isset( $this->options['test_mode'] ) && 1 == $this->options['test_mode'] && ! empty( $this->options['endpoint_secret_test'] ) ) {

            $key = $this->options['endpoint_secret_test'];

        } elseif ( ( ! isset( $this->options['test_mode'] ) || 1 != $this->options['test_mode'] ) && ! empty( $this->options['endpoint_secret'] ) ) {

            $key = $this->options['endpoint_secret'];

        } else {
            return $key = '';
        }

        return $key;
    }

    /**
     * Return endpoint secret used in this class
     */
    public function get_endpoint_secret() {
        return $this->set_endpoint_secret();
    }

    /**
     * Get enabled payment methods
     * @return array Enabled payment methods
     */
    public function get_payment_methods() {
        // We must assume payment_methods index doesn't exist yet
        $payment_methods = $this->options['payment_methods'] ?? array( 'card' => 1 );
        $enabled_payment_methods = array();
        foreach ( $payment_methods as $key => $enabled ) {
            if ( $enabled ) {
                $enabled_payment_methods[] = $key;
            }
        }

        return apply_filters( 'learndash_stripe_payment_method_types', $enabled_payment_methods );
    }

    /**
     * Check if key is empty
     * @return bool True if empty, false otherwise
     */
    public function key_is_empty() {
        if ( empty( $this->secret_key ) || empty( $this->publishable_key ) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if PayPal is used or not.
     * @return boolean True if active, false otherwise.
     */
    public function is_paypal_active() {
        if ( version_compare( LEARNDASH_VERSION, '2.4.0', '<' ) ) {
            $ld_options   = learndash_get_option( 'sfwd-courses' );
            $paypal_email = isset( $ld_options['paypal_email'] ) ? $ld_options['paypal_email'] : '';
        } else {
            $paypal_email = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_PayPal', 'paypal_email' );
        }

        if ( ! empty( $paypal_email ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if Stripe currency ISO code is zero decimal currency
     * 
     * @param  string  $currency Stripe currency ISO code
     * @return boolean           True if zero decimal|false otherwise
     */
    public function is_zero_decimal_currency( $currency = '' ) {
        $currency = strtoupper( $currency );

        $zero_decimal_currencies = array(
            'BIF',
            'CLP',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'MGA',
            'PYG',
            'RWF',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF',
        );

        if ( in_array( $currency, $zero_decimal_currencies ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate random string
     * @param  integer $length Length of the random string
     * @return string          Random string
     */
    public function generate_random_string(  $length = 3 ) {
        return substr( md5( microtime() ), 0, $length );
    }
}