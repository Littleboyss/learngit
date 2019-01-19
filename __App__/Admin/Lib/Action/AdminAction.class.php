<?php

/**
 * 后台控制器基类，后台控制器类请继承该类
 * @author 周应华 
 */
class AdminAction extends Action
{
    public function __construct()
    {
        parent::__construct();

        // 没有登录，跳转到登录页面
        if (!session('?admin')) {
            $loginUrl = C('CMS_URL') . '/index.php?m=Public&a=login&callback_url='
                    . urlencode('http://' . $_SERVER['HTTP_HOST'] . U('Public/login'));
            redirect($loginUrl);
        }
        
        // 验证权限
        $admin = session('admin');
        if (!(GROUP_NAME == 'Admin' && MODULE_NAME == 'Index')) {
            if (!in_array('Admin', $admin['rights'])) {
                if (!in_array(GROUP_NAME, $admin['rights'])) {
                    $this->error('你没有权限访问此页面');
                }
            }
        }
    }
	public function phpUnescape($escstr)
	{
		preg_match_all("/%u[0-9A-Za-z]{4}|%.{2}|[0-9a-zA-Z.+-_]+/", $escstr, $matches); //prt($matches);
		$ar = &$matches[0];
		$c = "";
		foreach ($ar as $val) {
			if (substr($val, 0, 1) != "%") { //如果是字母数字+-_.的ascii码
				$c .= $val;
			} elseif (substr($val, 1, 1) != "u") { //如果是非字母数字+-_.的ascii码
				$x = hexdec(substr($val, 1, 2));
				$c .= chr($x);
			} else { //如果是大于0xFF的码
				$val = intval(substr($val, 2), 16);
				if ($val < 0x7F) { // 0000-007F
					$c .= chr($val);
				} elseif ($val < 0x800) { // 0080-0800
					$c .= chr(0xC0 | ($val / 64));
					$c .= chr(0x80 | ($val % 64));
				} else { // 0800-FFFF
					$c .= chr(0xE0 | (($val / 64) / 64));
					$c .= chr(0x80 | (($val / 64) % 64));
					$c .= chr(0x80 | ($val % 64));
				}
			}
		}
		return $c;
	}
	/**
	* 设置和获取数据的缓存信息
	* 此处采用的是文件缓存方式
	* project_data:缓存所有项目数据名 playercountry_data:国籍缓存 team_data:队伍缓存数据
	* @param $name 数据的名称,用户区分是哪种类型的缓存
	* @param $time 缓存的时间 默认600秒
	* @return 返回指定数据的
	* @author wangh 2017.2.20
	*/
	protected function getcache($name,$time = 6000){
		if (!in_array($name, C('CACHE_DATA'))){
			return false;
		}
		$data = S($name);
		if(!$data){
			if($name == 'project_data'){ 
				$data = M('MatchProject')->select();
			}

			if($name == 'playercountry_data'){
				$data = M('PlayerCountry')->getField('id,country');
			}

			if($name == 'team_data'){
				$data = M('MatchTeam')->getField('id,name,project_id');
			}

			if($name == 'room_type'){
				$data = M('MatchRoomType')->getField('id,name');
			}
			if($name == 'match_type_data'){
				$data = M('MatchType')->getField('id,name,introduce');
			}
			S($name,$data,$time); //添加缓存
		}
		return $data;
	}
	//清除缓存
	protected function clearcache($name){
		S($name,null);
	}

	protected function getproject($is_get = false){
		//获取项目所有的项目
		$project = $this->getcache('project_data');
		if($is_get === true){//转换为二维可直接获取的数据
			$s = array(); //用来存储数组
			foreach ($project as $key => $value) {
				$s[$value['id']] = $value['name'];
			}
			$project = $s;
		}
		$this->assign('project',$project);
	}
	
	protected function getteam($project = false){
		$team = $this->getcache('team_data');
		if($project !== false){
			$_data = array();
			foreach ($team as $key => $value) {
				if(in_array($value['project_id'], $project)){
					$_data[$key] = $value;
				}
			}
			$team = $_data;
		}
		$this->assign('team',$team);
	}
	// 帐目变化入user_account表
	// $class_id     int     帐变类型
	// $type         tinyint 1为门票，2为砖石，3为木头
	// $uid          int     用户编号
	// $nums         int     数量
	// $is_back_nums bool    是否为账目增加默认为false
	protected function insert_account($class_id,$type,$uid,$nums,$is_back_nums=false,$room_id=0){
		if ($is_back_nums) {
			$account['back_nums']=$nums;
		}else{
			$account['go_nums']=$nums;
		}
		$account['class_id']=$class_id;
		$account['type']=$type;
		$account['user_id']=$uid;
		$account['addtime']=time();
		if($room_id != 0){
			$account['addtime'] = $room_id;
		}
		$res = M('UserAccount')->add($account);
		return $res;
	}


	protected function returnMsg($error,$msg,$data){
		$info['error'] = $error;
		$info['msg'] = $msg;
		$info['data'] = $data;
		exit(json_encode($info));
	}

}