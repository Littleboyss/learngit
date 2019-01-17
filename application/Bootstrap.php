<?php
/**
  * @name Bootstrap
  * @author yantze
  * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
  * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
  * @see http://www.laruence.com/manual/yaf.class.bootstrap.html
  * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
  * 调用的次序, 和申明的次序相同
  **/
use \Yaf\Bootstrap_Abstract;
use \Yaf\Dispatcher;
use \Yaf\Application;
use \Yaf\Registry;
use \Yaf\Loader;
class Bootstrap extends Bootstrap_Abstract
{
    public function _initEnv(Dispatcher $dispatcher)
    {
        // 设置时区
        date_default_timezone_set('Asia/Shanghai');

        Loader::getInstance()->registerLocalNamespace(array('Ext', 'Eapi'));

        // 缓存配置文件信息
        $config = Application::app()->getConfig();
        Registry::set('config', $config);

        defined('ACCOUNT_DOMAIN') || define('ACCOUNT_DOMAIN', "http://account.{$config->env->domain}");
        defined('WWW_DOMAIN') || define('WWW_DOMAIN', "http://www.{$config->env->domain}");
        defined('USER_DOMAIN') || define('USER_DOMAIN', "http://user.{$config->env->domain}");
        defined('SAD_DOMAIN') || define('SAD_DOMAIN', "http://sad.{$config->env->domain}");
        defined('WAP_DOMAIN') || define('WAP_DOMAIN', "http://wap.{$config->env->domain}");

        // defined('LEASE_DOMAIN') || define('LEASE_DOMAIN', "http://lease_local.{$config->env->domain}:83");
        // defined('COMPANY_DOMAIN') || define("COMPANY_DOMAIN", "http://company_local.{$config->env->domain}:83");

        defined('LEASE_DOMAIN') || define('LEASE_DOMAIN', "http://lease.{$config->env->domain}");
        defined('COMPANY_DOMAIN') || define("COMPANY_DOMAIN", "http://company.{$config->env->domain}");

        defined('FILE_PATH') || define("FILE_PATH", "http://file.{$config->env->domain}/file/");
        defined('PHOTO_PATH') || define("PHOTO_PATH", "http://file.{$config->env->domain}/photo/");

        defined('WEB_STATIC_PATH') || define("WEB_STATIC_PATH", $config->env->static->path);

        defined('APP_DEBUG') || define('APP_DEBUG', $config->env->debug);

        // 科学计算默认保留3位小数
        bcscale(3);
    }

    /**
     * 日志系统初始化
     *
     * @return void
     */
    public function _initLog()
    {
        $log = new \Libs\Log\File([
            'file' => '/weblogs/php/starter_runtime.log', // 每个应用应该使用不同的日志文件
        ]);
        Registry::set('log', $log);
    }

    public function _initDebug(Dispatcher $dispatcher)
    {
        $debug = APP_DEBUG || E_Session::get('phpdebugbar');
        if (!$debug && isset($_GET['debug']) && $_GET['debug'] == 'true') {
            $debug = true;
            E_Session::set('phpdebugbar', true);
        }
        if ($debug) {
            // 调试工具
            $debugbar = new \DebugBar\StandardDebugBar();

            // ajax请求写入数据库
            $pdo = new PDO("mysql:host=10.5.3.64;dbname=debug", 'dev', 'dev963');
            $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->exec("SET NAMES utf8");
            $debugbar->setStorage(new DebugBar\Storage\PdoStorage($pdo));

            $debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector('eapi'));
            Registry::set('debugbar', $debugbar);
        }
    }
}
