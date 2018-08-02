<?php

include __DIR__.'/config.php';

use PaynlPayum\Request\Start;

try {
    $token = $payum->getHttpRequestVerifier()->verify($_REQUEST);
    $paymentStorage = $payum->getStorage($token->getDetails()->getClass());
    $gateway = $payum->getGateway($token->getGatewayName());

    /** @var \Payum\Core\GatewayInterface $gateway */
    $reply = $gateway->execute($transaction = new Start($token), true);
    /** @var \Payum\Core\Model\Payment $payment */
    $payment = $transaction->getModel();
    $paymentStorage->update($payment);
    $paymentDetails = $payment->getDetails();

    $payum->getHttpRequestVerifier()->invalidate($token);
    header('Location: ' . $paymentDetails['paynlResponseData']['transaction']['paymentURL']);
} catch (\Exception $exception) {
    var_dump($exception);
}