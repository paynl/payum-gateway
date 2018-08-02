<?php

include __DIR__.'/config.php';

use Payum\Core\Request\GetHumanStatus;
use PaynlPayum\PaynlApi;

try {
    /** @var \Payum\Core\Payum $payum */
    $token = $payum->getHttpRequestVerifier()->verify($_REQUEST);
    $gateway = $payum->getGateway($token->getGatewayName());

    // order_id - is order ID (transaction ID) returned by Pay.nl
    $orderId = $_REQUEST['order_id'];

    $payment = [PaynlApi::FIELD_ORDER_ID => $orderId];

    $gateway->execute($status = new GetHumanStatus($payment));

    $paymentStorage = $payum->getStorage($token->getDetails()->getClass());
    $payment = $paymentStorage->find($token->getDetails()->getId());

    echo "TRUE| Status = ". $status->getValue();
} catch (\Exception $exception) {
    var_dump($exception);
}