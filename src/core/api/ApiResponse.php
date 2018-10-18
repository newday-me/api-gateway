<?php

namespace newday\gateway\core\api;

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
     * 构造函数
     *
     * @param ResponseObject $response
     */
    public function __construct($response = null)
    {
        $response && $this->setResponse($response);
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
     * 是否成功
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->response->isSuccess();
    }

    /**
     * 获取状态码
     *
     * @return int
     */
    public function getCode()
    {
        return $this->response->getCode();
    }

    /**
     * 获取提示
     *
     * @return string
     */
    public function getMsg()
    {
        return $this->response->getMsg();
    }

    /**
     * 获取数据
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->response->getData();
    }

    /**
     * 获取额外数据
     *
     * @return mixed
     */
    public function getExtra()
    {
        return $this->response->getExtra();
    }

}











