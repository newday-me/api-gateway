<?php

namespace newday\gateway\hub;

use newday\gateway\core\base\Config;

class HubConfig extends Config
{
    /**
     * 应用密钥
     *
     * @var string
     */
    public $appKey;

    /**
     * 应用令牌
     *
     * @var string
     */
    public $appToken;

    /**
     * 获取应用秘钥
     *
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * 设置应用秘钥
     *
     * @param string $appKey
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * 获取应用令牌
     *
     * @return string
     */
    public function getAppToken()
    {
        return $this->appToken;
    }

    /**
     * 设置应用令牌
     *
     * @param string $appToken
     */
    public function setAppToken($appToken)
    {
        $this->appToken = $appToken;
    }

}