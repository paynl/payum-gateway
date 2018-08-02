<?php

namespace PaynlPayum;

use Paynl\Config as PaynlSdkConfig;
use Paynl\Paymentmethods;
use Paynl\Result\Transaction\Start as StartResult;
use Paynl\Transaction;

class PaynlApi
{
    const TOKEN_CODE = 'token_code';
    const API_TOKEN = 'api_token';
    const SERVICE_ID = 'service_id';
    const PAYMENT_OPTION_ID = 'payment_option_id';
    const TEST_MODE = 'test_mode';

    const FIELD_ORDER_ID = 'orderId';
    const FIELD_ORDER_AMOUNT = 'amount';
    const FIELD_ORDER_DESCRIPTION = 'description';
    const FIELD_ORDER_PROCESSING_DATE = 'processingDate';

    /** @var string */
    private $tokenCode;

    /** @var string */
    private $apiToken;

    /** @var string */
    private $serviceId;

    /** @var int */
    private $paymentMethod;

    /** @var bool */
    private $testMode;

    public function startTransaction($transactionOptions)
    {
        self::prepareSdk();

        try {
            $result = Transaction::start($transactionOptions);
            if (!($result instanceof StartResult)) {
                throw new \Exception("Could not start transaction. Unexpected result has been received.");
            }
            $resultData = $result->getData();
        } catch (\Exception $exception) {
            $resultData = array(
                'result' => 0,
                'errorId' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            );
        }

        return $resultData;
    }

    private function prepareSdk()
    {
        PaynlSdkConfig::setApiToken($this->getApiToken());
        PaynlSdkConfig::setServiceId($this->getServiceId());
        PaynlSdkConfig::setTokenCode($this->getTokenCode());
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param string $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param string $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * @return string
     */
    public function getTokenCode()
    {
        return $this->tokenCode;
    }

    /**
     * @param string $tokenCode
     */
    public function setTokenCode($tokenCode)
    {
        $this->tokenCode = $tokenCode;
    }

    public function getTransaction($transactionId)
    {
        self::prepareSdk();

        return Transaction::get($transactionId);
    }

    public function captureTransaction($transactionId)
    {
        self::prepareSdk();

        return Transaction::capture($transactionId);
    }

    public function refundTransaction($transactionId, $amount = null, $description = null, \DateTime $processDate = null)
    {
        self::prepareSdk();

        return Transaction::refund($transactionId, $amount, $description, $processDate);
    }

    public function voidTransaction($transactionId)
    {
        self::prepareSdk();

        return Transaction::void($transactionId);
    }

    public function getPaymentMethods()
    {
        self::prepareSdk();
        return Paymentmethods::getList();
    }

    /**
     * @return int
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param int $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return $this->testMode;
    }

    /**
     * @param bool $testMode
     */
    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;
    }
}
