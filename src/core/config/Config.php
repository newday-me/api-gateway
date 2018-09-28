<?php

namespace newday\gateway\core\config;

class Config
{
    /**
     * 分割符
     */
    const CHAR_DIVIDE = '|';

    /**
     * 换行符
     */
    const CHAR_LINE = "\n";

    /**
     * 服务链接
     *
     * @var string
     */
    protected $serverUrl;

    /**
     * 服务IP列表
     *
     * @var array
     */
    protected $serverIpList = [];

    /**
     * 服务请求头
     *
     * @var array
     */
    protected $serverHeader = [];

    /**
     * 超时时间
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * 获取服务链接
     *
     * @return string
     */
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * 设置服务链接
     *
     * @param string $serverUrl
     */
    public function setServerUrl($serverUrl)
    {
        $this->serverUrl = $serverUrl;
    }

    /**
     * 获取服务IP列表
     *
     * @return array
     */
    public function getServerIpList()
    {
        return $this->serverIpList;
    }

    /**
     * 设置服务IP列表
     *
     * @param array $serverIpList
     */
    public function setServerIpList($serverIpList)
    {
        $this->serverIpList = $serverIpList;
    }

    /**
     * 设置服务IP列表的字符串
     *
     * @param $serverIpListStr
     */
    public function setServerIpListStr($serverIpListStr)
    {
        if (strpos($serverIpListStr, static::CHAR_DIVIDE)) {
            $list = explode(static::CHAR_LINE, $serverIpListStr);
            $serverIpList = [];
            foreach ($list as $vo) {
                if (!strpos($vo, static::CHAR_DIVIDE)) {
                    continue;
                }

                list($host, $weight) = explode(static::CHAR_DIVIDE, $vo);
                $serverIpList[] = [
                    'ip' => $host,
                    'weight' => $weight
                ];
            }
            $this->setserverIpList($serverIpList);
        }
    }

    /**
     * 获取随机服务IP
     *
     * @return string
     */
    public function getServerIpRand()
    {
        $sum = 0;
        $serverIpList = $this->getserverIpList();
        foreach ($serverIpList as $vo) {
            $sum += $vo['weight'];
        }

        $rand = rand(1, $sum);
        $num = 0;
        foreach ($serverIpList as $vo) {
            $num += $vo['weight'];
            if ($num >= $rand) {
                return $vo['ip'];
            }
        }
        return null;
    }

    /**
     * 设置服务请求头json
     *
     * @param string $serverHeaderJson
     */
    public function setServerHeaderJson($serverHeaderJson)
    {
        $serverHeader = json_encode($serverHeaderJson, true);
        is_array($serverHeader) && $this->setServerHeader($serverHeader);
    }

    /**
     * 获取服务请求头
     *
     * @return array
     */
    public function getServerHeader()
    {
        return $this->serverHeader;
    }

    /**
     * 设置服务请求头
     *
     * @param array $serverHeader
     */
    public function setServerHeader($serverHeader)
    {
        $this->serverHeader = $serverHeader;
    }

    /**
     * 获取超时时间
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * 设置超时时间
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
}