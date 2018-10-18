<?php

namespace newday\gateway\core\interfaces;

interface PackInterface
{
    /**
     * 打包数据
     *
     * @param array $packData
     * @return string
     */
    public function pack(array $packData);

    /**
     * 解包数据
     *
     * @param string $packDataJson
     * @return mixed
     */
    public function unpack($packDataJson);
}