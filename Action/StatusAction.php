<?php

namespace PaynlPayum\Action;

use Paynl\Result\Transaction\Transaction as ResultTransaction;
use PaynlPayum\PaynlApi;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;

/**
 * @property PaynlApi api
 */
class StatusAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
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
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        try {
            $model = $request->getModel();
            $transaction = $this->api->getTransaction($model[PaynlApi::FIELD_ORDER_ID]);
            $this->mapPaynlStatusToPayumStatus($transaction, $request);
        } catch (\Exception $exception) {
            $request->markUnknown();
        }
    }

    /**
     * @param ResultTransaction $transaction
     * @param GetStatusInterface $request
     */
    private function mapPaynlStatusToPayumStatus($transaction, $request)
    {
        switch ($transaction) {
            case $transaction->isAuthorized():
                $request->markAuthorized();
                break;
            case $transaction->isBeingVerified():
                $request->markSuspended();
                break;
            case $transaction->isPaid():
                $request->markCaptured();
                break;
            case $transaction->isPending():
                $request->markPending();
                break;
            case $transaction->isRefunded():
            case $transaction->isPartiallyRefunded():
                $request->markRefunded();
                break;
            case $transaction->isCanceled():
                $request->markCanceled();
                break;
            default:
                $request->markUnknown();
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        $model = $request->getModel();
        return
            $request instanceof GetStatusInterface &&
            $model instanceof \ArrayAccess &&
            !empty($model[PaynlApi::FIELD_ORDER_ID]);
    }
}
