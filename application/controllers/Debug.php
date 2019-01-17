<?php
class DebugController extends Controller
{
    public function infoAction()
    {
        \Yaf\Dispatcher::getInstance()->disableView();
        $openHandler = new \DebugBar\OpenHandler($this->_debugbar);
        $result = $openHandler->handle(null, false);
        if ($result == 'null') {
            return $this->error('获取调试信息失败');
        }

        return $this->_response->setBody($result);
    }
}