<?php

namespace newday\gateway\hub;

use newday\gateway\support\Http;
use newday\gateway\core\Signature;
use newday\gateway\core\traits\PackTrait;
use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\objects\ResponseObject;
use newday\gateway\core\exception\ServerException;

class HubClient
{
    use PackTrait;

    /**
     * 配置
     *
     * @var HubConfig
     */
    protected $config;

    /**
     * 构造函数
     *
     * @param HubConfig $config
     */
    public function __construct(HubConfig $config = null)
    {
        $config && $this->setConfig($config);
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
            $config = $this->getConfig();

            $serverUrl = $config->getServerUrl();
            if (empty($serverUrl)) {
                throw new ServerException('应用服务地址为空');
            }

            $appKey = $config->getAppKey();
            if (empty($appKey)) {
                throw new ServerException('应用密钥为空');
            }

            $appToken = $config->getAppToken();
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
            $serverIp = $config->getServerIpRand();
            if ($serverIp) {
                $parse = parse_url($serverUrl);
                $serverUrl = str_replace($parse['host'], $serverIp, $serverUrl);
                $header['Host'] = $parse['host'];
            }

            // 请求头
            $header = array_merge($header, $config->getServerHeader());

            // 请求选项
            $timeout = $timeout ? $timeout : $config->getTimeout();
            $option = [
                'timeout' => $timeout
            ];

            // 请求数据
            $requestPack = $this->getRequestPack();
            $requestData = $requestPack->pack($postData);

            // 请求接口
            $http = Http::getInstance();
            $responseDataJson = $http->request($serverUrl, $requestData, $header, $option);

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

}