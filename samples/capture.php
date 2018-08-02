<?php

include __DIR__.'/config.php';

use Payum\Core\Request\Capture;

try {
    // orderId - is order ID (transaction ID) returned by Pay.nl
    $orderId = $_REQUEST['orderId'];
    $payment = ['orderId' => $orderId];

    /** @var \Payum\Core\Payum $payum */
    $payum->getGateway($gatewayName)->execute($transaction = new Capture($payment));
    echo 'Transaction captured!';
} catch (\Exception $exception) {
    var_dump($exception->getMessage());
}