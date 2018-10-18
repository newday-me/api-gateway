<?php

namespace newday\gateway\hub;

use newday\gateway\core\base\Server;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\exception\ServerException;
use newday\gateway\provider\ProviderClient;
use newday\gateway\provider\ProviderConfig;

class HubServer extends Server
{

    /**
     * 签名验证
     *
     * @param string $appToken
     * @throws ServerException
     */
    public function valid($appToken)
    {
        $request = $this->getRequest();
        $timestamp = $request->header(NameConstant::HEADER_NAME_API_TIMESTAMP);
        $signature = $request->header(NameConstant::HEADER_NAME_API_SIGNATURE);
        if ($this->getSignature()->generate($appToken, $timestamp) != $signature) {
            throw new ServerException('应用签名验证失败');
        }

        if ($timestamp < time() - 60) {
            throw new ServerException('应用签名已经过期');
        }
    }

    /**
     * 获取请求的应用密钥
     *
     * @return string
     */
    public function getRequestAppKey()
    {
        $request = $this->getRequest();
        return $request->header(NameConstant::HEADER_NAME_API_KEY);
    }

    /**
     * 获取请求的接口名
     *
     * @return string
     */
    public function getRequestAppName()
    {
        $request = $this->getRequest();
        return $request->header(NameConstant::HEADER_NAME_API_NAME);
    }

    /**
     * 运行服务
     *
     * @param string $apiClass
     * @param ProviderConfig $providerConfig
     * @return ApiResponse
     */
    public function run($apiClass, $providerConfig)
    {
        try {
            $client = new ProviderClient($providerConfig);
            $client->setContainer($this->getContainer());

            $request = $this->getRequest();
            $postData = $request->input();
            return $client->request($apiClass, $postData);
        } catch (\Exception $e) {
            $extra = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            return $this->apiError('服务发生意外', '', $extra);
        }
    }

}