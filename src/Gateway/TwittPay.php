<?php
/**
 * TwittPay payment for Tutor LMS.
 *
 * @package TwittPay\Tutor\Gateway
 */

namespace TwittPay\Tutor\Gateway;

use ErrorException;
use Ollyo\PaymentHub\Core\Payment\BasePayment;
use Ollyo\PaymentHub\Core\Support\Arr;
use Ollyo\PaymentHub\Core\Support\System;
use Throwable;
use TwittPay\Tutor\Api\ApiClient;
use TwittPay\Tutor\Api\ApiException;

class TwittPay extends BasePayment {

protected ApiClient $client;

public function check(): bool {
$config_keys = Arr::make( array( 'api_key' ) );

return $config_keys->every(
function ( $key ) {
return $this->config->has( $key ) && ! empty( $this->config->get( $key ) );
}
);
}

public function setup(): void {
$this->client = new ApiClient( (string) $this->config->get( 'api_key' ) );
}

public function setData( $data ): void {
parent::setData( $this->prepareData( $data ) );
}

private function prepareData( $data ) {
$amount = number_format( (float) $data->total_price, 2, '.', '' );

return array(
'cus_name'    => $data->billing_address->name ?? 'Customer',
'cus_email'   => $data->billing_address->email,
'amount'      => $amount,
'metadata'    => array(
'order_id'     => (string) $data->order_id,
'redirect_url' => $this->config->get( 'success_url' ),
'amount'       => $amount,
),
'success_url' => $this->config->get( 'webhook_url' ),
'cancel_url'  => $this->config->get( 'cancel_url' ),
'webhook_url' => $this->config->get( 'webhook_url' ),
);
}

public function createPayment() {
try {
$response = $this->client->createPayment( $this->getData() );

if ( ! empty( $response['payment_url'] ) ) {
// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- trusted checkout address.
wp_redirect( $response['payment_url'] );
exit;
}

if ( ! empty( $response['message'] ) ) {
/* translators: %s: error from the payment service */
throw new ApiException( sprintf( esc_html__( 'Gateway error: %s', 'tutor-twittpay' ), esc_html( $response['message'] ) ) );
}

throw new ApiException( esc_html__( 'Could not get a payment link from TwittPay', 'tutor-twittpay' ) );
} catch ( ApiException $error ) {
throw new ErrorException( esc_html( $error->getMessage() ) );
}
}

public function verifyAndCreateOrderData( object $payload ): object {
try {
$transaction_id = $this->extractTransactionId( $payload );
$response       = $this->validatePaymentResponse( $transaction_id );

return $this->prepareOrderData( $response, $transaction_id );
} catch ( Throwable $error ) {
throw new ApiException(
sprintf(
/* translators: %s: error message */
esc_html__( 'Payment verification failed: %s', 'tutor-twittpay' ),
esc_html( $error->getMessage() )
)
);
}
}

private function extractTransactionId( object $payload ): string {
foreach ( array( 'transactionId', 'transaction_id' ) as $key ) {
if ( ! empty( $payload->get[ $key ] ) ) {
return sanitize_text_field( $payload->get[ $key ] );
}
}

if ( ! empty( $payload->stream ) ) {
$stream = json_decode( $payload->stream, true );
foreach ( array( 'transactionId', 'transaction_id' ) as $key ) {
if ( is_array( $stream ) && ! empty( $stream[ $key ] ) ) {
return sanitize_text_field( $stream[ $key ] );
}
}
}

throw new ApiException( esc_html__( 'Transaction ID not found in the request', 'tutor-twittpay' ) );
}

/** Verify with TwittPay. The answer is never taken from the request itself. */
private function validatePaymentResponse( string $transaction_id ): array {
$response = $this->client->verifyPayment( $transaction_id );

if ( empty( $response ) || ! is_array( $response ) || empty( $response['status'] ) ) {
throw new ApiException( esc_html__( 'Invalid response from the payment service', 'tutor-twittpay' ) );
}

$meta = $response['metadata'] ?? array();
if ( is_string( $meta ) ) {
$meta = json_decode( $meta, true );
}

if ( ! is_array( $meta ) || empty( $meta['order_id'] ) || empty( $meta['redirect_url'] ) ) {
throw new ApiException( esc_html__( 'Missing required order details in the response', 'tutor-twittpay' ) );
}

// A completed payment must cover the amount this order was created with.
if ( 'COMPLETED' === strtoupper( trim( (string) $response['status'] ) ) ) {
$paid     = (float) ( $response['amount'] ?? 0 );
$expected = (float) ( $meta['amount'] ?? 0 );

if ( $expected <= 0 || $paid + 0.01 < $expected ) {
throw new ApiException( esc_html__( 'The paid amount does not match the order', 'tutor-twittpay' ) );
}
}

$response['metadata'] = $meta;

return $response;
}

private function prepareOrderData( array $response, string $transaction_id ): object {
$return_data = System::defaultOrderData();

$status_map = array(
'ERROR'     => 'failed',
'COMPLETED' => 'paid',
'PENDING'   => 'pending',
);

$status = strtoupper( trim( (string) $response['status'] ) );

$return_data->id             = sanitize_text_field( $response['metadata']['order_id'] );
$return_data->payment_status = $status_map[ $status ] ?? 'failed';
$return_data->transaction_id = sanitize_text_field( $transaction_id );
// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
$return_data->redirectUrl = esc_url_raw( $response['metadata']['redirect_url'] );

$return_data->payment_error_reason = '';
$return_data->payment_payload      = $response;
$return_data->fees                 = '';
$return_data->earnings             = '';

return $return_data;
}
}
