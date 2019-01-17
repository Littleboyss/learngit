<?php
use \Libs\Validate;

class organizeModel extends BaseModel
{
    protected $table = 'organize';

    protected $dateFormat = 'U';

    public static $treeArray = [];

    public $add_rule = [
        'pid|父级id'         => 'require|number',
        'type|组织机构类型'      => 'require|in:,1,4',
        'name|组织机构名'       => 'require|chs|min:2|max:20',
        'desc|描述'          => 'max:255',
        'all_pid|整个上级id'   => 'max:255',
        'all_pname|整个上级名称' => 'max:255',
    ];

    public $edit_rule = [
        'id'               => 'require|number',
        'pid|父级id'         => 'require|number',
        'type|组织机构类型'      => 'require|in:,1,4',
        'name|组织机构名'       => 'require|chs|min:2|max:20',
        'desc|描述'          => 'max:255',
        'all_pid|整个上级id'   => 'max:255',
        'all_pname|整个上级名称' => 'max:255',
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
    public function addOrganize($organize_data)
    {
        foreach ($organize_data as $key => $value) {
            $this->$key = $value;
        }

        $res = $this->save($organize_data);
        if ($res) {
            return $this->id;
        };
        return $res;
    }

    /**
     *
     * 更新
     */
    public function updateOrganize($organize_data, $id)
    {
        return $this->where($this->primaryKey, $id)->update($organize_data);
    }

    public function getTree($array, $pid = 0, $level = 0)
    {
        foreach ($array as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['level']        = $level;
                self::$treeArray[] = $v;
                unset($array[$k]);
                self::getTree($array, $v['id'], $level + 1);
            }
        }
        return self::$treeArray;
    }
}
