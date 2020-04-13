#!/usr/bin/env php
#
<?php
$phar = new Phar('yc.phar');
$phar->buildFromDirectory(__DIR__, '/\.php$/');
$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();
$phar->setDefaultStub('src/index.php');


/*
## 参考
* https://blog.csdn.net/u011474028/article/details/54973571

*/