<?php

class OrganizeController extends ApiController
{
    public function addAction()
    {
        $Organize     = new OrganizeModel();
        $add_data = $this->getPostJson();
        $res      = $Organize->addCheck($add_data);
        if ($res) {
            $this->error($res);
        }
        // 添加的组织pid不为零时
        // 把父级的all_pid以及all_pname用逗号连接上
        if ($add_data['pid'] !== 0) {
            $p_data = $Organize::find($add_data['pid'])->toArray();
            if ($p_data) {
                $this->error('未找到父级!');
            } else {
                $add_data['all_pid']   = $p_data['all_pid'].','.$p_data['id'];
                $add_data['all_pname'] = $p_data['all_pname'].','.$p_data['name'];
            }
        }
        $Organize_id = $Organize->addOrganize($add_data);
        if ($Organize_id) {
            $add_data['id'] = $Organize_id;
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
        $Organize = new OrganizeModel;
        $data = $Organize::where($get_data)
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
        $Organize     = new OrganizeModel;
        if (!$get_data) {
            $this->error('参数缺失!');
        }
        $data = $Organize::where($get_data)
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
        $Organize        = new OrganizeModel;
        $update_data = $this->getPostJson();
        $res         = $Organize->editCheck($update_data);
        if ($res) {
            $this->error($res);
        }
        $id = $update_data['id'];
        unset($update_data['id']);
        // 判断组织机构是否存在
        if (isset($id)) {
            $Organize_data = $Organize::find($id);
            if (!$Organize_data) {
                $this->error('该组织机构不存在!');
            }
        } else {
            $this->error('参数缺失!');
        }
        // 添加的组织pid不为零时
        // 把父级的all_pid以及all_pname用逗号连接上
        if ($add_data['pid'] !== 0) {
            $p_data = $Organize::find($add_data['pid'])->toArray();
            if ($p_data) {
                $this->error('未找到父级!');
            } else {
                $add_data['all_pid']   = $p_data['all_pid'].','.$p_data['id'];
                $add_data['all_pname'] = $p_data['all_pname'].','.$p_data['name'];
            }
        }
        $result = $Organize->updateOrganize($update_data, $id);
        if ($result) {
            $this->success('更新成功!');
        } else {
            $this->error('更新失败!');
        }
    }

    //删除
    public function delAction()
    {
        $Organize = new OrganizeModel;
        $data = $this->getPostJson();
        // 判断组织机构是否存在
        if (isset($data['id'])) {
            $Organize_data = $Organize::find($data['id']);
            if (!$Organize_data) {
                $this->error('该组织机构不存在!');
            }
        } else {
            $this->error('参数缺失!');
        }
        $result = $Organize_data->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
