<?php
/**
 * Created by PhpStorm.
 * User: Dmytro Dmytrashchuk
 * Date: 7/26/2018
 * Time: 5:34 PM
 */

include __DIR__.'/config.php';

use Payum\Core\Request\GetHumanStatus;

try {
    // orderId - is order ID (transaction ID) returned by Pay.nl
    $orderId = $_REQUEST['orderId'];
    $payment = ['orderId' => $orderId];

    /** @var \Payum\Core\Payum $payum */
    $payum->getGateway('paynl_ideal')->execute($status = new GetHumanStatus($payment));
    $payment = $status->getModel();

    var_dump($status);
} catch (\Exception $exception) {
    var_dump($exception);
}