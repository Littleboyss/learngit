<?php
class TestController extends CliController
{
    public function runAction()
    {
        echo 'log start';
        $this->_logHandler->log('test!!!!!!');
        echo 'log end';
    }
}
