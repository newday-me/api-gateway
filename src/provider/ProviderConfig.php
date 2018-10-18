<?php

namespace newday\gateway\provider;

use newday\gateway\core\base\Config;

class ProviderConfig extends Config
{

    /**
     * 服务token
     *
     * @var string
     */
    protected $serverToken;

    /**
     * 获取服务令牌
     *
     * @return string
     */
    public function getServerToken()
    {
        return $this->serverToken;
    }

    /**
     * 设置服务令牌
     *
     * @param $serverToken
     */
    public function setServerToken($serverToken)
    {
        $this->serverToken = $serverToken;
    }

}