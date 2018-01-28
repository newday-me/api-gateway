<?php

namespace newday\gateway\core\objects;

use newday\gateway\core\constant\DataConstant;

class ResponseObject extends Object
{

    /**
     * 结果类型
     *
     * @var int
     */
    protected $type = DataConstant::DATA_TYPE_API;

    /**
     *  状态码
     *
     * @var int
     */
    protected $code = DataConstant::API_ERROR;

    /**
     * 提示
     *
     * @var string
     */
    protected $msg = '';

    /**
     * 数据
     *
     * @var mixed
     */
    protected $data = '';

    /**
     * 额外数据
     *
     * @var mixed
     */
    protected $extra = [];

    /**
     * 是否服务成功
     *
     * @return bool
     */
    public function isServerSuccess()
    {
        return $this->getType() == DataConstant::DATA_TYPE_API;
    }

    /**
     * 是否接口成功
     *
     * @return bool
     */
    public function isApiSuccess()
    {
        return $this->getCode() == DataConstant::API_SUCCESS;
    }

    /**
     * 获取类型
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 设置类型
     *
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * 获取状态码
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 设置状态码
     *
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * 获取提示
     *
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * 设置提示
     *
     * @param string $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * 获取数据
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 设置数据
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 获取额外数据
     *
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * 设置额外数据
     *
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Object::toArray()
     */
    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'code' => $this->getCode(),
            'msg' => $this->getMsg(),
            'data' => $this->getData(),
            'extra' => $this->getExtra()
        ];
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Object::validArray()
     */
    public function validArray($array)
    {
        if (is_array($array) && isset($array['type']) && isset($array['code'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Object::loadArray()
     */
    public function loadArray($array)
    {
        if ($this->validArray($array)) {
            isset($array['type']) && $this->setType($array['type']);
            isset($array['code']) && $this->setCode($array['code']);
            isset($array['msg']) && $this->setMsg($array['msg']);
            isset($array['data']) && $this->setData($array['data']);
            isset($array['extra']) && $this->setExtra($array['extra']);
        }
    }

}