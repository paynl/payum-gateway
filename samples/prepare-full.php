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

$payment->setDetails(array(
    // optional
    'bank' => 1,
    'extra1' => 'ext1',
    'extra2' => 'ext2',
    'extra3' => 'ext3',
    'products' => array(
        array(
            'id' => 1,
            'name' => 'een product',
            'price' => 5.00,
            'tax' => 0.87,
            'qty' => 1,
        ),
        array(
            'id' => 2,
            'name' => 'ander product',
            'price' => 5.00,
            'tax' => 0.87,
            'qty' => 1,
        )
    ),
    'language' => 'EN',
    'ipaddress' => '127.0.0.1',
    'invoiceDate' => new DateTime('2016-02-16'),
    'deliveryDate' => new DateTime('2016-06-06'), // in case of tickets for an event, use the event date here
    'enduser' => array(
        'initials' => 'T',
        'lastName' => 'Test',
        'gender' => 'M',
        'birthDate' => new DateTime('1990-01-10'),
        'phoneNumber' => '0612345678',
        'emailAddress' => 'test@test.nl',
    ),
    'address' => array(
        'streetName' => 'Test',
        'houseNumber' => '10',
        'zipCode' => '1234AB',
        'city' => 'Test',
        'country' => 'NL',
    ),
    'invoiceAddress' => array(
        'initials' => 'IT',
        'lastName' => 'ITEST',
        'streetName' => 'Istreet',
        'houseNumber' => '70',
        'zipCode' => '5678CD',
        'city' => 'ITest',
        'country' => 'NL',
    )
));

$paymentStorage->update($payment);

$tokenFactoryHelper = new PaynlTokenFactoryHelper($payum);
$startPath = Paynl\Helper::getBaseUrl().'/start.php';
$afterPath = Paynl\Helper::getBaseUrl() . '/done.php';
$startToken = $tokenFactoryHelper->createToken('paynl_ideal', $payment, $startPath, $afterPath);
header("Location: " . $startToken->getTargetUrl());