<?php

/**
 * 不需要登录访问的地址
 * @author 周应华 
 */

class PublicAction extends Action
{    
    /**
     * 登录 
     */
    public function login()
    {
        $username = $this->_get('u');
        $nickname = $this->_get('n');
        $remark   = $this->_get('r');
        $time     = $this->_get('t');
        $skey     = $this->_get('skey');

        if (time() - $time > 30) {
            $this->error('登录超时，请重试');
        }

        if (md5($username . $nickname . $remark . $time . C('ADMIN_SSO_KEY')) != $skey) {
            $this->error('签名验证失败');
        }
        
        // 是否开通后台权限
        $adminModel = D('AdminAdmin');
        $admin = $adminModel->where(array('username' => $username))->find();
        if (!$admin) {
            $this->error('你没有开通' . C('SITE_NAME') . '后台权限');
        }
        
        // 登录的模块
        $rights = explode(',', $admin['rights']);
        if ($rights) {
            if ($admin['loginmodule'] && (in_array('Admin', $rights) || in_array($admin['loginmodule'], $rights))) {
                $module = $admin['loginmodule'];
            } else {
                $module = $rights[0];
            }
        } else {
            $this->error('你没有任何权限');
        }
        session('module', $module);
        
        // 更新用户信息
        $data = array();
        $data['id'] = $admin['id'];
        $data['nickname'] = $nickname;
        $data['remark'] = $remark;
        $data['loginmodule'] = $module;
        $data['logintime'] = time();
        $data['loginip'] = get_client_ip();
        $data['logintimes'] = $admin['logintimes'] + 1;
        $adminModel->save($data);
        
        session('admin', array(
            'username' => $username,
            'nickname' => $nickname,
            'rights' => $rights,
            'icon' => $admin['icon'],
        ));
        
        $this->redirect('Index/index');
    }
    
    /**
     * 登出 
     */
    public function logout()
    {
        session(null);
        $this->redirect('Index/index');
    }
    //清楚缓存
    public function clearcacheall(){
        // $cache_names = C('CACHE_DATA');
        // foreach ($cache_names as $key => $value) {
        //     // $this->clearcache($value);
        //     S($value,null);
        // }

        $path = APP_PATH . 'Runtime/Temp/';
        
        $dh = opendir($path); 
        while ($file = readdir($dh)) { 
            if($file != "." && $file != "..") { 
                $fullpath = $path."/".$file;
                echo $fullpath . '<br />';
                unlink($fullpath); 
            } 
        } 
        closedir($dh); 
    }

    public function test(){
        
    }
    // 获取一天前最高积分的阵容
    public function get_top_score(){
        header('Content-type:text/json');  
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
        $map['project_id'] = $_REQUEST['project_id'];
        $content = file_get_contents('./lineup_top'.$map['project_id'].'.txt');
        if ($content) {
            //$player = json_decode($content,true);// 转化为数组
            //echo json_encode($player);exit;
            echo $content;exit;
        }else{
            echo '{"error":1,"msg":"\u6570\u636e\u4e0d\u5b58\u5728","data":[],"extra_data":[]}';exit;
        }
    }
}