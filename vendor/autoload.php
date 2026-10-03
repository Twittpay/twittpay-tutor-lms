<?php
// Small built-in class loader. No Composer needed.
if ( ! defined( 'TWITTPAY_TUTOR_AUTOLOAD' ) ) {
define( 'TWITTPAY_TUTOR_AUTOLOAD', true );

spl_autoload_register(
function ( $class ) {
$prefix = 'TwittPay\\Tutor\\';
if ( 0 !== strpos( $class, $prefix ) ) {
return;
}

$file = dirname( __DIR__ ) . '/src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
if ( is_readable( $file ) ) {
require_once $file;
}
}
);
}
