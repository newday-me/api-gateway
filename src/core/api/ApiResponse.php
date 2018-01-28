<?php

namespace newday\gateway\core\api;

use newday\gateway\core\pack\Pack;
use newday\gateway\core\pack\ResponsePack;
use newday\gateway\core\constant\DataConstant;
use newday\gateway\core\objects\ResponseObject;
use newday\gateway\support\Response;

class ApiResponse
{

    /**
     * 回复对象
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
     * @param string $responseData
     * @param ResponsePack $pack
     */
    public function __construct($responseData = null, ResponsePack $pack = null)
    {
        $pack && $this->setPack($pack);
        $responseData && $this->loadResponseData($responseData);
    }

    /**
     * 服务失败
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @param ResponsePack $pack
     * @return static
     */
    public static function serverError($msg = 'error', $data = '', $extra = [], ResponsePack $pack = null)
    {
        $type = DataConstant::DATA_TYPE_SERVER;
        $code = DataConstant::SERVER_ERROR;
        return static::make($type, $code, $msg, $data, $extra, $pack);
    }

    /**
     * 接口成功
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @param ResponsePack $pack
     * @return static
     */
    public static function apiSuccess($msg = 'success', $data = '', $extra = [], ResponsePack $pack = null)
    {
        return static::apiData(DataConstant::API_SUCCESS, $msg, $data, $extra, $pack);
    }

    /**
     * 接口失败
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @param ResponsePack $pack
     * @return static
     */
    public static function apiError($msg = 'error', $data = '', $extra = [], ResponsePack $pack = null)
    {
        return static::apiData(DataConstant::API_ERROR, $msg, $data, $extra, $pack);
    }

    /**
     * 接口数据
     *
     * @param $code
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @param ResponsePack $pack
     * @return static
     */
    public static function apiData($code, $msg = 'error', $data = '', $extra = [], ResponsePack $pack = null)
    {
        $type = DataConstant::DATA_TYPE_API;
        return static::make($type, $code, $msg, $data, $extra, $pack);
    }

    /**
     * 构造对象
     *
     * @param int $type
     * @param int $code
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @param ResponsePack $pack
     * @return static
     */
    public static function make($type, $code, $msg = '', $data = '', $extra = [], ResponsePack $pack = null)
    {
        $object = new ResponseObject();
        $object->setType($type);
        $object->setCode($code);
        $object->setMsg($msg);
        $object->setData($data);
        $object->setExtra($extra);

        $instance = new static();
        $pack && $instance->setPack($pack);
        $instance->setResponse($object);
        return $instance;
    }

    /**
     * 加载回复数据
     *
     * @param string $responseData
     * @return ResponseObject|null
     */
    public function loadResponseData($responseData)
    {
        $pack = $this->getPack();
        $response = $pack->unpack($responseData);
        $this->setResponse($response);
        return $response;
    }

    /**
     * 输出回复
     */
    public function send()
    {
        $pack = $this->getPack();
        $content = $pack->pack($this->getResponse()->toArray());
        Response::getSingleton()->send($content);
    }

    /**
     * 获取回复
     *
     * @return ResponseObject
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 设置回复
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
    public function getPack()
    {
        return $this->pack ? $this->pack : $this->getDefaultPack();
    }

    /**
     * 设置打包对象
     *
     * @param ResponsePack $pack
     */
    public function setPack(ResponsePack $pack)
    {
        $this->pack = $pack;
    }

    /**
     *  获取打包对象
     *
     * @return ResponsePack
     */
    public function getDefaultPack()
    {
        return ResponsePack::getSingleton();
    }

}











