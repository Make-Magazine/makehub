<?php
namespace Indeed\Ihc;

class Checkout
{
  /**
   * @var int
   */
    private $lid = 0;
    /**
     * @var int
     */
    private $uid = 0;

    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        add_shortcode( 'ihc-checkout-page', [ $this, 'output' ] );

        add_action( 'wp_ajax_checkout_subscription_details', [ $this, 'subscriptionDetails' ] );
        add_action( 'wp_ajax_nopriv_checkout_subscription_details', [ $this, 'subscriptionDetails' ] );
    }

    /**
     * @param int
     * @return object
     */
    public function setLid( $lid=0 )
    {
        $this->lid = $lid;
        return $this;
    }

    /**
     * @param int
     * @return object
     */
    public function setUid( $uid=0 )
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @param none
     * @return array
     */
    public function setFields()
    {
        $fields = ihc_get_user_reg_fields();
        if ( !$fields ){
            return [];
        }
        $displayType = empty( $this->uid ) ? 'display_public_ap' : 'display_public_reg';
        $targetFields = [ 'ihc_dynamic_price', 'payment_select', 'ihc_coupon' ];
        foreach ( $fields as $key => $field ){
            if ( $field[ $displayType ] && in_array( $field['name'], $targetFields ) !== false ){
                $returnFields[ $field['name'] ] = [
                    "label"       => $field['label'],
                    "type"        => $field['type'],
                    "native_wp"   => $field['native_wp'],
                    'req'         => $field['req'],
                    'sublevel'    => $field['sublevel']
                ];
            }
        }
        return $returnFields;
    }

    /**
     * @param array
     * @return string
     */
    public function output( $args=[] )
    {
        // set the user id
        global $current_user;
        if ( $this->uid === 0 && isset( $current_user->ID ) ){
            $this->uid = $current_user->ID;
        }

        // set the level id
        if ( $this->lid === 0 && isset( $_GET['lid'] ) ){
            $this->lid = $_GET['lid'];
        }
        if ( $this->lid === 0 && isset( $_POST['lid'] ) ){
            $this->lid = $_POST['lid'];
        }
        if ( $this->lid === 0 && isset( $args['lid'] ) ){
            $this->lid = $args['lid'];
        }

        // if we don't have level id out
        if ( $this->lid === 0 ){
            return '';
        }

        // set the fields for checkout
        $fields = $this->setFields();

        // level details and settings
        $levelData = \Indeed\Ihc\Db\Memberships::getOne( $this->lid );

        // payment services
        $paymentsServices = $this->getPaymentServices();

        // payment select settings
        $registerFieldsData = ihc_get_user_reg_fields();
        $key = ihc_array_value_exists( $registerFieldsData, 'payment_select', 'name' );
        if ( $key !== false ){
            $selectPaymentSettings = $registerFieldsData[$key];
        }

        // params
        $params = [
                            'lid'                       => $this->lid,
                            'uid'                       => $this->uid,
                            'fields'                    => $fields,
                            'levelData'                 => $levelData,
                            'paymentServices'           => $paymentsServices,
                            'defaultPayment'            => get_option('ihc_payment_selected'),
                            'showTaxes'                 => 1,
                            'showPrivacyPolicy'         => 1,
                            'privacyPolicyMessage'      => 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="#"  target="_blank">privacy policy</a>',
                            'showUserDetails'           => 1,
                            'paymentSelectSettings'     => isset( $selectPaymentSettings ) ? $selectPaymentSettings : [],
        ];

        // returing the output
        $view = new \Indeed\Ihc\IndeedView();
        return $view->setTemplate( IHC_PATH . 'public/views/checkout-page.php' )
                    ->setContentData( $params )
                    ->getOutput();
    }

    /**
     * @param none
     * @return array
     */
    public function getPaymentServices()
    {
        $allServices = ihc_get_active_payments_services();
        $excludePayments = \Ihc_Db::get_excluded_payment_types_for_level_id( $this->lid );
        if ( empty( $excludePayments ) ){
            return $allServices;
        }
        $excludeArray = explode( ',', $excludePayments );
        foreach ($excludeArray as $ek=>$ev){
            if (isset($allServices[$ev])){
                unset($allServices[$ev]);
            }
        }
        return $allServices;
    }

}
