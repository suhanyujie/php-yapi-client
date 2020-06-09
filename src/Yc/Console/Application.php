<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 2020-04-20
 * Time: 09:21
 */

namespace App\Yc\Console;

use App\Services\YapiService;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;

/**
 * yc 应用
 * 参考 https://github.com/composer/composer/blob/master/bin/composer
 *
 * Class Application
 * @package App\Yc\Console
 */
class Application extends BaseApplication
{
    protected $yc;

    protected $io;

    /**@var \Inhere\Console\Application */
    protected $app;

    /**
     * 字符串 logo
     * 参考 http://www.asciiarts.net/
     * @var string
     */
    private $logo = <<<LOGO
 __  __     ______    
/\ \_\ \   /\  ___\   
\ \____ \  \ \ \____  
 \/\_____\  \ \_____\ 
  \/_____/   \/_____/ 
                      
LOGO;

    public function __construct(string $name = 'yc', string $version = '0.1.0')
    {
        static $shutdownRegistered = false;

        parent::__construct($name, $version);
    }

    /**
     * @desc 设置配置信息
     */
    public function setConfig()
    {
        $this->app->setConfig(['name' => 'yapi commit helper']);
        $this->app->setConfig(['description' => 'yapi commit helper']);
        $this->app->setConfig(['logoText' => $this->logo]);
        $this->app->setConfig(['version' => VERSION]);
    }

    /**
     * @desc 应用执行
     */
    public function execute()
    {
        global $argv;
        $this->app = new \Inhere\Console\Application();
        $this->setConfig();
        $paramCount = count($argv);
        $this->customerRegist();
        if ($paramCount <= 2) {
            $this->app->showVersionInfo();
            $this->app->showHelpInfo();
            die;
        }
        $this->app->run();
    }

    /**
     * @desc 命令注册
     */
    public function customerRegist()
    {
        $this->app->command('file', function(Input $in, Output $out) {
            YapiService::doSaveOrUpdate();
        }, 'Enter a doc file path');

        $this->app->command('hello', function(Input $in, Output $out) {
            $out->info("hello terminal");
        }, 'this a test cmd');
    }

    /**
     * @desc 应用执行
     */
    public function run1(InputInterface $input, OutputInterface $output)
    {
        if (null === $output) {
            $output = Factory::createOutput();
        }

        return parent::run($input, $output);
    }
}
