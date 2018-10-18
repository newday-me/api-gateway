<?php

namespace newday\gateway\core\objects;

use newday\gateway\core\base\Object;

class RequestObject extends Object
{

    /**
     * 参数
     *
     * @var array
     */
    protected $param = [];

    /**
     * 文件
     *
     * @var array
     */
    protected $file = [];

    /**
     * 获取参数
     *
     * @param string $name
     * @param null $default
     * @return array|string|null
     */
    public function getParam($name = '', $default = null)
    {
        if ($name === '') {
            return $this->param;
        } else {
            return isset($this->param[$name]) ? $this->param[$name] : $default;
        }
    }

    /**
     * 获取文件
     *
     * @param string $name
     * @param null $default
     * @return array|string|null
     */
    public function getFile($name = '', $default = null)
    {
        if ($name === '') {
            return $this->file;
        } else {
            return isset($this->file[$name]) ? $this->file[$name] : $default;
        }
    }

    /**
     * 设置参数
     *
     * @param mixed $field
     * @param string $value
     */
    public function setParam($field, $value = null)
    {
        if (is_array($field)) {
            $this->param = $field;
        }
        if (is_null($value)) {
            unset($this->param[$field]);
        } else {
            $this->param[$field] = $value;
        }
    }

    /**
     * 设置文件
     *
     * @param mixed $field
     * @param array $value
     */
    public function setFile($field, $value = null)
    {
        if (is_array($field)) {
            $this->file = $field;
        }
        if (is_null($value)) {
            unset($this->file[$field]);
        } else {
            $this->file[$field] = $value;
        }
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
            'param' => $this->param,
            'file' => $this->file
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
        if (is_array($array) && isset($array['param']) && isset($array['file'])) {
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
            if (is_array($array['param'])) {
                $this->param = $array['param'];
            }

            if (is_array($array['file'])) {
                $this->file = $array['file'];
            }
        }
    }
}