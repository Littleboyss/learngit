<?php
use \Libs\Validate;

class UserModel extends BaseModel
{
    protected $table = 'user';

    public $timestamps = false;

    public $add_rule = [
        'username|用户名' => 'require|chs|min:2|max:20',
        'phone|手机号'    => 'require|length:11|number',
        'email|邮箱'     => 'require|email|max:255',
        'password|密码'  => 'require|min:6',
    ];

    public $edit_rule = [
        'id'           => 'require|number',
        'username|用户名' => 'chs|min:2|max:20',
        'phone|手机号'    => 'length:11|number',
        'email|邮箱'     => 'email|max:255',
        'password|密码'  => 'min:6',
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
     * 添加用户
     */
    public function addUser($user_data)
    {
        $user_data['password']   = $this->doHash(md5($user_data['password']));
        $user_data['created_at'] = time();
        return $this->insertGetId($user_data);
    }

    /**
     *
     * 用户更新
     */
    public function updateUser($user_data, $id)
    {
        $user_data['password']   = $this->doHash(md5($user_data['password']));
        $user_data['updated_at'] = time();
        return $this->where($this->primaryKey, $id)->update($user_data);
    }
    /**
     *
     * 用户密码进行加密
     */
    public function doHash($user_passwd)
    {
        $salt = 'dfaAnfjd.Xia41j.';
        return md5($salt . $user_passwd . $salt);
    }
}
