<?php
namespace EasyAffiliate\Lib\Paypal;

use EasyAffiliate\Models\Options;
use PaypalPayoutsSDK\Core\PayPalHttpClient;
use PaypalPayoutsSDK\Core\ProductionEnvironment;
use PaypalPayoutsSDK\Core\SandboxEnvironment;

class PayPalClient
{
  /**
   * Returns PayPal HTTP client instance with environment which has access
   * credentials context. This can be used invoke PayPal API's provided the
   * credentials have the access to do so.
   */
  public static function client()
  {
    return new PayPalHttpClient(self::environment());
  }

  /**
   * Setting up and Returns PayPal SDK environment with PayPal Access credentials.
   * For demo purpose, we are using SandboxEnvironment. In production this will be
   * ProductionEnvironment.
   */
  public static function environment()
  {
    $options = Options::fetch();
    $clientId = getenv("ESAF_PAYPAL_CLIENT_ID") ?: $options->paypal_client_id;
    $clientSecret = getenv("ESAF_PAYPAL_CLIENT_SECRET") ?: $options->paypal_secret_id;

    if (apply_filters('esaf_paypal_environment', 'prod', $clientId, $clientSecret) === 'prod') {
      return new ProductionEnvironment($clientId, $clientSecret);
    } else {
      return new SandboxEnvironment($clientId, $clientSecret);
    }
  }
}
