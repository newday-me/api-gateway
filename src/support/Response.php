<?php

namespace newday\gateway\support;

use newday\gateway\core\traits\InstanceTrait;

class Response
{
    /**
     * 实例trait
     */
    use InstanceTrait;

    /**
     * 输出内容
     *
     * @param mixed $data
     * @param array $header
     */
    public function send($data = '', $header = [])
    {
        // 清空输出
        ob_clean();

        // 返回头
        foreach ($header as $co => $vo) {
            header($co . ': ' . $vo);
        }

        // 内容
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        echo $data;

        // 退出
        die();
    }

}