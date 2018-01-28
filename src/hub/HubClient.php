<?php

namespace newday\gateway\hub;

use newday\gateway\support\Http;
use newday\gateway\core\Signature;
use newday\gateway\core\pack\RequestPack;
use newday\gateway\core\pack\ResponsePack;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\exception\ServerException;

class HubClient
{
    /**
     * 配置
     *
     * @var HubConfig
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
     * @param HubConfig $config
     * @param RequestPack $requestPack
     * @param ResponsePack $responsePack
     */
    public function __construct(HubConfig $config = null, RequestPack $requestPack = null, ResponsePack $responsePack = null)
    {
        $config && $this->setConfig($config);
        $requestPack && $this->setRequestPack($requestPack);
        $responsePack && $this->setResponsePack($responsePack);
    }

    /**
     * 请求接口
     *
     * @param string $apiName
     * @param array $postData
     * @param int $timeout
     * @return ApiResponse
     */
    public function request($apiName, array $postData = [], $timeout = 30)
    {
        try {
            $serverUrl = $this->config->getServerUrl();
            if (empty($serverUrl)) {
                throw new ServerException('应用地址为空');
            }

            $appKey = $this->config->appKey;
            if (empty($appKey)) {
                throw new ServerException('应用密钥为空');
            }

            $appToken = $this->config->appToken;
            if (empty($appToken)) {
                throw new ServerException('应用令牌为空');
            }

            // 签名
            $timestamp = time();
            $signature = Signature::sign($appToken, $timestamp);

            // 请求头
            $header = [
                NameConstant::HEADER_NAME_API_KEY => $appKey,
                NameConstant::HEADER_NAME_API_NAME => $apiName,
                NameConstant::HEADER_NAME_API_TIMESTAMP => $timestamp,
                NameConstant::HEADER_NAME_API_SIGNATURE => $signature
            ];

            // 服务主机
            $serverIp = $this->config->getServerIpRand();
            if ($serverIp) {
                $parse = parse_url($serverUrl);
                $serverUrl = str_replace($parse['host'], $serverIp, $serverUrl);
                $header['Host'] = $parse['host'];
            }

            // 请求头
            $header = array_merge($header, $this->config->getServerHeader());

            // 选项
            $timeout = $timeout ? $timeout : $this->config->getTimeout();
            $option = [
                'timeout' => $timeout
            ];

            // 请求数据
            $requestPack = $this->getRequestPack();
            $requestData = $requestPack->pack($postData);

            // 请求接口
            $http = Http::getSingleton();
            $responseDataJson = $http->request($serverUrl, $requestData, $header, $option);

            // 结果处理
            $responsePack = $this->getResponsePack();
            $apiResponse = new ApiResponse($responseDataJson, $responsePack);
            $response = $apiResponse->getResponse();
            if (is_null($response)) {
                $extra = [
                    'header' => $http->getResponseHeader(),
                    'body' => $http->getResponseBody()
                ];
                return ApiResponse::serverError('接口请求失败', '', $extra, $responsePack);
            } else {
                return $apiResponse;
            }
        } catch (\Exception $e) {
            $responsePack = $this->getResponsePack();
            $extra = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            return ApiResponse::serverError('接口请求失败', '', $extra, $responsePack);
        }
    }

    /**
     * 获取配置
     *
     * @return HubConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 设置配置
     *
     * @param HubConfig $config
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
        return $this->requestPack ? $this->requestPack : $this->getDefaultRequestPack();
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
     * 获取默认请求打包对象
     *
     * @return RequestPack
     */
    protected function getDefaultRequestPack()
    {
        return RequestPack::getSingleton();
    }

    /**
     * 获取回复打包对象
     *
     * @return ResponsePack
     */
    public function getResponsePack()
    {
        return $this->responsePack ? $this->responsePack : $this->getDefaultResponsePack();
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
     * 获取默认回复打包对象
     *
     * @return ResponsePack
     */
    protected function getDefaultResponsePack()
    {
        return ResponsePack::getSingleton();
    }
}