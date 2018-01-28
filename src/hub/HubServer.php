<?php

namespace newday\gateway\hub;

use newday\gateway\core\pack\ResponsePack;
use newday\gateway\provider\ProviderConfig;
use newday\gateway\support\Request;
use newday\gateway\core\Signature;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\exception\ServerException;
use newday\gateway\provider\ProviderClient;

abstract class HubServer
{
    /**
     * 请求打包对象
     *
     * @var ResponsePack
     */
    protected $responsePack;

    /**
     * 构造函数
     *
     * @param ResponsePack $responsePack
     */
    public function __construct(ResponsePack $responsePack = null)
    {
        $responsePack && $this->setResponsePack($responsePack);
    }

    /**
     * 服务入口
     *
     * @return ApiResponse
     */
    public function entry()
    {
        try {
            // 签名验证
            $this->valid();

            // 运行
            return $this->run();
        } catch (\Exception $e) {
            $responsePack = $this->getResponsePack();
            $extra = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            return ApiResponse::serverError('服务发生意外', '', $extra, $responsePack);
        }
    }

    /**
     * 签名验证
     *
     * @throws ServerException
     */
    public function valid()
    {
        $appKey = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_KEY);
        $appToken = $this->getAppToken($appKey);
        if (empty($appToken)) {
            throw new ServerException('获取应用密钥失败');
        }

        $timestamp = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_TIMESTAMP);
        $signature = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_SIGNATURE);
        if (Signature::sign($appToken, $timestamp) != $signature) {
            throw new ServerException('应用签名验证失败');
        } elseif ($timestamp < time() - 60) {
            throw new ServerException('应用签名已经过期');
        }
    }

    /**
     * 运行服务
     *
     * @return ApiResponse
     * @throws ServerException
     */
    public function run()
    {
        $appKey = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_KEY);
        $apiName = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_NAME);

        $config = new ProviderConfig();
        $apiClass = $this->getApiClass($appKey, $apiName, $config);
        if (empty($apiClass)) {
            throw new ServerException('获取接口类名为空');
        }

        $responsePack = $this->getResponsePack();
        $client = new ProviderClient($config, $responsePack);

        $postData = Request::getSingleton()->input();
        return $client->request($apiClass, $postData);
    }

    /**
     * 获取回复打包对象
     *
     * @return ResponsePack
     */
    public function getResponsePack()
    {
        return $this->responsePack;
    }

    /**
     * 设置回复打包对象
     *
     * @param ResponsePack $responsePack
     */
    public function setResponsePack($responsePack)
    {
        $this->responsePack = $responsePack;
    }


    /**
     * 获取应用token
     *
     * @param string $appKey
     * @return string
     * @throws ServerException
     */
    abstract protected function getAppToken($appKey);

    /**
     * 获取接口类名
     *
     * @param string $appKey
     * @param string $apiName
     * @param ProviderConfig $config
     * @return string
     * @throws ServerException
     */
    abstract protected function getApiClass($appKey, $apiName, ProviderConfig $config);
}