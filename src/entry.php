#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-13
 * Time: 10:45
 */

// Avoid APC causing random fatal errors per https://github.com/composer/composer/issues/264
if (extension_loaded('apc') && filter_var(ini_get('apc.enable_cli'), FILTER_VALIDATE_BOOLEAN) && filter_var(ini_get('apc.cache_by_default'), FILTER_VALIDATE_BOOLEAN)) {
    if (version_compare(phpversion('apc'), '3.0.12', '>=')) {
        ini_set('apc.cache_by_default', 0);
    } else {
        fwrite(STDERR, 'Warning: APC <= 3.0.12 may cause fatal errors when running composer commands.'.PHP_EOL);
        fwrite(STDERR, 'Update APC, or set apc.enable_cli or apc.cache_by_default to 0 in your php.ini.'.PHP_EOL);
    }
}

Phar::mapPhar('yc.phar');

require 'phar://yc.phar';
__HALT_COMPILER();


/*
## 参考
- https://github.com/composer/composer/blob/master/src/Composer/Compiler.php

*/
?>
