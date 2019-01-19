<?php
/**
 * 房间相关的接口
 * @author wangh 2017.2.17
 */
class RoomAction extends LoginAction{
	//房间类型
	public function roomtype(){
		$cache_name = 'room_type_data';
		$MatchRoomType = M('MatchRoomType');
		$data = $this->cache('get',$cache_name);
		if(!$data){
			$data = $MatchRoomType->order("sort asc")->select();
			$this->cache('set',$cache_name,$data,3600*24);
		}
		$this->returnMsg(0,'room',$data);
	}


	/**
	* 获取房间的列表
	* @param
	*/
	public function roomlist(){
		$page = $this->_data['page'] ? $this->_data['page'] : 1;
		$limit = 10; // 每次查询数目,默认10条
		$start = ($page - 1) * $limit;
		$MatchRoom = M('MatchRoom');
		$Map['status'] = 1; //发布中
		$Map['settlement_status'] = 1; //未结算


		$type_id = $this->_data['type_id']; // 房间类型
		if(is_numeric($type_id) && $type_id != 0){
			$Map['type_id'] = $type_id;
		}

		$project_id = $this->_data['project_id']; // 项目id
		if(is_numeric($project_id)){
			$Map['project_id'] = $project_id;
		}

		$price = $this->_data['price']; // 门票价格
		if($price){
			$price_where = explode('|', $price); // 分割条件
			if($price_where[0] == 0 && $price_where[1] == 0){
				$Map['price']  = array('eq',0);
			}elseif($price_where[1] == ''){
				$Map['price']  = array('EGT',intval($price_where[0]));
			}else{
				$Map['price']  = array(array('EGT',intval($price_where[0])),array('elt',intval($price_where[1])),'and');
			}
		}
		
		// 按照时间进行塞选房间列表
		// $now_time = strtotime(date('Y-m-d H:00:00')); //现在整时的时间搓
		// $Map['match_start_time'] = array('gt',$now_time); //只显示目前整点后房间列表

		//当天以后的比赛
		$tomorrow = strtotime(date('Y-m-d 00:00:00')) + 3600*24;
		$Map['match_start_time'] = array('gt',$tomorrow);

		$data = $MatchRoom->where($Map)->limit($start,$limit)->order('match_start_time asc')->select();
		$room_type_data = $this->getdata('room_type_name_all',86400); //获取房间类型,添加缓存
		// print_r($room_type_data);
		$project_data = $this->getdata('project_name_all',86400);
		if($room_type_data == false || $project_data == false){
			$this->returnMsg(1,'system');
		}
		
		foreach ($data as $key => $value) {
	        if($value['tag_img'] == 0){
	            $data[$key]['type_img'] = $room_type_data[$value['type_id']]['tag_img']; // 房间图标
	        }else{
	            $data[$key]['type_img'] = C('ROOM_TAG_IMG_URL').C('ROOM_TAG')[$value['tag_img']]['tag_img']; // 房间图标
	        }
			$data[$key]['type_name'] = $room_type_data[$value['type_id']]['name']; //房间名称
			
			$data[$key]['project_name'] = $project_data[$value['project_id']]['name'];//项目图标
			$data[$key]['open_tag'] = sprintf(C('ROOM_OPEN_RULE')[$value['open_id']],$value['open_num']);

			if($value['reward_id'] == 1 || $value['reward_id'] == 2 || $value['reward_id'] == 12){
				$str = sprintf(C('REWARD_RULE_TAG')[$value['reward_id']],$value['prize_num']);
				$data[$key]['reward_tag'] = str_replace('|', '%', $str);
			}else{
				$data[$key]['reward_tag'] = C('REWARD_RULE_TAG')[$value['reward_id']];
			}
			$data[$key]['prize_name'] = C('PRIZE_TYPE')[$value['prize_type']];
			$data[$key]['match_start_date'] = $this->starttime($value['match_start_time']);
			$data[$key]['join_num'] = $this->joinroomnum($this->_user['id'],$value['id']);
		}

		$this->returnMsg(0,'room',$data);
	}
	/**
	* 获取房间的详情
	* @param $id 房间的id
	*/
	public function roomdetail(){
		$id = $this->_data['id']; // 房间的id号
		if(!is_numeric($id)){
			$this->returnMsg(1);
		}
		/*2017.3.2start*/
		$data = $this->getroomdetail($id,$type = 'all',1);
		$data['join_num'] = $this->joinroomnum($this->_user['id'],$id);
		//奖金分配规则,数组形式返回
		// $data['reward_rule'] = array();
		$rewards = $this->getdata('reward_rule_data');
		// print_r($rewards);die;

		if($data['reward_id'] == 1){

			$prize_num = ceil($data['now_guess_num']*$data['prize_num']/100);
	
			if($prize_num > 1){
				$data['reward_rule'] = array('1-'.$prize_num => $data['reward_num']);
			}elseif($prize_num == 1){
				$data['reward_rule'] = array('1' => $data['reward_num']);
			}else{
				$data['reward_rule'] = array();
			}
		}elseif($data['reward_id'] == 2){
			$prize_num = ceil($data['now_guess_num']*$data['prize_num']/100); //中奖数量
			if($prize_num > 1){
				$data['reward_rule'] = array('1-'.$prize_num => ceil($data['reward_num']/$prize_num));
			}elseif($prize_num == 1){
				$data['reward_rule'] = array('1' => $data['reward_num']);
			}else{
				$data['reward_rule'] = array();
			}
		}elseif($data['reward_id'] == 3){
			$data['reward_rule'] = array('积分高于主播' => '均分'.$data['reward_num']);
		}elseif($data['reward_id'] == 4){
			$data['reward_rule'] = unserialize($rewards[4]['data']);
		}elseif($data['reward_id'] == 5){
			$data['reward_rule'] = unserialize($rewards[5]['data']);
		}elseif($data['reward_id'] == 6){
			$data['reward_rule'] = unserialize($rewards[6]['data']);
		}elseif($data['reward_id'] == 7){
			$data['reward_rule'] = array('1' => 0);
		}elseif($data['reward_id'] == 8){
			$data['reward_rule'] = unserialize($rewards[8]['data']);
		}elseif($data['reward_id'] == 9){
			$data['reward_rule'] = unserialize($rewards[9]['data']);
		}elseif($data['reward_id'] == 10){
			$data['reward_rule'] = unserialize($rewards[10]['data']);
		}elseif($data['reward_id'] == 11){
			$data['reward_rule'] = unserialize($rewards[11]['data']);
		}elseif($data['reward_id'] == 12){
			
			$goods = M('ShopGoods')->where(array('id' => $data['prize_goods_id']))->find();

			if($data['prize_num'] == 1){
				$num_ = $data['prize_num'];
			}else{
				$num_ = '1-'.$data['prize_num'];
			}

			$data['reward_rule'] = array($num_ => $goods['name']);
		}

		$data['room_rule'] = C('ROOM_RULE')[$data['prize_type']];

		$this->returnMsg(0,'room',$data);
		/*2017.3.2 end*/
	}

	//获取该房间的所有球员信息,可以考虑加缓存
	public function roomplayer(){
        $id = $this->_data['id'];
        if(!is_numeric($id)){
            $this->returnMsg(1);
        }
        // 获取房间所有球员数据
        $data = $this->getroomplayer($id);
        // 根据房间id判断所属项目
        $project_id = M('MatchRoom')->where('id ='.$id)->getField('project_id');
        if ($project_id == 4) {
        	$PlayerMatchData = M('PlayerMatchData');
        }elseif($project_id == 5){
        	$PlayerMatchData = M('PlayerMatchDataLol');
        }elseif($project_id == 6){
        	$PlayerMatchData = M('PlayerMatchDataDota2');
        }
        foreach ($data as $key => $value) {
        	//获取球员最后11场比赛的积分
        	$score = $PlayerMatchData->field('match_id,score,is_join')->where(array('player_id' => $value['id']))->limit(11)->order('id desc')->select();
        	$last_ten_score = array();
        	foreach ($score as $kk => $vv) {
        		if($vv['match_id'] == $data[$key]['match_id']){ //查询的数据与当前的比赛是同一个比赛 则跳过
        			if($vv['is_join'] == 1){
        				$data[$key]['state'] = 1; //球员是否锁定1是2否
        			}else{
        				$data[$key]['state'] = 2;
        			}
        			continue;
        		}
        		$data[$key]['state'] = 2;
        		$last_ten_score[] = $vv['score']/10;
        	}
        	$last_ten_score = count($last_ten_score) > 10 ? array_pop($last_ten_score) : $last_ten_score; //保留10条数据
        	// $data[$key]['last_ten_score'] = $last_ten_score;

        	//2017.3.21 生成随机的比分,数据多后,直接去除上面的注释 start
        	for ($i=0; $i < 10; $i++) { 
        		$sss[] = rand(200,555)/10;
        	}
        	$data[$key]['last_ten_score'] = $sss;
        	unset($sss);
        	//end


        }
        if($data === false){
        	$this->returnMsg(1,'room');
        }
		$this->returnMsg(0,'room',$data);
	}

    //匹配其他竞猜
    public function otherbet(){
        $id = $this->_data['id'];
        if(!is_numeric($id)){
        	$this->returnMsg(1); //参数错误
        }
        $RoomAll = D('RoomAll');
        $room_data = $RoomAll->where(array('id' => $id))->find();
        if(!$room_data){
        	$this->returnMsg(1); //参数错误
        }
		$Map['status'] = 1; //发布中
		$Map['settlement_status'] = 1; //未结算
		$Map['match_team'] = $room_data['match_team'];
		$Map['lineup_id'] = $room_data['lineup_id'];
        $data = $RoomAll->where($Map)->select();
		$room_type_data = $this->getdata('room_type_name_all',86400); //获取房间类型,添加缓存
		// print_r($room_type_data);
		$project_data = $this->getdata('project_name_all',86400);
		if($room_type_data == false || $project_data == false){
			$this->returnMsg(1,'system');
		}		
		foreach ($data as $key => $value) {
	        if($value['tag_img'] == 0){
	            $data[$key]['type_img'] = $room_type_data[$value['type_id']]['tag_img']; // 房间图标
	        }else{
	            $data[$key]['type_img'] = C('ROOM_TAG_IMG_URL').C('ROOM_TAG')[$value['tag_img']]['tag_img']; // 房间图标
	        }
			$data[$key]['type_name'] = $room_type_data[$value['type_id']]['name']; //房间名称
			// $data[$key]['type_img'] = $room_type_data[$value['type_id']]['tag_img']; // 房间图标
			$data[$key]['project_name'] = $project_data[$value['project_id']]['name'];//项目图标
			$data[$key]['open_tag'] = sprintf(C('ROOM_OPEN_RULE')[$value['open_id']],$value['open_num']);

			if($value['reward_id'] == 1 || $value['reward_id'] == 2){
				$str = sprintf(C('REWARD_RULE_TAG')[$value['reward_id']],$value['prize_num']);
				$data[$key]['reward_tag'] = str_replace('|', '%', $str);
			}else{
				$data[$key]['reward_tag'] = C('REWARD_RULE_TAG')[$value['reward_id']];
			}
			$data[$key]['prize_name'] = C('PRIZE_TYPE')[$value['prize_type']];
			$data[$key]['match_start_date'] = $this->starttime($value['match_start_time']);

			$joinroomnum = $this->joinroomnum($this->_user['id'],$value['id']);//参加房间的次数
			$data[$key]['join_num'] = $joinroomnum;
			//去除用户已经投注满了的
			if($joinroomnum == $value['allow_uguess_num']){
				unset($data[$key]);
			}

		}
		$this->returnMsg(0,'room',array_values($data));

    }


}