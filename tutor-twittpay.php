<?php
/**
 * Plugin Name: Tutor LMS - TwittPay Gateway
 * Plugin URI: https://twittpay.com
 * Description: Accept bKash, Nagad, Rocket and Upay payments in Tutor LMS with automatic verification.
 * Version: 3.3.2
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: TwittPay
 * Author URI: https://twittpay.com
 * Text Domain: tutor-twittpay
 *
 * @package TwittPay\Tutor
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

define( 'TUTOR_TWITTPAY_FILE', __FILE__ );
define( 'TUTOR_TWITTPAY_DIR', dirname( TUTOR_TWITTPAY_FILE ) );

require_once TUTOR_TWITTPAY_DIR . '/vendor/autoload.php';

if ( class_exists( \TwittPay\Tutor\Core\Plugin::class ) ) {
\TwittPay\Tutor\Core\Plugin::run();
}
