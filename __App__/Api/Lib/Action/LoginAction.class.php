<?php

/**
 * 需要登录的模块继承此类
 */
class LoginAction extends CommonAction{

	public function __construct(){
		parent::__construct();
		$user_data = $this->checklogin(); // 检测用户的登录状态
		if(!$user_data){
			$this->returnMsg(1,'user');
		}
	}
	/**
	* 查询用户参与该房间的次数
	* @param $uid 用户的id
	* @param room_id
	* @return 用户参与房间投注的次数
	* @author 2017.3.3
	*/
	protected function joinroomnum($uid,$room_id){
		$Map['uid'] = $uid;
		$Map['room_id'] = $room_id;
		$guess_num = M('UserGuessRecord')->where($Map)->sum('guess_num');
		return $guess_num ? $guess_num : 0;
	}

}