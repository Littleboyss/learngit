<?php
use \Libs\Validate;

class RoleModel extends BaseModel
{
    protected $table = 'role';

    protected $dateFormat = 'U';

    public $add_rule = [
        'name|角色名' => 'require|chs|min:2|max:20',
        'desc|描述'  => 'max:255',
    ];

    public $edit_rule = [
        'id'       => 'require|number',
        'name|角色名' => 'chs|min:2|max:20',
        'desc|描述'  => 'max:255',
    ];

    // 添加验证
    public function addCheck($data)
    {
        $validate = Validate::make($this->add_rule);
        if (!$validate->check($data)) {
            return $validate->getError();
        } else {
            return false;
        }
    }

    // 修改验证
    public function editCheck($data)
    {
        $validate = Validate::make($this->edit_rule);
        if (!$validate->check($data)) {
            return $validate->getError();
        } else {
            return false;
        }
    }

    /**
     *
     * 添加
     */
    public function addRole($role_data)
    {
        foreach ($role_data as $key => $value) {
            $this->$key = $value;
        }
        $res = $this->save($role_data);
        if ($res) {
            return $this->id;
        };
        return $res;
    }

    /**
     *
     * 更新
     */
    public function updateRole($role_data, $id)
    {
        return $this->where($this->primaryKey, $id)->update($role_data);
    }

}
