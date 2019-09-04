<?php

namespace App\V1\Utils;

trait TransactionTrait
{
    protected function transactionHelper()
    {
        return TransactionHelper::getInstance();
    }

    protected function transactionStart($connection = null)
    {
        $this->transactionHelper()->start($connection);
    }

    protected function transactionComplete()
    {
        $this->transactionHelper()->complete();
    }

    protected function transactionStop()
    {
        $this->transactionHelper()->stop();
    }
}
