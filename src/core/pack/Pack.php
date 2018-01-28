<?php

namespace newday\gateway\core\pack;

use newday\gateway\core\traits\InstanceTrait;

abstract class Pack
{
    // 实例trait
    use InstanceTrait;

    /**
     * 打包数据
     *
     * @param array $packData
     * @return string
     */
    abstract public function pack(array $packData);

    /**
     * 解包数据
     *
     * @param string $packDataJson
     * @return mixed
     */
    abstract public function unpack($packDataJson);

    /**
     * 编码内容
     *
     * @param string $content
     * @return string
     */
    public function encodeContent($content)
    {
        return base64_encode($content);
    }

    /**
     * 解码内容
     *
     * @param string $content
     * @return bool|string
     */
    public function decodeContent($content)
    {
        return base64_decode($content);
    }
}