<?php
/**
 * Registers TwittPay with Tutor LMS.
 *
 * @package TwittPay\Tutor\Gateway
 */

namespace TwittPay\Tutor\Gateway;

use Tutor\Ecommerce\Settings;

final class InitTutor {

public function __construct() {
add_filter( 'tutor_gateways_with_class', __CLASS__ . '::paymentGatewaysWithRef', 10, 2 );
add_filter( 'tutor_payment_gateways_with_class', __CLASS__ . '::filterPaymentGateways' );
add_filter( 'tutor_payment_gateways', array( $this, 'addPaymentMethod' ), 100 );
}

public static function paymentGatewaysWithRef( $value, $gateway ) {
$arr = array(
'twittpay' => array(
'gateway_class' => TwittPayGateway::class,
'config_class'  => TwittPayConfig::class,
),
);

if ( isset( $arr[ $gateway ] ) ) {
$value[ $gateway ] = $arr[ $gateway ];
}

return $value;
}

public static function filterPaymentGateways( $gateways ) {
$arr = array(
'twittpay' => array(
'gateway_class' => TwittPayGateway::class,
'config_class'  => TwittPayConfig::class,
),
);

return $gateways + $arr;
}

public function addPaymentMethod( $methods ) {
$settings = Settings::get_payment_gateway_settings( 'twittpay' );

$methods[] = array(
'name'                 => 'twittpay',
'label'                => isset( $settings['fields'][0]['value'] ) ? $settings['fields'][0]['value'] : 'bKash, Nagad, Rocket, Upay',
'is_installed'         => true,
'is_active'            => true,
'icon'                 => '',
'support_subscription' => true,
'fields'               => array(
array(
'name'  => 'display_name',
'type'  => 'text',
'label' => 'Display Name',
'value' => 'bKash, Nagad, Rocket, Upay',
),
array(
'name'  => 'api_key',
'type'  => 'secret_key',
'label' => 'Brand Key',
'value' => '',
),
),
);

return $methods;
}
}
