<?php

namespace newday\gateway\core\api;

use newday\gateway\support\Response;
use newday\gateway\core\pack\ResponsePack;
use newday\gateway\core\objects\ResponseObject;

class ApiResponse
{

    /**
     * 响应对象
     *
     * @var ResponseObject
     */
    protected $response;

    /**
     * 打包对象
     *
     * @var ResponsePack
     */
    protected $pack;

    /**
     * 构造函数
     *
     * @param ResponseObject $response
     * @param ResponsePack $pack
     */
    public function __construct($response = null, ResponsePack $pack = null)
    {
        $response && $this->setResponse($response);
        $pack && $this->setResponsePack($pack);
    }

    /**
     * 输出响应
     */
    public function send()
    {
        $pack = $this->getResponsePack();
        $data = $this->getResponse()->toArray();
        $content = $pack->pack($data);
        Response::getSingleton()->send($content);
    }

    /**
     * 获取响应
     *
     * @return ResponseObject
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 设置响应
     *
     * @param ResponseObject $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * 获取打包对象
     *
     * @return ResponsePack
     */
    public function getResponsePack()
    {
        return $this->pack;
    }

    /**
     * 设置打包对象
     *
     * @param ResponsePack $pack
     */
    public function setResponsePack(ResponsePack $pack)
    {
        $this->pack = $pack;
    }

}











