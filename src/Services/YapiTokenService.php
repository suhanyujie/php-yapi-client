<?php

namespace App\Services;

use App\Libs\ConfigParse;

class YapiTokenService
{
    /**
     * token flag 列表
     */
    public static function listTokenFlag()
    {
        $config = ConfigParse::parseConfig();
        $tokenList = $config['token_section'];
        $tokenFlagList = array_keys($tokenList);
        $str = "token flag 列表如下：\n" .json_encode($tokenFlagList, 320);
        file_put_contents('php://output', $str);
    }
}
