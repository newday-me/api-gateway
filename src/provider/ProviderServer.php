<?php

namespace newday\gateway\provider;

use newday\gateway\core\base\Api;
use newday\gateway\core\base\Server;
use newday\gateway\core\api\ApiRequest;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\exception\ServerException;
use newday\gateway\core\support\Response;
use newday\gateway\provider\api\IntroApi;
use newday\gateway\provider\api\ListApi;

class ProviderServer extends Server
{

    /**
     * 配置
     *
     * @var ProviderConfig
     */
    protected $config;

    /**
     * 接口列表
     *
     * @var array
     */
    protected $apiList = [];

    /**
     * 注册接口
     *
     * @param string $api
     */
    public function registerApi($api)
    {
        $this->apiList[$api] = $api;
    }

    /**
     * 批量注册接口
     *
     * @param array $list
     */
    public function registerApiMulti($list)
    {
        foreach ($list as $value) {
            $this->registerApi($value);
        }
    }

    /**
     * 获取接口列表
     *
     * @return array
     */
    public function getApiList()
    {
        return $this->apiList;
    }

    /**
     * 服务入口
     *
     * @param bool $valid
     * @return ApiResponse
     */
    public function entry($valid = true)
    {
        try {
            // 签名验证
            $valid && $this->valid();

            // 运行
            return $this->run();
        } catch (\Exception $e) {
            $extra = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            return $this->apiError('接口发生意外', '', $extra);
        }
    }

    /**
     * 构造响应
     *
     * @param ApiResponse $apiResponse
     * @return Response
     */
    public function buildResponse($apiResponse)
    {
        $responsePack = $this->getResponsePack();
        $data = $apiResponse->getResponse()->toArray();
        $content = $responsePack->pack($data);
        return new Response($content);
    }

    /**
     * 签名验证
     *
     * @return $this
     * @throws ServerException
     */
    protected function valid()
    {
        $request = $this->getRequest();
        $timestamp = $request->header(NameConstant::HEADER_NAME_API_TIMESTAMP);
        $signature = $request->header(NameConstant::HEADER_NAME_API_SIGNATURE);
        if (empty($signature)) {
            throw new ServerException('接口签名为空');
        }

        $serverToken = $this->getConfig()->getServerToken();
        if ($this->getSignature()->generate($serverToken, $timestamp) != $signature) {
            throw new ServerException('接口签名验证失败');
        }

        if ($timestamp < time() - 60) {
            throw new ServerException('接口签名已经过期');
        }

        return $this;
    }

    /**
     * 运行服务
     *
     * @return ApiResponse
     * @throws ServerException
     */
    protected function run()
    {
        $request = $this->getRequest();
        $class = $request->header(NameConstant::HEADER_NAME_API_CLASS);
        if (empty($class)) {
            throw new ServerException('接口类名为空');
        }

        $apiList = array_merge([
            NameConstant::CLASS_API_LIST => ListApi::class,
            NameConstant::CLASS_API_INTRO => IntroApi::class
        ], $this->apiList);
        if (!isset($apiList[$class])) {
            throw new ServerException('接口类未注册');
        }

        return $this->execApi($apiList[$class]);
    }

    /**
     * 执行Api
     *
     * @param string $class
     * @return ApiResponse
     * @throws ServerException
     */
    protected function execApi($class)
    {
        if (class_exists($class)) {
            $api = new $class();
            if ($api instanceof Api) {
                // 源数据
                $request = $this->getRequest();
                $requestData = $request->input();

                // 请求数据
                $requestPack = $this->getRequestPack();
                $requestObject = $requestPack->unpack($requestData);
                if (is_null($requestObject)) {
                    throw new ServerException('解包请求数据失败');
                }

                $apiRequest = new ApiRequest($requestObject);
                return $api->entry($apiRequest, $this);
            } else {
                throw new ServerException('无效的接口类');
            }
        } else {
            throw new ServerException('接口类不存在');
        }
    }

    /**
     * 设置配置
     *
     * @param ProviderConfig $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * 获取配置
     *
     * @return ProviderConfig
     */
    protected function getConfig()
    {
        return $this->config;
    }

}