<?php

/**
 * 系统相关操作
 * @author wangh 
 */

class SystemAction extends AdminAction{

    //清除缓存
    public function cacheindex(){
        if(IS_POST){
            // print_r($this->_post());die;
            $data = $this->_post();
            foreach ($data['cache'] as $key => $value) {
                file_get_contents($value);
            }
            $this->success('清除成功',U('cacheindex'));
        }else{
            $this->display();
        }
        
    }
}
