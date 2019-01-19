<?php

class EmptyAction extends Action{
    //404
    public function _empty(){
        exit(json_encode(array('error' => 404,'msg' => '404,你查询的页面不存在')));
    }

    //404
    // public function _empty(){
    //     exit(array('error' => 404,'msg' => '404,你查询的页面不存在'));
    // }

}
