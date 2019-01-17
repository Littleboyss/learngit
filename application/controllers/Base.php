<?php
/**
 * 视图布局控制器
 */
use \Yaf\Registry;
use \Yaf\Session;
abstract class BaseController extends \Libs\Controller\Base
{
    // 当前登录用户信息
    protected $_loginUser = null;

    public function init()
    {
        parent::init();

        $this->_loginUser = Session::getInstance()->user;
        // 检测session是否完整
        if ($this->_loginUser && !isset($this->_loginUser['user_mobile'])) {
            Session::getInstance()->user = null;
            $this->_loginUser = null;
        }

        $this->_view->loginUser = $this->_loginUser;
    }
}
