<?php

namespace newday\gateway\provider;

use newday\gateway\core\pack\RequestPack;
use newday\gateway\core\pack\ResponsePack;
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
     * 请求打包对象
     *
     * @var RequestPack
     */
    protected $requestPack;

    /**
     * 回复打包对象
     *
     * @var ResponsePack
     */
    protected $responsePack;

    /**
     * 构造函数
     *
     * @param ProviderConfig $config
     * @param RequestPack $requestPack
     * @param ResponsePack $responsePack
     */
    public function __construct(ProviderConfig $config = null, RequestPack $requestPack = null, ResponsePack $responsePack = null)
    {
        $config && $this->setConfig($config);
        $requestPack && $this->setRequestPack($requestPack);
        $responsePack && $this->setResponsePack($responsePack);
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
            $extra = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            return ApiResponse::serverError('接口发生意外', '', $extra, $this->getResponsePack());
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
        if (Signature::sign($this->config->getServerToken(), $timestamp) != $signature) {
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
            return $this->applyApi($class);
        }
    }

    /**
     * 服务失败
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @return ApiResponse
     */
    public function serverError($msg = 'error', $data = '', $extra = [])
    {
        return ApiResponse::serverError($msg, $data, $extra, $this->getResponsePack());
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
        return ApiResponse::apiSuccess($msg, $data, $extra, $this->getResponsePack());
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
        return ApiResponse::apiError($msg, $data, $extra, $this->getResponsePack());
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
        return ApiResponse::apiData($code, $msg, $data, $extra, $this->getResponsePack());
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

    /**
     * 获取请求打包对象
     *
     * @return RequestPack
     */
    public function getRequestPack()
    {
        return $this->requestPack;
    }

    /**
     * 设置请求打包对象
     *
     * @param $requestPack
     */
    public function setRequestPack($requestPack)
    {
        $this->requestPack = $requestPack;
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
     * 应用Api
     *
     * @param string $class
     * @return ApiResponse
     * @throws ServerException
     */
    protected function applyApi($class)
    {
        if ($class == NameConstant::CLASS_API_LIST) {
            return $this->execApi(ListApi::class);
        } elseif ($class == NameConstant::CLASS_API_INTRO) {
            return $this->execApi(IntroApi::class);
        } elseif (isset($this->apiList[$class])) {
            return $this->execApi($class);
        } else {
            throw new ServerException('接口类未注册');
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
                $request = new ApiRequest($requestData, $requestPack);

                return $api->entry($request, $this);
            } else {
                throw new ServerException('无效的接口类');
            }
        } else {
            throw new ServerException('接口类不存在');
        }
    }
}