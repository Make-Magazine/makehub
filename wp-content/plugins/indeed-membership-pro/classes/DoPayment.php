<?php

namespace Indeed\Ihc;
/*
@since 7.4
*/
class DoPayment
{
  private $attributes         = array();
  private $paymentGateway     = '';
  private $returnUrl          = '';

	public function __construct($params=array(), $paymentGateway='', $returnUrl='')
	{
		  $this->attributes       = $params;
      $this->paymentGateway   = $paymentGateway;
      $this->returnUrl        = $returnUrl;
	}

  public function insertOrder()
  {
      $createOrder = new \Indeed\Ihc\CreateOrder($this->attributes, $this->paymentGateway);
      $this->attributes['orderId'] = $createOrder->proceed()->getOrderId();
      return $this;
  }

	public function processing()
	{
		switch ($this->paymentGateway){
			case 'paypal':
        if (ihc_check_payment_available('paypal')){
            $paymentGatewayObject = new \Indeed\Ihc\PaymentGateways\PayPalStandard();
        }
				break;
			case 'mollie':
        if (ihc_check_payment_available('mollie')){
            $paymentGatewayObject = new \Indeed\Ihc\PaymentGateways\Mollie();
        }
				break;
      case 'paypal_express_checkout':
        if (ihc_check_payment_available('paypal_express_checkout')){
            $paymentGatewayObject = new \Indeed\Ihc\PaymentGateways\PayPalExpressCheckout();
        }
        break;
      case 'pagseguro':
        if (ihc_check_payment_available('pagseguro')){
            $paymentGatewayObject = new \Indeed\Ihc\PaymentGateways\Pagseguro();
        }
        break;
      case 'stripe_checkout_v2':
        if ( ihc_check_payment_available( 'stripe_checkout_v2' ) ){
            $paymentGatewayObject = new \Indeed\Ihc\PaymentGateways\StripeCheckoutV2();
        }
        break;
      default:
        $this->doRedirectBack();
        break;
		}
    if (!empty($paymentGatewayObject)){
        $paymentGatewayObject->setAttributes($this->attributes)->doPayment()->redirect();
    }
	}

  private function doRedirectBack()
  {
      if (empty($this->returnUrl)){
          return;
      }
      wp_redirect($this->returnUrl);
      exit;
  }

}
