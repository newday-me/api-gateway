<?php

namespace newday\gateway\provider;

use newday\gateway\core\constant\CodeConstant;
use newday\gateway\core\objects\ResponseObject;
use newday\gateway\core\traits\PackTrait;
use newday\gateway\support\Request;
use newday\gateway\core\api\Api;
use newday\gateway\core\api\ApiRequest;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\Signature;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\exception\ServerException;
use newday\gateway\provider\api\IntroApi;
use newday\gateway\provider\api\ListApi;

class ProviderServer
{
    use PackTrait;

    /**
     * 接口列表
     *
     * @var array
     */
    protected $apiList = [];

    /**
     * 配置
     *
     * @var ProviderConfig
     */
    protected $config;

    /**
     * 构造函数
     *
     * @param ProviderConfig $config
     */
    public function __construct(ProviderConfig $config = null)
    {
        $config && $this->setConfig($config);
    }

    /**
     * 注册接口
     *
     * @param array|string $api
     */
    public function registerApi($api)
    {
        if (is_array($api)) {
            foreach ($api as $vo) {
                $this->registerApi($vo);
            }
        } else {
            $this->apiList[$api] = $api;
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
            $responseObject = ResponseObject::makeError('接口发生意外', '', $extra);
            return new ApiResponse($responseObject, $responsePack);
        }
    }

    /**
     * 签名验证
     *
     * @throws ServerException
     */
    protected function valid()
    {
        $timestamp = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_TIMESTAMP);
        $signature = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_SIGNATURE);
        if (empty($signature)) {
            throw new ServerException('接口签名为空');
        } elseif (Signature::sign($this->getConfig()->getServerToken(), $timestamp) != $signature) {
            throw new ServerException('接口签名验证失败');
        } elseif ($timestamp < time() - 60) {
            throw new ServerException('接口签名已经过期');
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
        $class = Request::getSingleton()->header(NameConstant::HEADER_NAME_API_CLASS);
        if (empty($class)) {
            throw new ServerException('接口类名为空');
        } else {
            $apiList = array_merge([
                NameConstant::CLASS_API_LIST => ListApi::class,
                NameConstant::CLASS_API_INTRO => IntroApi::class
            ], $this->apiList);
            if (isset($apiList[$class])) {
                return $this->execApi($apiList[$class]);
            } else {
                throw new ServerException('接口类未注册');
            }
        }
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
                $requestData = Request::getSingleton()->input();
                $requestPack = $this->getRequestPack();

                // 请求数据
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
     * 接口成功
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @return ApiResponse
     */
    public function apiSuccess($msg = 'success', $data = '', $extra = [])
    {
        return $this->apiData(CodeConstant::API_SUCCESS, $msg, $data, $extra);
    }

    /**
     * 接口失败
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @return ApiResponse
     */
    public function apiError($msg = 'error', $data = '', $extra = [])
    {
        return $this->apiData(CodeConstant::API_ERROR, $msg, $data, $extra);
    }

    /**
     * 接口数据
     *
     * @param $code
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @return ApiResponse
     */
    public function apiData($code, $msg = 'error', $data = '', $extra = [])
    {
        $responsePack = $this->getResponsePack();
        $responseObject = ResponseObject::make($code, $msg, $data, $extra);
        return new ApiResponse($responseObject, $responsePack);
    }

    /**
     * 获取配置
     *
     * @return ProviderConfig
     */
    public function getConfig()
    {
        return $this->config;
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

}