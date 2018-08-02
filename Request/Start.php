<?php

namespace PaynlPayum\Request;

use Payum\Core\Request\Generic;

class Start extends Generic
{
    /**
     * @param mixed $model
     *
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->setFirstModel($model);
    }
}