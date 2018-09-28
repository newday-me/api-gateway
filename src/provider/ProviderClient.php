<?php

namespace newday\gateway\provider;

use newday\gateway\support\Http;
use newday\gateway\support\Request;
use newday\gateway\core\Signature;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\traits\PackTrait;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\exception\ServerException;
use newday\gateway\core\objects\ResponseObject;

class ProviderClient
{
    use PackTrait;

    /**
     * 配置
     *
     * @var ProviderConfig
     */
    public $config;

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
            $config = $this->getConfig();

            if (empty($apiClass)) {
                throw new ServerException('接口类名为空');
            }

            $serverUrl = $config->getServerUrl();
            if (empty($serverUrl)) {
                throw new ServerException('接口服务地址为空');
            }

            $serverToken = $config->getServerToken();
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
            $serverIp = $config->getServerIpRand();
            if ($serverIp) {
                $parse = parse_url($serverUrl);
                $serverUrl = str_replace($parse['host'], $serverIp, $serverUrl);
                $header['Host'] = $parse['host'];
            }

            $header = array_merge($header, $config->getServerHeader());

            // 请求选项
            $timeOut = $timeout ? $timeout : $config->getTimeout();
            $option = [
                'timeout' => $timeOut
            ];

            // 请求接口
            $http = Http::getInstance();
            $responseDataJson = $http->request($serverUrl, $postData, $header, $option);

            // 响应数据
            $responsePack = $this->getResponsePack();
            $responseObject = $responsePack->unpack($responseDataJson);
            if (is_null($responseObject)) {
                $extra = [
                    'header' => $http->getResponseHeader(),
                    'body' => $http->getResponseBody()
                ];
                $responseObject = ResponseObject::makeError('接口请求失败', '', $extra);
            }

            return new ApiResponse($responseObject, $responsePack);
        } catch (\Exception $e) {
            $responsePack = $this->getResponsePack();
            $extra = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
            $responseObject = ResponseObject::makeError('接口请求失败', '', $extra);
            return new ApiResponse($responseObject, $responsePack);
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

}