<?php
ini_set('max_execution_time', '0');
set_time_limit(0);
/**
 * 短信接口
 * @author chengy 2017.04.10
 */
class TestAction extends Action{
    public function test_wx(){
        $Model = A('Wx');
        $res = $Model->sendmessage(6,153);
        var_dump($res);
    }

}