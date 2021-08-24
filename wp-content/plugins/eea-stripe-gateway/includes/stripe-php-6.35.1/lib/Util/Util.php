<?php

namespace EE_Stripe_6_35_1\Util;

use EE_Stripe_6_35_1\StripeObject;

abstract class Util
{
    private static $isMbstringAvailable = null;
    private static $isHashEqualsAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     * @return boolean true if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }
        if ($array === []) {
            return true;
        }
        if (array_keys($array) !== range(0, count($array) - 1)) {
            return false;
        }
        return true;
    }

    /**
     * Recursively converts the PHP Stripe object to an array.
     *
     * @param array $values The PHP Stripe object to convert.
     * @return array
     */
    public static function convertStripeObjectToArray($values)
    {
        $results = [];
        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if ($k[0] == '_') {
                continue;
            }
            if ($v instanceof StripeObject) {
                $results[ $k ] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[ $k ] = self::convertStripeObjectToArray($v);
            } else {
                $results[ $k ] = $v;
            }
        }
        return $results;
    }

    /**
     * Converts a response from the Stripe API to the corresponding PHP object.
     *
     * @param array $resp The response from the Stripe API.
     * @param array $opts
     * @return StripeObject|array
     */
    public static function convertToStripeObject($resp, $opts)
    {
        $types = [
            // data structures
            \EE_Stripe_6_35_1\Collection::OBJECT_NAME => 'EE_Stripe_6_35_1\\Collection',

            // business objects
            \EE_Stripe_6_35_1\Account::OBJECT_NAME => 'EE_Stripe_6_35_1\\Account',
            \EE_Stripe_6_35_1\AccountLink::OBJECT_NAME => 'EE_Stripe_6_35_1\\AccountLink',
            \EE_Stripe_6_35_1\AlipayAccount::OBJECT_NAME => 'EE_Stripe_6_35_1\\AlipayAccount',
            \EE_Stripe_6_35_1\ApplePayDomain::OBJECT_NAME => 'EE_Stripe_6_35_1\\ApplePayDomain',
            \EE_Stripe_6_35_1\ApplicationFee::OBJECT_NAME => 'EE_Stripe_6_35_1\\ApplicationFee',
            \EE_Stripe_6_35_1\Balance::OBJECT_NAME => 'EE_Stripe_6_35_1\\Balance',
            \EE_Stripe_6_35_1\BalanceTransaction::OBJECT_NAME => 'EE_Stripe_6_35_1\\BalanceTransaction',
            \EE_Stripe_6_35_1\BankAccount::OBJECT_NAME => 'EE_Stripe_6_35_1\\BankAccount',
            \EE_Stripe_6_35_1\BitcoinReceiver::OBJECT_NAME => 'EE_Stripe_6_35_1\\BitcoinReceiver',
            \EE_Stripe_6_35_1\BitcoinTransaction::OBJECT_NAME => 'EE_Stripe_6_35_1\\BitcoinTransaction',
            \EE_Stripe_6_35_1\Capability::OBJECT_NAME => 'EE_Stripe_6_35_1\\Capability',
            \EE_Stripe_6_35_1\Card::OBJECT_NAME => 'EE_Stripe_6_35_1\\Card',
            \EE_Stripe_6_35_1\Charge::OBJECT_NAME => 'EE_Stripe_6_35_1\\Charge',
            \EE_Stripe_6_35_1\Checkout\Session::OBJECT_NAME => 'EE_Stripe_6_35_1\\Checkout\\Session',
            \EE_Stripe_6_35_1\CountrySpec::OBJECT_NAME => 'EE_Stripe_6_35_1\\CountrySpec',
            \EE_Stripe_6_35_1\Coupon::OBJECT_NAME => 'EE_Stripe_6_35_1\\Coupon',
            \EE_Stripe_6_35_1\CreditNote::OBJECT_NAME => 'EE_Stripe_6_35_1\\CreditNote',
            \EE_Stripe_6_35_1\Customer::OBJECT_NAME => 'EE_Stripe_6_35_1\\Customer',
            \EE_Stripe_6_35_1\Discount::OBJECT_NAME => 'EE_Stripe_6_35_1\\Discount',
            \EE_Stripe_6_35_1\Dispute::OBJECT_NAME => 'EE_Stripe_6_35_1\\Dispute',
            \EE_Stripe_6_35_1\EphemeralKey::OBJECT_NAME => 'EE_Stripe_6_35_1\\EphemeralKey',
            \EE_Stripe_6_35_1\Event::OBJECT_NAME => 'EE_Stripe_6_35_1\\Event',
            \EE_Stripe_6_35_1\ExchangeRate::OBJECT_NAME => 'EE_Stripe_6_35_1\\ExchangeRate',
            \EE_Stripe_6_35_1\ApplicationFeeRefund::OBJECT_NAME => 'EE_Stripe_6_35_1\\ApplicationFeeRefund',
            \EE_Stripe_6_35_1\File::OBJECT_NAME => 'EE_Stripe_6_35_1\\File',
            \EE_Stripe_6_35_1\File::OBJECT_NAME_ALT => 'EE_Stripe_6_35_1\\File',
            \EE_Stripe_6_35_1\FileLink::OBJECT_NAME => 'EE_Stripe_6_35_1\\FileLink',
            \EE_Stripe_6_35_1\Invoice::OBJECT_NAME => 'EE_Stripe_6_35_1\\Invoice',
            \EE_Stripe_6_35_1\InvoiceItem::OBJECT_NAME => 'EE_Stripe_6_35_1\\InvoiceItem',
            \EE_Stripe_6_35_1\InvoiceLineItem::OBJECT_NAME => 'EE_Stripe_6_35_1\\InvoiceLineItem',
            \EE_Stripe_6_35_1\IssuerFraudRecord::OBJECT_NAME => 'EE_Stripe_6_35_1\\IssuerFraudRecord',
            \EE_Stripe_6_35_1\Issuing\Authorization::OBJECT_NAME => 'EE_Stripe_6_35_1\\Issuing\\Authorization',
            \EE_Stripe_6_35_1\Issuing\Card::OBJECT_NAME => 'EE_Stripe_6_35_1\\Issuing\\Card',
            \EE_Stripe_6_35_1\Issuing\CardDetails::OBJECT_NAME => 'EE_Stripe_6_35_1\\Issuing\\CardDetails',
            \EE_Stripe_6_35_1\Issuing\Cardholder::OBJECT_NAME => 'EE_Stripe_6_35_1\\Issuing\\Cardholder',
            \EE_Stripe_6_35_1\Issuing\Dispute::OBJECT_NAME => 'EE_Stripe_6_35_1\\Issuing\\Dispute',
            \EE_Stripe_6_35_1\Issuing\Transaction::OBJECT_NAME => 'EE_Stripe_6_35_1\\Issuing\\Transaction',
            \EE_Stripe_6_35_1\LoginLink::OBJECT_NAME => 'EE_Stripe_6_35_1\\LoginLink',
            \EE_Stripe_6_35_1\Order::OBJECT_NAME => 'EE_Stripe_6_35_1\\Order',
            \EE_Stripe_6_35_1\OrderItem::OBJECT_NAME => 'EE_Stripe_6_35_1\\OrderItem',
            \EE_Stripe_6_35_1\OrderReturn::OBJECT_NAME => 'EE_Stripe_6_35_1\\OrderReturn',
            \EE_Stripe_6_35_1\PaymentIntent::OBJECT_NAME => 'EE_Stripe_6_35_1\\PaymentIntent',
            \EE_Stripe_6_35_1\PaymentMethod::OBJECT_NAME => 'EE_Stripe_6_35_1\\PaymentMethod',
            \EE_Stripe_6_35_1\Payout::OBJECT_NAME => 'EE_Stripe_6_35_1\\Payout',
            \EE_Stripe_6_35_1\Person::OBJECT_NAME => 'EE_Stripe_6_35_1\\Person',
            \EE_Stripe_6_35_1\Plan::OBJECT_NAME => 'EE_Stripe_6_35_1\\Plan',
            \EE_Stripe_6_35_1\Product::OBJECT_NAME => 'EE_Stripe_6_35_1\\Product',
            \EE_Stripe_6_35_1\Radar\ValueList::OBJECT_NAME => 'EE_Stripe_6_35_1\\Radar\\ValueList',
            \EE_Stripe_6_35_1\Radar\ValueListItem::OBJECT_NAME => 'EE_Stripe_6_35_1\\Radar\\ValueListItem',
            \EE_Stripe_6_35_1\Recipient::OBJECT_NAME => 'EE_Stripe_6_35_1\\Recipient',
            \EE_Stripe_6_35_1\RecipientTransfer::OBJECT_NAME => 'EE_Stripe_6_35_1\\RecipientTransfer',
            \EE_Stripe_6_35_1\Refund::OBJECT_NAME => 'EE_Stripe_6_35_1\\Refund',
            \EE_Stripe_6_35_1\Reporting\ReportRun::OBJECT_NAME => 'EE_Stripe_6_35_1\\Reporting\\ReportRun',
            \EE_Stripe_6_35_1\Reporting\ReportType::OBJECT_NAME => 'EE_Stripe_6_35_1\\Reporting\\ReportType',
            \EE_Stripe_6_35_1\Review::OBJECT_NAME => 'EE_Stripe_6_35_1\\Review',
            \EE_Stripe_6_35_1\SKU::OBJECT_NAME => 'EE_Stripe_6_35_1\\SKU',
            \EE_Stripe_6_35_1\Sigma\ScheduledQueryRun::OBJECT_NAME => 'EE_Stripe_6_35_1\\Sigma\\ScheduledQueryRun',
            \EE_Stripe_6_35_1\Source::OBJECT_NAME => 'EE_Stripe_6_35_1\\Source',
            \EE_Stripe_6_35_1\SourceTransaction::OBJECT_NAME => 'EE_Stripe_6_35_1\\SourceTransaction',
            \EE_Stripe_6_35_1\Subscription::OBJECT_NAME => 'EE_Stripe_6_35_1\\Subscription',
            \EE_Stripe_6_35_1\SubscriptionItem::OBJECT_NAME => 'EE_Stripe_6_35_1\\SubscriptionItem',
            \EE_Stripe_6_35_1\SubscriptionSchedule::OBJECT_NAME => 'EE_Stripe_6_35_1\\SubscriptionSchedule',
            \EE_Stripe_6_35_1\SubscriptionScheduleRevision::OBJECT_NAME => 'EE_Stripe_6_35_1\\SubscriptionScheduleRevision',
            \EE_Stripe_6_35_1\TaxId::OBJECT_NAME => 'EE_Stripe_6_35_1\\TaxId',
            \EE_Stripe_6_35_1\TaxRate::OBJECT_NAME => 'EE_Stripe_6_35_1\\TaxRate',
            \EE_Stripe_6_35_1\ThreeDSecure::OBJECT_NAME => 'EE_Stripe_6_35_1\\ThreeDSecure',
            \EE_Stripe_6_35_1\Terminal\ConnectionToken::OBJECT_NAME => 'EE_Stripe_6_35_1\\Terminal\\ConnectionToken',
            \EE_Stripe_6_35_1\Terminal\Location::OBJECT_NAME => 'EE_Stripe_6_35_1\\Terminal\\Location',
            \EE_Stripe_6_35_1\Terminal\Reader::OBJECT_NAME => 'EE_Stripe_6_35_1\\Terminal\\Reader',
            \EE_Stripe_6_35_1\Token::OBJECT_NAME => 'EE_Stripe_6_35_1\\Token',
            \EE_Stripe_6_35_1\Topup::OBJECT_NAME => 'EE_Stripe_6_35_1\\Topup',
            \EE_Stripe_6_35_1\Transfer::OBJECT_NAME => 'EE_Stripe_6_35_1\\Transfer',
            \EE_Stripe_6_35_1\TransferReversal::OBJECT_NAME => 'EE_Stripe_6_35_1\\TransferReversal',
            \EE_Stripe_6_35_1\UsageRecord::OBJECT_NAME => 'EE_Stripe_6_35_1\\UsageRecord',
            \EE_Stripe_6_35_1\UsageRecordSummary::OBJECT_NAME => 'EE_Stripe_6_35_1\\UsageRecordSummary',
            \EE_Stripe_6_35_1\WebhookEndpoint::OBJECT_NAME => 'EE_Stripe_6_35_1\\WebhookEndpoint',
        ];
        if (self::isList($resp)) {
            $mapped = [];
            foreach ($resp as $i) {
                array_push($mapped, self::convertToStripeObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset($types[ $resp['object'] ])) {
                $class = $types[ $resp['object'] ];
            } else {
                $class = 'EE_Stripe_6_35_1\\StripeObject';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (self::$isMbstringAvailable === null) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                trigger_error("It looks like the mbstring extension is not enabled. " .
                    "UTF-8 strings will not properly be encoded. Ask your system " .
                    "administrator to enable the mbstring extension, or write to " .
                    "support@stripe.com if you have any questions.", E_USER_WARNING);
            }
        }

        if (is_string($value) && self::$isMbstringAvailable && mb_detect_encoding($value, "UTF-8", true) != "UTF-8") {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }

    /**
     * Compares two strings for equality. The time taken is independent of the
     * number of characters that match.
     *
     * @param string $a one of the strings to compare.
     * @param string $b the other string to compare.
     * @return bool true if the strings are equal, false otherwise.
     */
    public static function secureCompare($a, $b)
    {
        if (self::$isHashEqualsAvailable === null) {
            self::$isHashEqualsAvailable = function_exists('hash_equals');
        }

        if (self::$isHashEqualsAvailable) {
            return hash_equals($a, $b);
        } else {
            if (strlen($a) != strlen($b)) {
                return false;
            }

            $result = 0;
            for ($i = 0; $i < strlen($a); $i++) {
                $result |= ord($a[ $i ]) ^ ord($b[ $i ]);
            }
            return ($result == 0);
        }
    }

    /**
     * Recursively goes through an array of parameters. If a parameter is an instance of
     * ApiResource, then it is replaced by the resource's ID.
     * Also clears out null values.
     *
     * @param mixed $h
     * @return mixed
     */
    public static function objectsToIds($h)
    {
        if ($h instanceof \EE_Stripe_6_35_1\ApiResource) {
            return $h->id;
        } elseif (static::isList($h)) {
            $results = [];
            foreach ($h as $v) {
                array_push($results, static::objectsToIds($v));
            }
            return $results;
        } elseif (is_array($h)) {
            $results = [];
            foreach ($h as $k => $v) {
                if (is_null($v)) {
                    continue;
                }
                $results[ $k ] = static::objectsToIds($v);
            }
            return $results;
        } else {
            return $h;
        }
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function encodeParameters($params)
    {
        $flattenedParams = self::flattenParams($params);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            list($k, $v) = $param;
            array_push($pieces, self::urlEncode($k) . '=' . self::urlEncode($v));
        }
        return implode('&', $pieces);
    }

    /**
     * @param array $params
     * @param string|null $parentKey
     *
     * @return array
     */
    public static function flattenParams($params, $parentKey = null)
    {
        $result = [];

        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? "{$parentKey}[{$key}]" : $key;

            if (self::isList($value)) {
                $result = array_merge($result, self::flattenParamsList($value, $calculatedKey));
            } elseif (is_array($value)) {
                $result = array_merge($result, self::flattenParams($value, $calculatedKey));
            } else {
                array_push($result, [$calculatedKey, $value]);
            }
        }

        return $result;
    }

    /**
     * @param array $value
     * @param string $calculatedKey
     *
     * @return array
     */
    public static function flattenParamsList($value, $calculatedKey)
    {
        $result = [];

        foreach ($value as $i => $elem) {
            if (self::isList($elem)) {
                $result = array_merge($result, self::flattenParamsList($elem, $calculatedKey));
            } elseif (is_array($elem)) {
                $result = array_merge($result, self::flattenParams($elem, "{$calculatedKey}[{$i}]"));
            } else {
                array_push($result, ["{$calculatedKey}[{$i}]", $elem]);
            }
        }

        return $result;
    }

    /**
     * @param string $key A string to URL-encode.
     *
     * @return string The URL-encoded string.
     */
    public static function urlEncode($key)
    {
        $s = urlencode($key);

        // Don't use strict form encoding by changing the square bracket control
        // characters back to their literals. This is fine by the server, and
        // makes these parameter strings easier to read.
        $s = str_replace('%5B', '[', $s);
        $s = str_replace('%5D', ']', $s);

        return $s;
    }

    public static function normalizeId($id)
    {
        if (is_array($id)) {
            $params = $id;
            $id = $params['id'];
            unset($params['id']);
        } else {
            $params = [];
        }
        return [$id, $params];
    }

    /**
     * Returns UNIX timestamp in milliseconds
     *
     * @return integer current time in millis
     */
    public static function currentTimeMillis()
    {
        return (int) round(microtime(true) * 1000);
    }
}
