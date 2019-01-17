<?php
/**
 * 视图布局控制器
 */
use \Yaf\Registry;
abstract class LayoutController extends BaseController
{
    protected $_layout = 'layout';

    // 当前用户角色信息
    protected $_roleInfo = null;

    // 不需要验证用户登录的控制器或action
    protected $_freeActions = [
        'index',
    ];

    // 验证当前请求是否需要验证用户身份
    protected function isFreeAction()
    {
        $controller = strtolower($this->_request->controller);
        $action = strtolower($this->_request->action);
        return in_array($controller, $this->_freeActions) || in_array("{$controller}/{$action}", $this->_freeActions);
    }

    // 初始化
    public function init()
    {
        parent::init();

        if (!$this->_loginUser) {
            if (!$this->isFreeAction()) {
                if ($this->isAjax()) {
                    return $this->error('请登录后在进行该操作！', -1, ['status' => 'not_login']);
                } else {
                    return $this->redirect(ACCOUNT_DOMAIN . '/user/login');
                }
            }
        }
        
        // todo 获取应用相关的角色信息
        $this->_view->roleInfo = $this->_roleInfo;

        $this->_view->thirdStatic = 'http://static.wujigang.cn/uiLibs';

        $this->_view->staticPath = WEB_STATIC_PATH;
        $config = Registry::get('config');
        $this->_view->staticVersion = $config->env->static->version;
    }

    // 增加布局方式
    protected function render($tpl, array $tpl_vars = null)
    {
        // 头部样式文件
        $this->_view->css = $this->_css;
        $this->_view->thirdCss = $this->_thirdCss;

        // 底部js文件
        $this->_view->js = $this->_js;
        $this->_view->thirdJs = $this->_thirdJs;

        // 主内容
        if (!isset($this->_view->content)) {
            $this->_view->content = parent::render($tpl, $tpl_vars);
        }

        return $this->_view->render($this->_layout.'.phtml');
    }
}
