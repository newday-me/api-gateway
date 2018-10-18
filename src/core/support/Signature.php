<?php

namespace newday\gateway\core\support;

use newday\gateway\core\interfaces\SignatureInterface;

class Signature implements SignatureInterface
{

    /**
     * 生成签名
     *
     * @param string $token
     * @param int $timestamp
     * @return string
     */
    public function generate($token, $timestamp)
    {
        return md5(base64_decode($token . '_' . $timestamp));
    }

}