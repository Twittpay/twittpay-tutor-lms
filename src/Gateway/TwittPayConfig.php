<?php
/**
 * TwittPay settings for Tutor LMS.
 *
 * @package TwittPay\Tutor\Gateway
 */

namespace TwittPay\Tutor\Gateway;

use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;
use Ollyo\PaymentHub\Core\Payment\BaseConfig;
use Tutor\Ecommerce\Settings;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;

class TwittPayConfig extends BaseConfig implements ConfigContract {

use PaymentUrlsTrait;

private $api_key;

protected $name = 'twittpay';

public function __construct() {
parent::__construct();

$settings      = Settings::get_payment_gateway_settings( 'twittpay' );
$this->api_key = $this->get_field_value( $settings, 'api_key' );
}

private function getApiKey(): string {
return (string) $this->api_key;
}

public function is_configured() {
return (bool) $this->api_key;
}

public function createConfig(): void {
parent::createConfig();

$this->updateConfig( array( 'api_key' => $this->getApiKey() ) );
}
}
