<?php

namespace newday\gateway\core;

class Signature
{

    /**
     * 生成签名
     *
     * @param string $token
     * @param int $timestamp
     * @return string
     */
    public static function sign($token, $timestamp)
    {
        return md5(base64_decode($token . '_' . $timestamp));
    }

}