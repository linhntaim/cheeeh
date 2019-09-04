<?php

namespace App\V1\Utils;

trait ClientAppTrait
{
    protected $clientAppName;
    protected $clientAppUrl;

    private function initClientApp()
    {
        $clientAppHelper = ClientAppHelper::getInstance();
        $this->clientAppName = $clientAppHelper->getName();
        $this->clientAppUrl = $clientAppHelper->getUrl();
    }

    public function getClientAppName()
    {
        return $this->clientAppName;
    }

    public function getClientAppUrl()
    {
        return $this->clientAppUrl;
    }
}
