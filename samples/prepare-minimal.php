<?php

include __DIR__.'/config.php';

use Payum\Core\Model\Payment;
use PaynlPayum\PaynlTokenFactoryHelper;

/** @var \Payum\Core\Payum $payum */
$paymentStorage = $payum->getStorage(Payment::class);

/** @var Payment $payment */
$payment = $paymentStorage->create();
$payment->setCurrencyCode('EUR');
$payment->setTotalAmount(123); // 1.23 EUR
$payment->setDescription('A description');

$paymentStorage->update($payment);

$tokenFactoryHelper = new PaynlTokenFactoryHelper($payum);
$startPath = Paynl\Helper::getBaseUrl().'/start.php';
$afterPath = Paynl\Helper::getBaseUrl() . '/done.php';
$startToken = $tokenFactoryHelper->createToken('paynl_ideal', $payment, $startPath, $afterPath);
header("Location: " . $startToken->getTargetUrl());