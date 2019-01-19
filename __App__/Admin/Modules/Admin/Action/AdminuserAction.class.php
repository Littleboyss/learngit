<?php

/**
 * 管理员管理
 * @author 周应华 
 */

class AdminuserAction extends AdminAction
{
    /**
     * 列表 
     */
    public function index()
    {
        $adminModel = D('AdminAdmin');
        $admin = $adminModel->order('id')->select();
        foreach ($admin as $k => $v) {
            $rights = array();
            $modules = C('MODULES');
            foreach (explode(',', $v['rights']) as $r) {
                if (isset($modules[$r])) {
                    $rights[] = $modules[$r];
                }
            }
            $admin[$k]['rights'] = implode('，', $rights);
        }
        $this->assign('admin', $admin);
        $this->display();
    }
    
    /**
     * 添加管理员
     */
    public function add()
    {
        if ($this->isPost()) {
            $adminModel = D('AdminAdmin');
            $data = array(
                'username' => $this->_post('username'),
                'addtime' => time(),
                'issuper' => $this->_post('issuper'),
                'rights' => (string) implode(',', $this->_post('rights'))
            );
            if ($adminModel->add($data)) {
                $this->success('成功', U('Adminuser/index'));
            } else {
                $this->error('失败' . $adminModel->getDbError());
            }
        } else {
            $this->display();
        }
    }
    
    /**
     * 编辑管理员 
     */
    public function edit()
    {
        $adminModel = D('AdminAdmin');
        if ($this->isPost()) {
            $id = (int) $this->_post('id');
            $data = array(
                'username' => $this->_post('username'),
                'rights' => (string) implode(',', $this->_post('rights'))
            );
            $adminModel->where(array('id' => $id))->save($data);
            close_dialog('edit');
        } else {
            $id = $this->_get('id');
            if ($id <= 0) {
                $this->error($this->sysMsg(13));
            }

            $admin = $adminModel->find($id);
            if (!$admin) {
                $this->error($this->sysMsg(19));
            }
            $admin['rights'] = explode(',', $admin['rights']);
            $this->assign('admin', $admin);
            $this->display();
        }
    }
    
    /**
     * 删除管理员 
     */
    public function del()
    {
        $id = (int) $this->_get('id');
        if ($id <= 0) {
            $this->error($this->sysMsg(13));
        }
        $adminModel = D('AdminAdmin');
        $username = $adminModel->where(array('id' => $id))->getField('username');
        if (in_array($username, C('SUPER_ADMINS'))) {
            $this->error($this->sysMsg(20));
        }
        if ($adminModel->where(array('id' => $id))->delete()) {
            $this->success($this->sysMsg(5));
        } else {
            $this->error($this->sysMsg(6));
        }
    }
}