<?php
namespace Indeed\Ihc;

class Cart
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
        add_shortcode( 'ihc-cart', [ $this, 'output' ] );
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
                continue;
            }
            unset( $fields[$key] );
        }
    }

    /**
     * @param array
     * @return string
     */
    public function output( $args=[] )
    {
        $fields = $this->setFields();
        $view = new \Indeed\Ihc\IndeedView();
        return $view->setTemplate( IHC_PATH . 'public/views/cart-page.php' )
             ->setContentData( [ 'lid' => 0 ] )
             ->getOutput();
    }
}
