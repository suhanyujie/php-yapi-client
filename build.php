#!/usr/bin/env php
#
<?php
$phar = new Phar('yc.phar');
$phar->buildFromDirectory(__DIR__, '/\.php$/');
$phar->addFile('.env');
$phar->compressFiles(Phar::GZ);
// setStub 设定启动器，需要拼接上 `#!/usr/bin/env php\n`
// 表示使用环境变量中的 php **解释执行**
$phar->setStub("#!/usr/bin/env php\n".Phar::createDefaultStub('src/index.php'));
$entry = 'src/index.php';
$phar->stopBuffering();

/*
## 参考
* https://blog.csdn.net/u011474028/article/details/54973571
* https://www.ctolib.com/topics-121626.html


*/
?>
