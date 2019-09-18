<?php
namespace Indeed\Ihc\PaymentGateways;
/*
@since 7.4
*/
abstract class PaymentAbstract
{

  protected $attributes       = array();
  protected $redirectUrl      = '';
  protected $abort            = false;
  protected $paymentTypeLabel = '';
  protected $currency         = '';
  protected $defaultRedirect  = '';

	abstract public function doPayment();

	abstract public function redirect();

	abstract public function webhook();

  abstract public function cancelSubscription();

  public function setAttributes($params=array())
  {
      $this->attributes = $params;
      $this->setDefaultRedirect();
      return $this;
  }

  protected function addTaxes($amount=0)
  {
      $levels = get_option('ihc_levels');
      $levelData = $levels[$this->attributes['lid']];
      $country = (isset($this->attributes['ihc_country'])) ? $this->attributes['ihc_country'] : '';
      $state = (isset($this->attributes['ihc_state'])) ? $this->attributes['ihc_state'] : '';
      $taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $levelData['price']);
      if ($taxes_price && !empty($taxes_price['total'])){
          $amount += $taxes_price['total'];
      }
      return $amount;
  }

  protected function applyDynamicPrice($amount=0)
  {
      if (ihc_is_magic_feat_active('level_dynamic_price') && isset($this->attributes['ihc_dynamic_price'])){
        $temp_amount = $this->attributes['ihc_dynamic_price'];
        if (ihc_check_dynamic_price_from_user($this->attributes['lid'], $temp_amount)){
          $amount = $temp_amount;
          \Ihc_User_Logs::write_log( __($this->paymentTypeLabel . ': Dynamic price on - Amount is set by the user @ ', 'ihc') . $amount . $this->currency, 'payments');
        }
      }
      return $amount;
  }

  protected function setDefaultRedirect()
  {
      if ($this->attributes['defaultRedirect']){
         $this->defaultRedirect = $this->attributes['defaultRedirect'];
         return;
      }
      $this->defaultRedirect = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $redirect = get_option('ihc_general_register_redirect');
      if (!$redirect || $redirect>-1){
          return;
      }
      $url = get_permalink($redirect);
      if ($url){
          $this->defaultRedirect = $url;
      }
      $url = ihc_get_redirect_link_by_label($redirect, $this->attributes['uid']);
      $url = apply_filters('ihc_register_redirect_filter', $url, $this->attributes['uid'], $this->attributes['lid']);
      if (strpos($url, IHC_PROTOCOL . $_SERVER['HTTP_HOST'] )!==0){
          $this->defaultRedirect = $url;
      }
  }

}
