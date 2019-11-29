<?php

namespace newday\gateway\core\base;

abstract class Obj implements \JsonSerializable
{

    /**
     * 根据json构造对象
     *
     * @param string $jsonStr
     * @return null|static
     */
    public static function fromJson($jsonStr)
    {
        $array = json_decode($jsonStr, true);
        if (is_array($array)) {
            return static::fromArray($array);
        } else {
            return null;
        }
    }

    /**
     * 根据数组创建对象
     *
     * @param $array
     * @return null|static
     */
    public static function fromArray($array)
    {
        $object = new static();
        if ($object->validArray($array)) {
            $object->loadArray($array);
            return $object;
        } else {
            return null;
        }
    }

    /**
     * 获取数组
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * 是否合法数组
     *
     * @param array $array
     * @return bool
     */
    abstract public function validArray($array);

    /**
     * 加载数组
     *
     * @param array $array
     */
    abstract public function loadArray($array);

    /**
     * 转JSON
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see JsonSerializable::jsonSerialize()
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}