<?php

namespace PaynlPayum\Request;

use Payum\Core\Model\ModelAggregateInterface;

class GetPaymentMethods implements ModelAggregateInterface
{
    /** @var array */
    private $methodsList;

    /** @var array */
    private $result;

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getMethodsList()
    {
        return $this->methodsList;
    }

    /**
     * @param array $methodsList
     */
    public function setMethodsList($methodsList)
    {
        $this->methodsList = $methodsList;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return null;
    }
}
