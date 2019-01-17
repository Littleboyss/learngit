<?php

class RoleController extends ApiController
{
    public function addAction()
    {
        $Role     = new RoleModel();
        $add_data = $this->getPostJson();
        $res      = $Role->addCheck($add_data);
        if ($res) {
            $this->error($res);
        }
        $Role_id = $Role->addRole($add_data);
        if ($Role_id) {
            $add_data['id'] = $Role_id;
            $this->success('添加成功!', $add_data);
        } else {
            $this->error('添加失败!');
        }
    }

    public function indexAction()
    {
        $page     = $this->_request->getQuery('page', 1);
        $limit   = $this->_request->getQuery('limit', 10);
        $get_data = $this->_request->getQuery();
        unset($get_data['page']);
        unset($get_data['limit']);
        $Role = new RoleModel;
        $data = $Role::where($get_data)
            ->paginate($limit, ['*'], 'page', $page)
            ->toArray();
        if ($data) {
            $this->success('查询成功!', $data);
        } else {
            $this->error('查询失败!');
        }
    }

    public function detailAction()
    {
        $get_data = $this->_request->getQuery();
        $Role     = new RoleModel;
        if (!$get_data) {
            $this->error('参数缺失!');
        }
        $data = $Role::where($get_data)
            ->select('id', 'name', 'desc', 'created_at')
            ->first()
            ->toArray();
        if ($data) {
            $this->success('查询成功!', $data);
        } else {
            $this->error('查询失败!');
        }
    }

    public function updateAction()
    {
        $Role        = new RoleModel;
        $updata_data = $this->getPostJson();
        $res         = $Role->editCheck($updata_data);
        if ($res) {
            $this->error($res);
        }
        $id = $updata_data['id'];
        unset($updata_data['id']);
        // 判断角色是否存在
        if (isset($id)) {
            $Role_data = $Role::find($id);
            if (!$Role_data) {
                $this->error('该角色不存在!');
            }
        } else {
            $this->error('参数缺失!');
        }
        $result = $Role->updateRole($updata_data, $id);
        if ($result) {
            $this->success('更新成功!');
        } else {
            $this->error('更新失败!');
        }
    }

    //删除
    public function delAction()
    {
        $Role = new RoleModel;
        $data = $this->getPostJson();
        // 判断角色是否存在
        if (isset($data['id'])) {
            $Role_data = $Role::find($data['id']);
            if (!$Role_data) {
                $this->error('该角色不存在!');
            }
        } else {
            $this->error('参数缺失!');
        }
        $result = $Role_data->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
