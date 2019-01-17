<?php
/**
 * 默认控制器
 */

class IndexController extends LayoutController
{
    /**
     * 租赁首页
     */
    public function indexAction()
    {
        var_dump($this->_request->getPost());
    }
}
