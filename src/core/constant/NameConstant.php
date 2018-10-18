<?php

namespace newday\gateway\core\constant;

class NameConstant
{
    /**
     * 接口key
     */
    const HEADER_NAME_API_KEY = 'Api-Key';

    /**
     * 接口时间
     */
    const HEADER_NAME_API_TIMESTAMP = 'Api-Time';

    /**
     * 接口签名
     */
    const HEADER_NAME_API_SIGNATURE = 'Api-Sign';

    /**
     * 接口名称
     */
    const HEADER_NAME_API_NAME = 'Api-Name';

    /**
     * 接口类名
     */
    const HEADER_NAME_API_CLASS = 'Api-Class';

    /**
     * 接口列表类
     */
    const CLASS_API_LIST = '/_/api/list';

    /**
     * 接口介绍类
     */
    const CLASS_API_INTRO = '/_/api/intro';
}