<?php

namespace newday\gateway\core\support;

class Response
{
    /**
     * 内容
     *
     * @var string
     */
    protected $content;

    /**
     * 头信息
     *
     * @var array
     */
    protected $header;

    /**
     * Response constructor.
     * @param string $content
     * @param array $header
     */
    public function __construct($content, $header = [])
    {
        $this->content = $content;
        $this->header = $header;
    }

    /**
     * 获取内容
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 设置内容
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * 获取头信息
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * 设置头信息
     *
     * @param array $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * 输出内容
     */
    public function send()
    {
        ob_get_clean();

        // 头信息
        foreach ($this->getHeader() as $co => $vo) {
            @header($this->ucWords($co) . ': ' . $vo);
        }

        // 内容
        echo $this->getContent();

        exit();
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