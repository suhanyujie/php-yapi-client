<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-11
 * Time: 11:16
 */

namespace App\Libs;

/**
 * 配置解析
 * Class ConfigParse
 * @package App\Libs
 */
class ConfigParse
{
    public static $config = [];

    // 解析配置文件
    public static function parseConfig(): array
    {
        if (!self::$config) {
            //解析配置文件
            self::$config = parse_ini_file(ROOT . "/.env", true);
        }

        return self::$config;
    }
}
