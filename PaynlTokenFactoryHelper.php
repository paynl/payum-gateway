<?php

namespace PaynlPayum;

use Payum\Core\Payum;

class PaynlTokenFactoryHelper
{
    /** @var Payum */
    private $payum;

    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    /**
     * {@inheritDoc}
     */
    public function createToken($gatewayName, $model, $actionPath, $afterPath)
    {
        $afterPath = $this->payum->getTokenFactory()->createToken($gatewayName, $model, $afterPath)->getTargetUrl();
        return $this->payum->getTokenFactory()->createToken($gatewayName, $model, $actionPath, [], $afterPath);
    }
}
