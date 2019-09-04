<?php

namespace App\V1\Utils;

use Illuminate\Support\Facades\DB;

class TransactionHelper
{
    protected static $instance;

    /**
     * @return TransactionHelper
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private $currentTransactionConnections = [];
    private $hasTransaction = false;

    private function __construct()
    {
        $this->currentTransactionConnections = [];
        $this->hasTransaction = false;
    }

    public function start($connection = null)
    {
        if (empty($connection)) {
            $connection = config('database.default');
        }
        if (!in_array($connection, $this->currentTransactionConnections)) {
            DB::connection($connection)->beginTransaction();
            $this->currentTransactionConnections[] = $connection;
            $this->hasTransaction = true;
        }
    }

    public function complete()
    {
        if (!$this->hasTransaction) return;
        foreach ($this->currentTransactionConnections as $connection) {
            DB::connection($connection)->commit();
        }
        $this->currentTransactionConnections = [];
        $this->hasTransaction = false;
    }

    public function stop()
    {
        if (!$this->hasTransaction) return;
        foreach ($this->currentTransactionConnections as $connection) {
            DB::connection($connection)->rollBack();
        }
        $this->currentTransactionConnections = [];
        $this->hasTransaction = false;
    }
}
