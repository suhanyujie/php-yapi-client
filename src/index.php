#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-07
 * Time: 16:53
 */

define('ROOT', dirname(dirname(__FILE__)));
define('VERSION', '0.1.1');
include_once ROOT . "/vendor/autoload.php";

use App\Libs\ConfigParse;
use App\Services\YapiService;

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') {
    echo 'Warning: yc should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
}
(new \App\Yc\Console\Application)->execute();
die;
?>
