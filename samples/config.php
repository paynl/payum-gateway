<?php

require_once '../vendor/autoload.php';

use PaynlPayum\PaynlApi;
use PaynlPayum\PaynlGatewayFactory;

use Payum\Core\GatewayFactoryInterface;
use Payum\Core\Payum;
use Payum\Core\PayumBuilder;


// Configure payum
$payumBuilder = new PayumBuilder();
$payumBuilder->addDefaultStorages();

// Add pay.nl factory to payum
$payumBuilder->addGatewayFactory(
    'paynl_factory',
    function (array $config, GatewayFactoryInterface $coreGatewayFactory) {
        return new PaynlGatewayFactory($config, $coreGatewayFactory);
    }
);

// Notify url, used for exchange
$payumBuilder->setGenericTokenFactoryPaths([
    'notify' => Paynl\Helper::getBaseUrl().'/notify.php'
]);

$token_code = 'AT-1234-5678';
$api_token = 'abcdefghijklmnopqrstuvwqyz1234';
$service_id = 'SL-1234-5678';
$test_mode = true;

/**
 * Configure the payment methods
 * You can add a gateway for each payment method, or you can omit payment_option_id.
 * If you omit payment_option_id, the customer can choose any payment method enabled in the service
 */
$payumBuilder->addGateway(
    'paynl_ideal', // You can use any name you like here
    [
        'factory' => 'paynl_factory', // Use the name you used in addGatewayFactory above
        'token_code' => $token_code, // See: https://admin.pay.nl/company/tokens for your tokens
        'api_token' => $api_token,
        'service_id' => $service_id, // See: https://admin.pay.nl/programs/programs use the SL code of your service
        'test_mode' => $test_mode,
        /**
         * payment_option_id
         *
         * Optional: if you omit this, the customer can select any payment method
         * You can use the payment option id to select a payment method
         * See https://admin.pay.nl/data/payment_profiles for all available payment methods
         */
        'payment_option_id' => 10
    ]
);

$payumBuilder->addGateway(
    'paynl_bancontact',
    [
        'factory' => 'paynl_factory',
        'token_code' => $token_code,
        'api_token' => $api_token,
        'service_id' => $service_id,
        'test_mode' => $test_mode,
        'payment_option_id' => 436
    ]
);

/** @var Payum $payum */
$payum = $payumBuilder->getPayum();