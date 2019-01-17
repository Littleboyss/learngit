<?php
// 命令行控制器，所有的命令行业务应该继承此控制器
abstract class CliController extends Controller
{
    protected $_logHandler = null;

    public function init()
    {
        parent::init();
        $this->_logHandler = new E_Log_File([
            'file' => '/weblogs/php/starter_cli.log', // 每个应用应该使用不同的日志文件
        ]);
        Yaf_Dispatcher::getInstance()->disableView();
    }
}
