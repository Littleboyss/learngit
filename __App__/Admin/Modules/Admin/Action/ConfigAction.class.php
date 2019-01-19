<?php

/**
 * 系统配置控制器,只允许指定的管理员使用
 * @author wangh 
 */

class ConfigAction extends AdminAction{
    /*
    * 竞猜开奖规则配置列表
    */
    public function betconfindex(){
        $this->display();
    }

    /*
    * 竞猜开奖规则配置添加
    */
    public function betconfadd(){
        $this->display();
    }

    /*
    * 竞猜开奖规则配置添加
    */
    public function betconfedit(){
        $this->display();
    }
}
