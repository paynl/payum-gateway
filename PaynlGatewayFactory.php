<?php

namespace PaynlPayum;

use PaynlPayum\Action\CaptureAction;
use PaynlPayum\Action\GetPaymentMethodsAction;
use PaynlPayum\Action\RefundAction;
use PaynlPayum\Action\StartAction;
use PaynlPayum\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class PaynlGatewayFactory extends GatewayFactory
{
    private $apiToken;

    private $serviceID;

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @return mixed
     */
    public function getServiceID()
    {
        return $this->serviceID;
    }

    /**
     * @param mixed $serviceID
     */
    public function setServiceID($serviceID)
    {
        $this->serviceID = $serviceID;
    }

    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $defaults = array(
            'payum.factory_name' => 'paynl_factory',
            'payum.factory_title' => 'Paynl',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.start' => new StartAction(),
            'payum.action.get_payment_methods' => new GetPaymentMethodsAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.status' => new StatusAction()
        );

        $config->defaults($defaults);
        $config['payum.prepend_actions'] = [
            'payum.action.start',
            'payum.action.status',
            'payum.action.refund'
        ];

        if (false == $config['payum.api']) {
            $config['payum.required_options'] = array(
                PaynlApi::API_TOKEN,
                PaynlApi::SERVICE_ID
            );
            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $paynlApi = new PaynlApi();
                $paynlApi->setApiToken($config[PaynlApi::API_TOKEN]);
                $paynlApi->setServiceId($config[PaynlApi::SERVICE_ID]);

                if (!empty($config[PaynlApi::PAYMENT_OPTION_ID])) {
                    $paynlApi->setPaymentMethod($config[PaynlApi::PAYMENT_OPTION_ID]);
                }

                if (!empty($config[PaynlApi::TOKEN_CODE])) {
                    $paynlApi->setTokenCode($config[PaynlApi::TOKEN_CODE]);
                }

                $testMode = !empty($config[PaynlApi::TEST_MODE]) ? (bool)$config[PaynlApi::TEST_MODE] : false;
                $paynlApi->setTestMode($testMode);

                return $paynlApi;
            };
        }
    }

}
