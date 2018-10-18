<?php

namespace newday\gateway\core\pack;

use newday\gateway\core\interfaces\PackInterface;
use newday\gateway\core\objects\ResponseObject;

class ResponsePack implements PackInterface
{
    /**
     * 打包数据
     *
     * @param array $packData
     * @return string
     */
    public function pack(array $packData)
    {
        $object = new ResponseObject();
        $object->loadArray($packData);
        return $object->toJson();
    }

    /**
     * 解码数据
     *
     * @param string $packDataJson
     * @return null|ResponseObject
     */
    public function unpack($packDataJson)
    {
        return ResponseObject::fromJson($packDataJson);
    }

}