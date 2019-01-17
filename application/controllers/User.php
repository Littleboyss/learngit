<?php

class UserController extends ApiController
{
    public function addAction()
    {
        $user     = new UserModel();
        $add_data = $this->getPostJson();
        $res      = $user->addCheck($add_data);
        if ($res) {
            $this->error($res);
        }
        $user_id = $user->addUser($add_data);
        if ($user_id) {
            $add_data['id'] = $user_id;
            $this->success('添加成功!', $add_data);
        } else {
            $this->error('添加失败!');
        }
    }

    public function indexAction()
    {
        $page     = $this->_request->getQuery('page', 1);
        $limit    = $this->_request->getQuery('limit', 10);
        $get_data = $this->_request->getQuery();
        unset($get_data['page']);
        unset($get_data['limit']);
        $user = new UserModel;
        $data = $user::where($get_data)
            ->paginate($limit, ['id', 'username', 'phone', 'email', 'created_at'], 'page', $page)
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
        $user     = new UserModel;
        if (!$get_data) {
            $this->error('参数缺失!');
        }
        $data = $user::where($get_data)
            ->select('id', 'username', 'phone', 'email', 'created_at')
            ->first()
            ->toArray();
        if ($data) {
            $this->success('查询成功!', $data);
        } else {
            $this->error('查询失败!');
        }
    }

    public function updataAction()
    {
        $user        = new UserModel;
        $updata_data = $this->getPostJson();
        $res         = $user->editCheck($updata_data);
        if ($res) {
            $this->error($res);
        }
        $id = $updata_data['id'];
        unset($updata_data['id']);
        // 判断用户是否存在
        if (isset($id)) {
            $user_data = $user::find($id);
            if (!$user_data) {
                $this->error('该用户不存在!');
            }
        } else {
            $this->error('参数缺失!');
        }
        $result = $user->updateUser($updata_data, $id);
        if ($result) {
            $this->success('更新成功!');
        } else {
            $this->error('更新失败!');
        }
    }

    //删除
    public function delAction()
    {
        $user = new UserModel;
        $data = $this->getPostJson();
        // 判断用户是否存在
        if (isset($data['id'])) {
            $id        = $data['id'];
            $user_data = $user::find($id);
            if (!$user_data) {
                $this->error('该用户不存在!');
            }
        } else {
            $this->error('参数缺失!');
        }
        $result = $user_data->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }
}
