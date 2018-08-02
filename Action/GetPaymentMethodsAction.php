<?php

namespace PaynlPayum\Action;

use PaynlPayum\PaynlApi;
use PaynlPayum\Request\GetPaymentMethods;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;

/**
 * @property PaynlApi api
 */
class GetPaymentMethodsAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    public function __construct()
    {
        $this->apiClass = PaynlApi::class;
    }


    /**
     * {@inheritDoc}
     *
     * @param GetPaymentMethods $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $defaultResult = array(
            'request' => array(
                'result' => 1,
                'errorId' => '',
                'errorMessage' => ''
            )
        );
        try {
            $request->setMethodsList($this->api->getPaymentMethods());
            $request->setResult($defaultResult);
        } catch (\Exception $exception) {
            $errorDetails = array(
                'request' => array(
                    'result' => 0,
                    'errorId' => $exception->getCode(),
                    'errorMessage' => $exception->getMessage()
                )
            );
            $request->setResult($errorDetails);
        }

    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof GetPaymentMethods;
    }
}
