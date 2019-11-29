<?php

namespace newday\gateway\core\objects;

use newday\gateway\core\base\Obj;

class IntroObject extends Obj
{

    /**
     * 接口类名
     *
     * @var string
     */
    protected $class;

    /**
     * 接口名
     *
     * @var string
     */
    protected $name;

    /**
     * 接口介绍
     *
     * @var string
     */
    protected $info = '';

    /**
     * 接口输入
     *
     * @var array
     */
    protected $input = [];

    /**
     * 接口输出
     *
     * @var array
     */
    protected $output = [];

    /**
     * 获取接口类名
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * 设置接口类名
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * 获取接口名
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 设置接口名
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 获取接口介绍
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * 设置接口描述
     *
     * @param $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * 获取接口输入
     *
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * 设置接口输入
     *
     * @param array $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * 获取接口输出
     *
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * 设置接口输出
     *
     * @param array $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * 添加输出
     *
     * @param mixed $output
     */
    public function addOutput($output)
    {
        $this->output[] = $output;
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
            'class' => $this->class,
            'name' => $this->name,
            'info' => $this->info,
            'input' => $this->input,
            'output' => $this->output
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
        if (is_array($array) && isset($array['class']) && isset($array['name'])) {
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
            isset($array['class']) && $this->setClass($array['class']);
            isset($array['name']) && $this->setName($array['name']);
            isset($array['info']) && $this->setInfo($array['info']);
            isset($array['input']) && $this->setInput($array['input']);
            isset($array['output']) && $this->setOutput($array['output']);
        }
    }

}