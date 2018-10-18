<?php

namespace newday\gateway\core\interfaces;

interface HttpInterface
{

    /**
     * 获取状态码
     *
     * @return int
     */
    public function getResponseCode();

    /**
     * 获取响应头
     *
     * @return string
     */
    public function getResponseHeader();


    /**
     * 获取响应正文
     *
     * @return string
     */
    public function getResponseBody();

    /**
     * 请求
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @param array $option
     * @return string
     */
    public function request($url, $data = null, $header = null, $option = []);

}