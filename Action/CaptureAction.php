<?php

namespace PaynlPayum\Action;

use PaynlPayum\PaynlApi;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHumanStatus;

/**
 * @property PaynlApi api
 */
class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
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
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($status = new GetHumanStatus($model));
        if (!$status->isAuthorized()) {
            throw new \LogicException('Transaction must be authorized to perform capture action!');
        }

        if (!$this->api->captureTransaction($model[PaynlApi::FIELD_ORDER_ID])) {
            throw new \LogicException('Could not perform capture action.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        $model = $request->getModel();
        return
            $request instanceof Capture &&
            $model instanceof \ArrayAccess &&
            !empty($model[PaynlApi::FIELD_ORDER_ID]);
    }
}
