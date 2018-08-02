<?php

namespace PaynlPayum\Action;

use PaynlPayum\PaynlApi;
use PaynlPayum\Request\Start;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\Payment;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;

/**
 * @property PaynlApi api
 */
class StartAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;
    use GenericTokenFactoryAwareTrait;

    const CURRENCY_EXPONENT = 2;

    public function __construct()
    {
        $this->apiClass = PaynlApi::class;
    }

    /**
     * @param Start $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var Payment $payment */
        $payment = $request->getModel();
        $transactionOptions = $this->buildTransactionOptions($payment, $request);
        $resultData = $this->api->startTransaction($transactionOptions);

        $paymentModelDetails = $payment->getDetails();
        $paymentModelDetails['paynlResponseData'] = $resultData;
        $payment->setDetails($paymentModelDetails);
    }

    /**
     * @param Payment $payment
     * @param Start $request
     * @return array
     */
    private function buildTransactionOptions($payment, $request)
    {
        $paymentDetails = array(
            'amount' => $payment->getTotalAmount() / pow(10, self::CURRENCY_EXPONENT),
            'currency' => $payment->getCurrencyCode(),
            'description' => $payment->getDescription(),
            'testmode' => (int)$this->api->isTestMode(),
            'returnUrl' => $request->getToken()->getAfterUrl()
        );

        $notifyToken = $this->tokenFactory->createNotifyToken(
            $request->getToken()->getGatewayName(),
            $payment
        );
        $paymentDetails['exchangeUrl'] = $notifyToken->getTargetUrl();

        $paymentOptionId = $this->api->getPaymentMethod();
        if (!empty($paymentOptionId)) {
            $paymentDetails['paymentMethod'] = $paymentOptionId;
        }
        return array_merge($paymentDetails, $payment->getDetails());
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return ($request instanceof Start) && ($request->getModel() instanceof PaymentInterface);
    }


}