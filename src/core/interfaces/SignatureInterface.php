<?php

namespace newday\gateway\core\interfaces;

interface SignatureInterface
{

    /**
     * 生成签名
     *
     * @param string $token
     * @param int $timestamp
     * @return string
     */
    public function generate($token, $timestamp);

}