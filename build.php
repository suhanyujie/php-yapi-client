#!/usr/bin/env php
#
<?php
$phar = new Phar('yc.phar');
$phar->buildFromDirectory(__DIR__, '/\.php$/');
$phar->addFile('.env');
$phar->compressFiles(Phar::GZ);
$entry = 'src/index.php';
// $phar->setStub(file_get_contents($entry));
$phar->setDefaultStub($entry);
$phar->stopBuffering();

/*
## 参考
* https://blog.csdn.net/u011474028/article/details/54973571
* https://www.ctolib.com/topics-121626.html


*/
?>
