<?php
/**
 * Created by PhpStorm.
 * User: Dmytro Dmytrashchuk
 * Date: 7/26/2018
 * Time: 5:25 PM
 */

include __DIR__.'/config.php';

use Payum\Core\Request\Refund;use PaynlPayum\PaynlApi;


// orderId - is order ID (transaction ID) returned by Pay.nl
$orderId = $_REQUEST['orderId'];
$refundPayment = [
    'orderId' => $orderId,
    'amount' => 2, // optional
    'description' => 'Test refund', // optional
    'processingDate' => new DateTime() // optional
];

try {
    $payum->getGateway('paynl_ideal')->execute(new Refund($refundPayment));
    echo 'Done!';
} catch (\Exception $exception) {
    ddd($exception->getMessage());
}