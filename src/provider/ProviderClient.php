<?php

namespace newday\gateway\provider;

use newday\gateway\support\Http;
use newday\gateway\support\Request;
use newday\gateway\core\Signature;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\exception\ServerException;
use newday\gateway\core\pack\ResponsePack;

class ProviderClient
{
    /**
     * 配置
     *
     * @var ProviderConfig
     */
    public $config;

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
     * @param ResponsePack $responsePack
     */
    public function __construct(ProviderConfig $config = null, ResponsePack $responsePack = null)
    {
        $config && $this->setConfig($config);
        $this->setResponsePack($responsePack);
    }

    /**
     * 请求接口
     *
     * @param string $apiClass
     * @param string $postData
     * @param int $timeout
     * @return ApiResponse
     */
    public function request($apiClass, $postData, $timeout = 0)
    {
        try {
            if (empty($apiClass)) {
                throw new ServerException('接口类名为空');
            }

            $serverUrl = $this->config->getServerUrl();
            if (empty($serverUrl)) {
                throw new ServerException('接口服务地址为空');
            }

            $serverToken = $this->config->getServerToken();
            if (empty($serverToken)) {
                throw new ServerException('接口服务令牌为空');
            }

            // 签名
            $timestamp = time();
            $signature = Signature::sign($serverToken, $timestamp);

            // 请求IP
            $requestIp = Request::getSingleton()->server('remote_addr');

            // 请求头
            $header = [
                NameConstant::HEADER_NAME_API_CLASS => $apiClass,
                NameConstant::HEADER_NAME_REQUEST_IP => $requestIp,
                NameConstant::HEADER_NAME_API_TIMESTAMP => $timestamp,
                NameConstant::HEADER_NAME_API_SIGNATURE => $signature
            ];

            // 随机主机
            $serverIp = $this->config->getServerIpRand();
            if ($serverIp) {
                $parse = parse_url($serverUrl);
                $serverUrl = str_replace($parse['host'], $serverIp, $serverUrl);
                $header['Host'] = $parse['host'];
            }

            $header = array_merge($header, $this->config->getServerHeader());

            // 选项
            $timeOut = $timeout ? $timeout : $this->config->getTimeout();
            $option = [
                'timeout' => $timeOut
            ];

            // 请求接口
            $http = Http::getSingleton();
            $responseDataJson = $http->request($serverUrl, $postData, $header, $option);

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