<?php

namespace newday\gateway\core\interfaces;

interface RequestInterface
{

    /**
     * 获取server参数
     *
     * @param  string $name
     * @param  string $default
     * @return string|array
     */
    public function server($name = '', $default = null);

    /**
     * 获取header参数
     *
     * @param  string $name
     * @param  string $default
     * @return string|array
     */
    public function header($name = '', $default = null);

    /**
     * 获取param参数
     *
     * @param string $name
     * @param null $default
     * @return string|array
     */
    public function param($name = '', $default = null);

    /**
     * 获取原始输入
     *
     * @return string
     */
    public function input();

}