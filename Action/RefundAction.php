<?php

namespace PaynlPayum\Action;

use Paynl\Result\Transaction\Refund as RefundResult;
use PaynlPayum\PaynlApi;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Refund;

/**
 * @property PaynlApi api
 */
class RefundAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
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
     * @param Refund $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($status = new GetHumanStatus($model));
        try {
            if ($status->isAuthorized()) {
                if (!$this->api->voidTransaction($model[PaynlApi::FIELD_ORDER_ID])) {
                    throw new \LogicException('Could not perform void action.');
                }
            } else {
                // api refund
                $amount = !empty($model[PaynlApi::FIELD_ORDER_AMOUNT]) ? $model[PaynlApi::FIELD_ORDER_AMOUNT] : null;
                $description = !empty($model[PaynlApi::FIELD_ORDER_DESCRIPTION]) ? $model[PaynlApi::FIELD_ORDER_DESCRIPTION] : null;
                $processDate = !empty($model[PaynlApi::FIELD_ORDER_PROCESSING_DATE]) ? $model[PaynlApi::FIELD_ORDER_PROCESSING_DATE] : null;
                $currency = $model[PaynlApi::FIELD_CURRENCY];

                $result = $this->api->refundTransaction($model[PaynlApi::FIELD_ORDER_ID], $amount, $description, $processDate, $currency);

                if (!($result instanceof RefundResult)) {
                    throw new \LogicException("Could not refund transaction. Unexpected result has been received.");
                }
                $resultData = $result->getData();
                if ($resultData['request']['result'] != 1) {
                    throw new \LogicException($resultData['request']['errorMessage'], $resultData['request']['errorId']);
                }
            }
        } catch (\Exception $exception) {
            throw new \LogicException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        $model = $request->getModel();
        return
            $request instanceof Refund &&
            $model instanceof \ArrayAccess &&
            !empty($model[PaynlApi::FIELD_ORDER_ID]);
    }
}
