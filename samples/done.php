<?php

include __DIR__.'/config.php';

use Payum\Core\Request\GetHumanStatus;

try {
    /** @var \Payum\Core\Payum $payum */
    $token = $payum->getHttpRequestVerifier()->verify($_REQUEST);
    $gateway = $payum->getGateway($token->getGatewayName());

    // orderId - is order ID (transaction ID) returned by Pay.nl
    $orderId = $_REQUEST['orderId'];
    $payment = ['orderId' => $orderId];

    $gateway->execute($status = new GetHumanStatus($payment));

    $paymentStorage = $payum->getStorage($token->getDetails()->getClass());
    $payment = $paymentStorage->find($token->getDetails()->getId());

    // depends on $status->getValue() - show "thank you" page or redirect back to checkout
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status->getValue(),
        'order' => [
            'total_amount' => $payment->getTotalAmount(),
            'currency_code' => $payment->getCurrencyCode(),
            'details' => $payment->getDetails(),
        ],
    ]);
} catch (\Exception $exception) {
    var_dump($exception);
}