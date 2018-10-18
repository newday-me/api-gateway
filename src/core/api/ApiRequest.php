<?php

namespace newday\gateway\core\api;

use newday\gateway\core\objects\RequestObject;

class ApiRequest
{

    /**
     * 请求对象
     *
     * @var RequestObject
     */
    protected $request;

    /**
     * 构造函数
     *
     * @param RequestObject $request
     */
    public function __construct($request = null)
    {
        $request && $this->setRequest($request);
    }

    /**
     * 设置请求数据
     *
     * @param RequestObject $request
     */
    public function setRequest(RequestObject $request)
    {
        $this->request = $request;
    }

    /**
     * 获取param参数
     *
     * @param $name
     * @param null $default
     * @return array|string
     */
    public function getParam($name = '', $default = null)
    {
        if (empty($this->request)) {
            return $default;
        } else {
            return $this->request->getParam($name, $default);
        }
    }

    /**
     * 获取file参数
     *
     * @param string $name
     * @param null $default
     * @return null|array
     */
    public function getFile($name = '', $default = null)
    {
        if (empty($this->request)) {
            return null;
        } else {
            return $this->request->getFile($name, $default);
        }
    }

}