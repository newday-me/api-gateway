<?php

namespace newday\gateway\core\support;

use newday\gateway\core\interfaces\HttpInterface;

class Http implements HttpInterface
{
    /**
     * 状态码
     *
     * @var integer
     */
    protected $code;

    /**
     * 头信息
     *
     * @var string
     */
    protected $header;

    /**
     * 正文
     *
     * @var string
     */
    protected $body;

    /**
     * 获取状态码
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->code;
    }

    /**
     * 获取响应头
     *
     * @return string
     */
    public function getResponseHeader()
    {
        return $this->header;
    }

    /**
     * 获取响应正文
     *
     * @return string
     */
    public function getResponseBody()
    {
        return $this->body;
    }

    /**
     * 请求
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @param array $option
     * @return string
     */
    public function request($url, $data = null, $header = null, $option = [])
    {
        $this->resetHttp();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        // 请求头
        if (!empty($header)) {
            $headerStrArr = $this->getHeaderStrArr($header);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerStrArr);
        }

        // HTTPS
        if (strpos($url, 'https://') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        // 提交数据
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        // 代理设置
        if (isset($option['proxy']) && is_array($option['proxy'])) {
            $proxy = $option['proxy'];
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, $proxy['host']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);

            // 身份
            if (isset($proxy['user']) && isset($proxy['password'])) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['user'] . ':' . $proxy['password']);
            }
        }

        // 超时时间
        if (isset($option['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $option['timeout']);
        }

        // 跟随跳转
        if (isset($option['location'])) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $option['location']);
        }

        $content = curl_exec($ch);

        // 请求错误
        if (curl_errno($ch)) {
            return null;
        }

        // 分离头和正文
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->header = substr($content, 0, $headerSize);
        $this->body = substr($content, $headerSize);

        curl_close($ch);

        if (isset($option['header']) && $option['header']) {
            return $content;
        } else {
            return $this->body;
        }
    }

    /**
     * 重置对象
     */
    protected function resetHttp()
    {
        $this->code = null;
        $this->header = null;
        $this->body = null;
    }

    /**
     * 头信息数组
     *
     * @param array $header
     * @return array
     */
    protected function getHeaderStrArr($header)
    {
        $headerStr = [];
        foreach ($header as $co => $vo) {
            $headerStr[] = $this->ucWords($co) . ': ' . $vo;
        }
        return $headerStr;
    }

    /**
     * 首字母大写
     *
     * @param string $word
     * @return string
     */
    protected function ucWords($word)
    {
        $arr = explode('-', $word);
        $list = [];
        foreach ($arr as $vo) {
            $list[] = ucfirst($vo);
        }
        return implode('-', $list);
    }
}