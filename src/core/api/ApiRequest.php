<?php

namespace newday\gateway\core\api;

use newday\gateway\support\Request;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\pack\RequestPack;
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
     * 打包对象
     *
     * @var RequestPack
     */
    protected $pack;

    /**
     * 构造函数
     *
     * @param string $requestData
     * @param RequestPack $pack
     */
    public function __construct($requestData = null, RequestPack $pack = null)
    {
        $pack && $this->setPack($pack);
        $requestData && $this->loadRequestData($requestData);
    }

    /**
     * 加载请求数据
     *
     * @param string $requestData
     * @return RequestObject|null
     */
    public function loadRequestData($requestData)
    {
        $pack = $this->getPack();
        $request = $pack->unpack($requestData);
        $this->setRequest($request);
        return $request;
    }

    /**
     * 获取请求对象
     *
     * @return RequestObject
     */
    public function getRequest()
    {
        return $this->request;
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
     * 获取打包对象
     *
     * @return RequestPack
     */
    public function getPack()
    {
        return $this->pack ? $this->pack : $this->getDefaultPack();
    }

    /**
     * 设置打包对象
     *
     * @param RequestPack $pack
     */
    public function setPack(RequestPack $pack)
    {
        $this->pack = $pack;
    }

    /**
     * 获取请求IP
     *
     * @return string
     */
    public function getRequestIp()
    {
        return Request::getSingleton()->header(NameConstant::HEADER_NAME_REQUEST_IP);
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

    /**
     *  获取打包对象
     *
     * @return RequestPack
     */
    public function getDefaultPack()
    {
        return RequestPack::getSingleton();
    }

}