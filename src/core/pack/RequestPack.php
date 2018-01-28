<?php

namespace newday\gateway\core\pack;

use CURLFile;
use newday\gateway\core\objects\RequestObject;

class RequestPack extends Pack
{
    /**
     * 临时文件
     *
     * @var array
     */
    protected $tempFiles = [];

    /**
     * 打包数据
     *
     * @param array $packData
     * @return string
     */
    public function pack(array $packData)
    {
        $object = new RequestObject();
        foreach ($packData as $co => $vo) {
            if (class_exists(CURLFile::class) && $vo instanceof CURLFile) {
                $object->setFile($co, $this->packFile($vo->getFilename()));
            } elseif (is_string($vo) && substr($vo, 0, 1) == '@' && is_file($file = substr($vo, 1))) {
                $object->setFile($co, $this->packFile($file));
            } elseif (is_array($vo) || is_object($vo)) {
                $object->setParam($co, $this->encodeContent(json_encode($vo)));
            } else {
                $object->setParam($co, $this->encodeContent($vo));
            }
        }
        return json_encode($object);
    }

    /**
     * 解码数据
     *
     * @param string $packDataJson
     * @return null|RequestObject
     */
    public function unpack($packDataJson)
    {
        $packData = json_decode($packDataJson, true);
        if (is_array($packData) && isset($packData['param']) && isset($packData['file'])) {
            $object = new RequestObject();

            // 处理数据
            foreach ($packData['param'] as $co => $vo) {
                $object->setParam($co, $this->decodeContent($vo));
            }

            // 处理文件
            $tempPath = sys_get_temp_dir();
            foreach ($packData['file'] as $co => $vo) {
                $tempFile = tempnam($tempPath, 'pac');
                if (is_writable($tempFile)) {
                    $content = $this->decodeContent($vo['content']);
                    file_put_contents($tempFile, $content);

                    // 临时文件
                    $vo['tmp_name'] = $tempFile;
                    $this->tempFiles[] = $tempFile;
                } else {
                    $vo['error'] = UPLOAD_ERR_CANT_WRITE;
                }

                unset($vo['content']);
                $object->setFile($co, $vo);
            }

            return $object;
        } else {
            return null;
        }
    }

    /**
     * 打包文件
     *
     * @param string $file
     * @return array
     */
    public function packFile($file)
    {
        $name = basename($file);
        if (is_file($file) && is_readable($file)) {
            $type = mime_content_type($file);
            $content = file_get_contents($file);
            return [
                'name' => $name,
                'type' => $type,
                'tmp_name' => '',
                'error' => UPLOAD_ERR_OK,
                'size' => strlen($content),
                'content' => $this->encodeContent($content)
            ];
        } else {
            return [
                'name' => $name,
                'type' => '',
                'tmp_name' => '',
                'error' => UPLOAD_ERR_NO_FILE,
                'size' => 0
            ];
        }
    }

    /**
     * 析构函数
     */
    public function __construct()
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }
    }

}