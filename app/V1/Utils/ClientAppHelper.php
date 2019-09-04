<?php

namespace App\V1\Utils;

class ClientAppHelper
{
    protected static $instance;

    /**
     * @return ClientAppHelper
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected $name;
    protected $url;

    private function __construct()
    {
        $this->name = ConfigHelper::getAppName();
        $this->url = ConfigHelper::getAppUrl();

        $this->fetchFromRequestHeader();
    }

    private function fetchFromRequestHeader()
    {
        if ($app = request()->header('Application')) {
            $app = json_decode($app, true);

            if (empty($app)) return false;

            $this->name = $app['name'];
            $this->url = $app['url'];

            return true;
        }

        return false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
