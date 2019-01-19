<?php
/**
 * 公共常用接口
 * @author wangh 2017.2.17
 */
class UserAction extends LoginAction{
	
	// 微信扫码支付的微信信息
	protected $h5_wxappid = 'wxcb820196a31b4bf5';
	protected $h5_wxmchid = 1374587902;
	protected $h5_wxkey = '4598532bBs5510368sg54123rmnj18FA';
	// // protected $wxappsecret = '70f5c66c699843e521ce7f3b6f07d0fc';
	

	//微信app支付
	protected $wxappid = 'wxe43bb7bc7cb3367d';
	protected $wxmchid = 1302281301;
	protected $wxkey = 'WyO9IBhhJnHxzSMMPnBpX22zccvFFWZi';
	// protected $wxkey = 'c7e44ed43c2394173d072e2056f333bd';
	// protected $wxappsecret = '70f5c66c699843e521ce7f3b6f07d0fc';
	protected $wx_place_order = 'https://api.mch.weixin.qq.com/pay/unifiedorder'; //统一下单url

	//获取用户的首冲状态状态是否使用过
	public function first_charge(){
		$data = M('UserCharge')->where(array('uid' => $this->_user['id'],'status' => 1,'first_charage' => 1))->find();
		$_data = array();
		if($data){
			$_data['first_charge'] = 1;
		}else{
			$_data['first_charge'] = 2;
		}
		$this->returnMsg(0,'charge',$_data);
	}



	//设置引导图的状态
	public function set_guide(){
		$type = $this->_data['type'];
		if(!in_array($type, array(1,2,3,4,5))){ //1首页 //2赛况 3阵容 //4选择球员 //5竞猜
			$this->returnMsg(1); //type值不正确
		}
		$UserUser = M('UserUser');
		$data = $UserUser->where(array('id' => $this->_user['id']))->find();
		$_data = explode('|', $data['is_guide']);
		$_data[$type - 1] = 1;

		$res = $UserUser ->where(array('id' => $data['id']))->setField('is_guide',implode('|', $_data));
		// if($res){
		$f = explode('|', $data['is_guide']);
		
		$this->returnMsg(0,'system',array('index' => $f[0],'event' => $f[1],'lineup' => $f[2],'select_player' => $f[3],'guess' => $f[4]));
		// }else{
			// $this->returnMsg(1,'system');
		// }
	}

	/**
	 * 用户投注的比赛
	 * 未开始的数据列表
	 * @param
	 */
	public function userbetmatch(){
		$page = $this->_data['page'] ? $this->_data['page'] : 1;
		$limit = 10; // 每次查询数目,默认10条
		$start = ($page - 1) * $limit;
		$UserGuessRecord = M('UserGuessRecord');
		$status = 1; // 比赛的状态 1未开始 2比赛中 3已结束
		if(!in_array($status, array(1,2,3))){
			$this->returnMsg(1);
		}
		$uid = $this->_user['id']; //用户的id
		$Map['match_status'] = $status;
		$Map['uid'] = $uid;
		$data = $UserGuessRecord->field('id,room_id,guess_num,lineup_id,ranking,is_reward,lineup_score')->where($Map)->limit($start,$limit)->select();//默认获取10条
		$_data = array(); //存储房间数据
		$_lineup_data = array(); //存储阵容数据
		$lineups = array(); //存储所有的阵容的id
		$_tema_info = array(); //存储比赛中的数据信息
		//2017.8.15 start
		$lineup_player_info = array(); //存储阵容球员的选手信息
		$lineup_player_info_score = array(); //存储阵容球员的阵容积分

		$usable_salary_sum = array();
		// $_lineup_score = array();
		foreach ($data as $key => $value) {
			$lineups[] = $value['lineup_id'];
			//查询用户阵容
			$user_guess_info = M('Lineup')->where(array('id' => $value['lineup_id']))->find();
			$lineup = $_lineup_data[$value['lineup_id']] = unserialize($user_guess_info['lineup']); //阵容按阵容id保存

			$room_data = $this->getroomdetail($value['room_id']); //获取房间的信息

			//2017.8.15 start
			$all_players = $this->project_players($room_data['project_id']);
			$lineup_score = 0;
			$salary_sum = 0;
			foreach ($lineup as $kl => $vl) {
				$lineup_player_info[$value['lineup_id']][$kl] = $all_players[$vl];
				$lineup_score += $all_players[$vl]['average'];
				$salary_sum += $all_players[$vl]['salary'];
			}
			$lineup_player_info_score[$value['lineup_id']] =$lineup_score;
			$usable_salary_sum[$value['lineup_id']] = $room_data['lineup']['pay'] - $salary_sum;
			//2017.8.15 end
			unset($lineup_score);
			unset($salary_sum);
			$room_data['join_num'] = $this->joinroomnum($this->_user['id'],$value['room_id']); //用户参加该房间的次数
			$room_data['guess_id'] = $value['id'];
			$_data[$value['lineup_id']][] = $room_data; //房间信息按阵容id保存
		}
		$lineups = array_values(array_unique($lineups)); //去除重复值,重新建立索引
		$s = array();
		foreach ($lineups as $key => $value) {
			$s[$key]['lineup_id'] = $value;
			$s[$key]['lineup_score'] = $lineup_player_info_score[$value];
			$s[$key]['usable_salary_sum'] = $usable_salary_sum[$value];
			$s[$key]['lineup_info'] = $_lineup_data[$value];
			//2017.8.15 start
			$s[$key]['player_lineup_info'] = $lineup_player_info[$value];
			// $s[$key]['lineup_info'] = array_values($_lineup_data[$value]);
			$s[$key]['join_room_num'] = count($_data[$value]);
			$s[$key]['lineup'] = $_data[$value][0]['lineup'];
			$s[$key]['match_start_date'] = $_data[$value][0]['match_start_date'];
			// $s[$key]['type_id'] = $_data_[$value][0]['room_list'][0]['id'];
			if($status == 2){
				$s[$key]['team_info'] = $_tema_info[$value];
			}
			$s[$key]['room_info'] = $_data[$value];
			$s[$key]['project_id'] = $_data[$value][0]['project_id'];
		}
		// print_r($s);die;
		$this->returnMsg(0,'room',$s);
	}

	//比赛中的数据
	public function userbetmatching(){
		$page = $this->_data['page'] ? $this->_data['page'] : 1;
		$limit = 10; // 每次查询数目,默认10条
		$start = ($page - 1) * $limit;
		$UserGuessRecord = M('UserGuessRecord');
		$status = 2; // 比赛的状态 1未开始 2比赛中 3已结束
		$uid = $this->_user['id']; //用户的id
		$Map['match_status'] = $status;
		$Map['uid'] = $uid;
		$data = $UserGuessRecord->field('id,room_id,guess_num,lineup_id,ranking,is_reward,lineup_score')->where($Map)->limit($start,$limit)->select();//默认获取10条
		// print_r($data);
		//2017.8.15 start
		$lineup_player_info = array(); //存储阵容球员的选手信息

		$_data = array(); //存储房间数据
		$_lineup_data = array(); //存储阵容数据
		$lineups = array(); //存储所有的阵容的id
		$_tema_info = array(); //存储比赛中的数据信息
		// $_lineup_score = array();
		foreach ($data as $key => $value) {
			$lineups[] = $value['lineup_id'];
			//查询用户阵容
			$user_guess_info = M('Lineup')->where(array('id' => $value['lineup_id']))->find();
			$lineup = $_lineup_data[$value['lineup_id']] = unserialize($user_guess_info['lineup']); //阵容按阵容id保存
			// echo $value['room_id'];
			$room_data = $this->getroomdetail($value['room_id']); //获取房间的信息
			// print_r($room_data);die;

			//2017.8.15 start
			$all_players = $this->project_players($room_data['project_id']);
			foreach ($lineup as $kl => $vl) {
				$lineup_player_info[$value['lineup_id']][$kl] = $all_players[$vl];
			}

			//2017.8.15 end
			$room_data['join_num'] = $this->joinroomnum($this->_user['id'],$value['room_id']); //用户参加该房间的次数
			$room_data['guess_id'] = $value['id'];
			//进行中的比赛需要更新所选阵容得分的实时信
			$room_data['player_ranking'] = $value['ranking'];//比赛中的获取排名信息

			if($room_data['reward_id'] == 12){ //实物奖品显示实物的名称
				$room_data['is_reward'] = $room_data['goods_name'] ? $room_data['goods_name'] : '无';
			}else{
				$room_data['is_reward'] = $value['is_reward']; //可获得的奖励
			}
			

			$room_data['my_score'] = $value['lineup_score']/10; //我在此房间的阵容得分
			//比赛中的数据获取一个最高得分,获取一个有奖得分
			$max_socre = $UserGuessRecord->order('lineup_score desc')->field('lineup_score,ranking')->where(array('room_id' => $value['room_id']))->find();
			$room_data['max_socre'] = $max_socre['lineup_score']/10;
			$room_data['max_rank'] = 1;
			//获取有奖的积分
			$door_socre = $UserGuessRecord->field('lineup_score,ranking')->order('lineup_score asc')->where('room_id='.$value['room_id'].' and is_reward>0')->find();
			$room_data['door_socre'] = $door_socre['lineup_score']/10;
			$room_data['door_rank'] = $door_socre['ranking'] ? $door_socre['ranking'] : 1;
			$match_list = M('MatchRoomInfo')->where(array('room_id' => $value['room_id']))->getField('match_team');// 获取比赛队伍
			// echo $room_data['project_id'];

			$today_match_data = A('Public')->todaymatch($match_list,$room_data['project_id']); // 比赛数据
			// print_r($today_match_data);die;
			//var_dump($today_match_data);die;
			$linup_score = 0;
			foreach ($lineup as $keys => $values) { //获取所选队伍的实时得分信息
				if($today_match_data[$room_data['project_id']][$values] && $today_match_data[$room_data['project_id']][$values]['is_join'] == 1){ //检测当前这个球员的数据是否为真 和 是否已经上场 ,则锁定该球员
					$_tema_info[$value['lineup_id']][$values]['state'] = 1; //锁定该球员
				}else{
					$_tema_info[$value['lineup_id']][$values]['state'] = 2; //不锁定该球员
				}
				if ($room_data['project_id'] == 4) {
					// NBA项目
					$_tema_info[$value['lineup_id']][$values]['play_time'] = $today_match_data[$room_data['project_id']][$values]['play_time']; //球员剩余时间
					$_tema_info[$value['lineup_id']][$values]['score'] = $this->scorerule($values,$today_match_data[$room_data['project_id']][$values]);

				}elseif ($room_data['project_id'] == 5) {
					// lol项目
					$players = M('MatchPlayerWcg')->where('id ='.$values)->find();
					//var_dump($players);
					 if ($players['position'] == 6) {
					 	$_tema_info[$value['lineup_id']][$values]['result'] = $players['result'];
					 }else{
					 	// $death = $today_match_data[$room_data['project_id']][$values]['death'] == 0 ? 1 :$today_match_data[$room_data['project_id']][$values]['death'];
					 	//$_tema_info[$value['lineup_id']][$values]['KDA'] = number_format(($today_match_data[$room_data['project_id']][$values]['kill']+$today_match_data[$room_data['project_id']][$values]['assists'])/$death,1); //kda
					 }
					$_tema_info[$value['lineup_id']][$values]['score'] = number_format($this->scorerule_lol($values,$today_match_data[$room_data['project_id']][$values]),1);
				}elseif ($room_data['project_id'] == 6) {
					// dota2项目
					$players = M('MatchPlayerWcg')->where('id ='.$values)->find();
					//var_dump($players);
					if ($players['position'] == 6) {
					 	$_tema_info[$value['lineup_id']][$values]['result'] = $players['result'];
					}else{
					 	// $death = $today_match_data[$room_data['project_id']][$values]['death'] == 0 ? 1 :$today_match_data[$room_data['project_id']][$values]['death'];
					 	//$_tema_info[$value['lineup_id']][$values]['KDA'] = number_format(($today_match_data[$room_data['project_id']][$values]['kill']+$today_match_data[$room_data['project_id']][$values]['assists'])/$death,1); //kda
					}
					$_tema_info[$value['lineup_id']][$values]['score'] = number_format($this->scorerule_dota2($values,$today_match_data[$room_data['project_id']][$values]),1);
				}
				$linup_score += $_tema_info[$value['lineup_id']][$values]['score'];
			}
			$_linup_score[$value['lineup_id']] = $linup_score;
			// print_r($_tema_info);die;
			$_data[$value['lineup_id']][] = $room_data; //房间信息按阵容id保存
		}
		$lineups = array_values(array_unique($lineups)); //去除重复值,重新建立索引
		$s = array();
		foreach ($lineups as $key => $value) {
			$s[$key]['lineup_id'] = $value;// 阵容id

			//2017.8.15 start
			$s[$key]['player_lineup_info'] = $lineup_player_info[$value];	
					
			$s[$key]['lineup_score'] = $_linup_score[$value];// 阵容积分
			$s[$key]['project_id'] = $_data[$value][0]['project_id'];
			// $s[$key]['lineup_info'] = array_values($_lineup_data[$value]);// 阵容信息
			$s[$key]['lineup_info'] = $_lineup_data[$value];// 阵容信息
			$s[$key]['join_room_num'] = count($_data[$value]);// 参加数量
			$s[$key]['lineup'] = $_data[$value][0]['lineup'];// 阵容
			$s[$key]['match_start_date'] = $_data[$value][0]['match_start_date'];// 比赛开始时间
			$s[$key]['type_id'] = $_data[$value][0]['type_id'];// 阵容类型
			if($status == 2){
				$s[$key]['team_info'] = $_tema_info[$value];// 比赛中数据
			}
			$s[$key]['room_info'] = $_data[$value];// 房间信息
			// 判断是否全部上场
			foreach ($_tema_info[$value] as $values) {
				if ($values['state'] == 1) {
					$s[$key]['is_enter'] = 1;
				}else{
					$s[$key]['is_enter'] = 2;
					break;
				}
			}
			
		}
		// print_r($s);die;
		$this->returnMsg(0,'room',$s);
	}
	// 积分规则
    // $player_id 选手的id
    // $player_match_data选手比赛的得分数据
    // @return 返回积分
    protected function scorerule_dota2($player_id,$player_match_data){
        $score_sum = 0;
        $position = M('MatchPlayerWcg')->where(array('id'=>$player_id))->getField('position');
        //var_dump($position);exit;
        if ($position == 6) {
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
        }else{
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
        }
        foreach ($player_match_data as $key => $value) {
            $score_sum += $socre_rule[$key] * $value;
        }

        return number_format($score_sum,1);
    }

	//获取用户已结束的赛况列表
	public function finishbet(){
		$page = $this->_data['page'] ? $this->_data['page'] : 1;
		$limit = 10; // 每次查询数目,默认10条
		$start = ($page - 1) * $limit;
		$UserGuessRecord = M('UserGuessRecord');
		$uid = $this->_user['id']; //用户的id

		//查询用户的分表中奖信息
		$table_name = $this->get_hash_table('UserBetGess',$uid);
		$UserBetGess = M($table_name);

		$Map['match_status'] = 3;
		$Map['uid'] = $uid;
		$data = $UserGuessRecord->field('id,room_id,guess_num,lineup_id,lineup_score,ranking,is_reward,settlement_status')->order('id desc')->where($Map)->limit($start,$limit)->select();//默认获取10条
		foreach ($data as $key => $value) {

			$user_bet_gess_data = $UserBetGess->where(array('room_id' => $value['room_id'],'uid' => $uid,'ranking' => $value['ranking'],'lineup_score' => $value['lineup_score']))->find();

			$data[$key]['get_reward_id'] = $user_bet_gess_data ? $user_bet_gess_data['id'] : 0; //领奖的id
			$data[$key]['get_status'] = $user_bet_gess_data ? $user_bet_gess_data['status'] : 2; //领奖的状态,2 没有奖励 1 已经领取 0未领取

			$room_data = $this->getroomdetail($value['room_id']); //获取房间的信息

			if($value['settlement_status'] == 3){
				$data[$key]['is_reward'] = '流盘';
			}else{
				if($room_data['reward_id'] == 12){ //实物奖品显示实物的名称
					if($value['is_reward'] == 1){
						$data[$key]['is_reward'] = $room_data['goods_name'] ? $room_data['goods_name'] : '无';
					}else{
						$data[$key]['is_reward'] = '无';
					}	
				}				
			}

			$data[$key]['room_info'] = $room_data;
			$data[$key]['project_id'] = $room_data['project_id'];
			$data[$key]['room_info']['join_num'] = $this->joinroomnum($this->_user['id'],$value['room_id']);
			$data[$key]['lineup_score'] = $value['lineup_score']/10;
			$data[$key]['date'] = date('Y-m-d',$room_data['match_start_time']);
		}
		$this->returnMsg(0,'room',$data);
	}


	//获取用户投注详情
	public function userroomdetail(){
		$id = $this->_data['id']; // 投注id
		$room_id = $this->_data['room_id']; // 房间的id号
		if(!is_numeric($id)){
			$this->returnMsg(1);
		}
		$uid = $this->_user['id'];
		$Map['id'] = $id;
		$Map['room_id'] = $room_id;
		$Map['uid'] = $this->_user['id'];
		$UserGuessRecord = M('UserGuessRecord');
		$data = $UserGuessRecord->where($Map)->find();
		if(!$data){
			$this->returnMsg(1); //没有查询到用户对这个房间的投注,返回参数错误
		}
		//用户所选的阵容信息
		$Lineup = M('Lineup');
		$user_guess_info = $Lineup->where('id = '.$data['lineup_id'])->find();
		//获取该房间的所有信息
		$room_data = $this->getroomdetail($data['room_id'],'all');
		$room_data['join_num'] = $this->joinroomnum($this->_user['id'],$room_id); //用户参加房间的次数
		//获取所的球员
		if($room_data['project_id'] == 4){
			$players = $this->getdata('player_data_all');
		}elseif($room_data['project_id'] == 5 || $room_data['project_id'] == 6){
			$players = $this->getdata('player_data_lol');
		}

		$lineup_data = unserialize($user_guess_info['lineup']);

		// $all_players = $this->project_players($room_data['project_id']);
		// $lineup_player_info_l = array();
		//存储所选阵容的选手信息
		$lineup_player_info = array();
		$ii = 0;
		$lineup_average = 0; //阵容平均积分
		$usable_salary_sum = 0; //可用的工资
		foreach ($lineup_data as $key => $value) {

			// $lineup_player_info_l[$key] = $all_players[$value];

			foreach ($room_data['match_list'] as $k => $v) {		//该房间所有比赛信息
				if($players[$value]['team_id'] == $v['team_a'] || $players[$value]['team_id'] == $v['team_b']){
					$lineup_player_info[$ii]['match_id'] = $v['id']; //筛选该选手属于哪个比赛
				}
			}
			//计算积分和工资
			$lineup_average += $players[$value]['average'];;//阵容平均积分
			$usable_salary_sum += $players[$value]['salary']; //可用的工资

			$lineup_player_info[$ii]['salary'] = $players[$value]['salary'];
			$lineup_player_info[$ii]['team_id'] = $players[$value]['team_id'];
			$lineup_player_info[$ii]['name'] = $players[$value]['name'];
			$lineup_player_info[$ii]['img'] = $players[$value]['img'];
			$lineup_player_info[$ii]['player_id'] = $value;
			$lineup_player_info[$ii]['average'] = $players[$value]['average'];
			$lineup_player_info[$ii]['result'] = $players[$value]['result'];// 比赛结果
			if($room_data['project_id'] == 4){
				$lineup_player_info[$ii]['play_time'] = $players[$value]['play_time'];
			}elseif($room_data['project_id'] == 5 || $room_data['project_id'] == 6){
				$lineup_player_info[$ii]['KDA'] = $players[$value]['KDA'];
			}
			
			$lineup_player_info[$ii]['position'] = $key; //球员的位置信息
			$ii++;
		}
		unset($ii);
		$_data['usable_salary_sum'] = $room_data['lineup']['pay'] - $usable_salary_sum;
		$_data['lineup_average'] = $lineup_average;
		$_data['lineup_info'] = $lineup_data; //返回阵容的信息
		$_data['room_info'] = $room_data;
		// print_r($lineup_player_info);die;
		//当比赛的状态不为1(未开始),查询用户的竞猜排名和选手的得分数据
		if($data['match_status'] != 1){
			if($room_data['project_id'] == 4){
				$PlayerMatchData = M('PlayerMatchData');
			}elseif($room_data['project_id'] == 5){
				$PlayerMatchData = M('PlayerMatchDataLol');
			}elseif($room_data['project_id'] == 6){
				$PlayerMatchData = M('PlayerMatchDataDota2');
			}
			$player_match_info = array();
			$sum_score=0;
			foreach ($lineup_player_info as $key => $value) {
				$score_data = $PlayerMatchData->where(array('player_id' => $value['player_id'],'match_id' => $value['match_id']))->find();
				$score_data['scores'] = $score_data['scores']/10;
				$sum_score += $score_data['scores'];
				if ($room_data['project_id'] == 4) {
					$score_data['score'] = $score_data['score']/10;
				}
				$score_data['is_join'] = $score_data['is_join'] ? $score_data['is_join'] : 2;
				// $player_match_info[$key] = $score_data;
				$lineup_player_info[$key]['match_data'] = $score_data;
			}
			$_data['sum_score'] = $sum_score;
			// $_data['player_match_info'] = $player_match_info; //获取选手的比分数据

			//获取当前登录用户的数据
			$user_rank = $UserGuessRecord->field('id,uid,is_reward,ranking,lineup_score')->where(array('id' => $id,'room_id' => $room_id,'uid' => $this->_user['id']))->find();
			$user_rank['my_score'] = $user_rank['lineup_score']/10;
			$user_rank['lineup_score'] = $user_rank['lineup_score']/10;
			$user_rank['player_ranking'] = $user_rank['ranking'];
			//比赛中的数据获取一个最高得分,获取一个有奖得分
			$max_socre = $UserGuessRecord->order('lineup_score desc')->field('lineup_score')->where(array('room_id' => $room_id))->find();
			$user_rank['max_socre'] = $max_socre['lineup_score']/10;
			$user_rank['max_rank'] = 1;
			//获取我的积分
			$door_socre = $UserGuessRecord->field('lineup_score,ranking')->order('lineup_score asc')->where('room_id='.$room_id.' and is_reward>0')->find();
			$user_rank['door_socre'] = $door_socre['lineup_score']/10;
			$user_rank['door_rank'] = $door_socre['ranking'] ? $door_socre['ranking'] : 1;
			$_data['user_rank'] = $user_rank;

			//获取用户的排名前20数据
			$all_user_rank = $UserGuessRecord->field('id,uid,is_reward,ranking,lineup_score,lineup_id')->where(array('room_id' => $room_id))->order('ranking asc')->limit(50)->select(); //查询20条默认数据
			$top = array();
			foreach ($all_user_rank as $key => $value) { //没有做20条数据限制

				if($room_data['reward_id'] == 12){ //实物奖品显示实物的名称
					$value['is_reward'] = $value['is_reward'] ? $room_data['goods_name'] : '无';
				}else{
					$value['is_reward'] = $value['is_reward']; //可获得的奖励
				}

				if($value['guess_num'] > 1){
					for($i = 1; $i <= $value['guess_num'];$i++){
						$top[] = $value;
					}
				}else{
					$top[] = $value;
				}
			}

			foreach ($top as $key => $value) {
				$user_data = $this->getuserdata($value['uid']);
				$top[$key]['lineup_score'] = $value['lineup_score']/10;
				$top[$key]['username'] = $user_data['username'];
				$top[$key]['rank_name'] =  $user_data['rank_name'];
				$top[$key]['avatar_img'] = $user_data['avatar_img'];
				//获取用户所选阵容的出场总时间
				$lineup_data_info = $Lineup->where(array('id' => $value['lineup_id']))->find();
				$top[$key]['total_play_time'] = $lineup_data_info['total_play_time'];
			}
			$_data['all_user_rank'] = $top;
			//主播房,返回主播投注的信息
			if($room_data['is_special'] == 1 && $room_data['special_uid'] != 0){//主播房,返回主播投注的信息
				//获取主播所选的阵容
				$special_team_info = array();//存储返回的接口信息

				$special_data = M('UserGuessRecord')->where(array('uid' => $room_data['special_uid'],'room_id' => $room_id))->find();
				//获取主播的用户信息
				$special_team_info['user'] = $this->getuserdata($room_data['special_uid']);
				$special_team_info['user']['lineup_score'] = $special_data['lineup_score'] ? $special_data['lineup_score']/10 : 0;
				$_data['special_data'] = $special_team_info;
			}
		}
		$_data['lineup_player'] = $lineup_player_info;
		// $_data['lineup_player_l'] = $lineup_player_info_l;
		foreach ($lineup_player_info as $key => $value) {
			if($value['match_data']['is_join'] == 1){
				$_data['is_enter'] = 1;
			}else{
				$_data['is_enter'] = 2;
				break;
			}
		}
		$this->returnMsg(0,'room',$_data);
	}

	//获取用户所选的阵容
	//这里是根据数据库中的is_join(是否已经上场) 1 是 2 否;
	public function getuserlineup(){
		$id = $this->_data['id']; //房间的id
		$lineup_id = $this->_data['lineup_id']; //投注竞猜产生的阵容id
		$uid = $this->_data['uid'];
		if(!isset($uid)){
			$uid = $this->_user['id'];
		}
		$Map['id'] = $lineup_id;
		// 验证阵容ID是否为该用户所投注选择的阵容
		$data = M('lineup')->where($Map)->find(); //查询用户的投注的阵容数据
		$check = M('UserGuessRecord')->where('uid = '.$uid.' and room_id = '.$id.' and lineup_id ='.$lineup_id)->find();
		if(!$data || !$check){
			$this->returnMsg(1,'lineup');
		}
		$room_player = $this->getroomplayer($id);



		//获取用户投注的阵容数据
		$lineup_data = unserialize($data['lineup']);
		$room_data = $this->getroomdetail($id,'all');


		$all_players = $this->project_players($room_data['project_id']);

		// print_r($room_data);die;
		$project_id =$room_data['project_id'];
		// 判断项目id
		if($project_id == 4){
			$PlayerMatchData = M('PlayerMatchData');
		}elseif($project_id == 5){
			$PlayerMatchData = M('PlayerMatchDataLol');
		}elseif($project_id == 6){
			$PlayerMatchData = M('PlayerMatchDataDota2');
		}
		$lineup_player_info = array();	
		foreach ($lineup_data as $key => $value) { //循环数据,检测球员的上场状态
			$lineup_player_info[$key] = $all_players[$value];

			if ($project_id == 4) {
				$_data = $PlayerMatchData->where(array('player_id' => $value,'match_id' => $room_player[$value]['match_id']))->find();
				if($_data['is_join'] == 1 || $room_data['settlement_status'] != 1){
					$_data['score'] = $_data['score']/10;
					$lineup_data[$key] = $_data;
				}else{
					//隐藏球员的信息
					$lineup_data[$key] = array();
				}
			}elseif($project_id == 5){
				$_data = $PlayerMatchData->where(array('player_id' => $value,'match_id' => $room_player[$value]['match_id']))->find();
				$_data['scores'] = $_data['scores']/10;
				if(!empty($_data)){
					$lineup_data[$key] = $_data;
				}
			}elseif($project_id == 6){
				$_data = $PlayerMatchData->where(array('player_id' => $value,'match_id' => $room_player[$value]['match_id']))->find();
				$_data['scores'] = $_data['scores']/10;
				if(!empty($_data)){
					$lineup_data[$key] = $_data;
				}
			}
		}
		//阵容的其他信息,球员积分,剩余时间
		$lineup_other_info = array();
		$sum_score = 0;
		$have_time = 0;
		foreach ($lineup_data as $k => $v) {
			// if($room_data['settlement_status'] != 1){

			// }
			if($v){
				if ($project_id == 4) {
					$sum_score += $v['score'];
				}else{
					$sum_score += $v['scores'];
				}
				$have_time += 48 - intval($v['play_time']);
			}

		}
		if($room_data['settlement_status'] != 1){
			$lineup_other_info['have_time'] = 0;
		}else{
			$lineup_other_info['have_time'] = $have_time;
		}
		$lineup_other_info['count_score'] = $sum_score;
		$__data['lineup_player_info'] = $lineup_player_info;
		$__data['lineup_data'] = $lineup_data;
		$__data['match_list'] = $room_data['match_list'];
		$__data['user_data'] = $this->getuserdata($uid);
		$__data['user_guess_data'] = $check;
		$__data['lineup_other_info'] = $lineup_other_info;
		$__data['project_id'] = $project_id;
		// print_r($_data);
		$this->returnMsg(0,'room',$__data);

	}
	// 返回用户最近十条账变记录
	public function user_account(){
		// 用户ID
		$uid = $this->_user['id'];
		$data = M('UserAccount')->field('t1.*,t2.class_name')->join('as t1 left join '.c('DB_PREFIX').'user_account_class as t2 on t1.class_id = t2.id')->where('user_id = '.$uid)->order('id desc')->select();
		if ($data) {
			$this->returnMsg(0,'order',$data);
		}else{
			$this->returnMsg(1,'lineup');
		}
	}
		// 返回用户动态
	public function user_news(){
		$uid = $this->_user['id'];
		$data = M('UserGuessRecord')->field('t1.add_time,t1.ranking,t1.is_reward,t2.name')->join('as t1 left join '.c('DB_PREFIX').'match_room as t2 on t1.room_id = t2.id')->where(array('t1.uid'=>$uid,'t1.settlement_status'=>2))->order('t1.id desc')->limit(5)->select();
		$datas = array();
		if ($data) {
			foreach ($data as $key => $value) {
				$value['add_time'] = $value['add_time'];
				if ($value['is_reward'] > 0 ) {
					$value['is_reward'] = true;
				}else{
					$value['is_reward'] = false;
				}
				$value['class_id'] = 1; // 竞猜奖励
				$datas[$key] = $value;
			}
		}
		// 充值记录或流盘返还
		$data = M('UserAccount')->field('t1.addtime,t1.class_id,t1.back_nums,t2.class_name,t1.room_id')->join('as t1 left join '.c('DB_PREFIX').'user_account_class as t2 on t1.class_id = t2.id')->where(array('user_id'=>$uid,'class_id'=>array(array('EQ',10),array('EQ',13),'or')))->order('t1.id desc')->limit(5)->select();
		if ($data) {
			foreach ($data as $key => $value) {
				$values['add_time'] = $value['addtime'];
				$values['name'] = trim($value['class_name']);
				$values['back_nums'] = (int)$value['back_nums'];
				$values['class_id'] = 2; // 充值钻石
				if ($value['class_id'] == 13) {
					$values['class_id'] = 3; // 流盘返还
					$room_name = M('MatchRoom')->where('id ='.$value['room_id'])->getField('name');
					$values['name'] =  $room_name.'-'.$values['name'];
				}
				array_push($datas , $values);
			}
		}
		if (!$datas) {
			$this->returnMsg(12,'user');// 暂无动态
		}
		// 整理出准备排序的数组
		foreach ( $datas as $k => &$v ) {
			$array[$k] = isset($v['add_time']) ? $v['add_time'] : '';
		}
		unset($v);
		// 对需要排序键值进行排序
		arsort($array);
		// 重新排列原有数组
		foreach ( $array as $k => $v ) {
			$datas[$k]['add_time'] = date('m/d',$datas[$k]['add_time']);
			$result[$datas[$k]['add_time']][] = $datas[$k];
		}
		$k = 0;
		foreach ($result as $key => $value) {
			$last[$k]['date'] = $key; 
			$last[$k]['listinfo'] = $value; 
			$k ++;
		}
		$this->returnMsg(0,'room',$last);// 返回数据
	}
	// 返回用户钱数
	public function user_info(){
		// 用户ID
		$map['token'] = $this->_data['user_token'];
		$data = M('UserUser')->field('entrance_ticket,diamond,gold')->where($map)->find();
		if ($data) {
			$this->returnMsg(0,'order',$data);
		}else{
			$this->returnMsg(1,'lineup');
		}
	}
	// 修改用户名
	public function change_username(){
		$id = $this->_user['id']; // 用户id
		$UserMoreInfo = M('UserMoreInfo');
		$user_more_info = $UserMoreInfo->where(array('uid' => $id))->find();
		if($user_more_info['edit_name'] == 1){
			$this->returnMsg(15,'user');//已经修改过了
		}

		$data['username'] = $this->_data['username'];
		$length = mb_strlen($data['username'],'UTF8');
		$user = M('UserUser');
		if (!$data['username']) {
			$this->returnMsg(13,'user');// 该昵称已被占用
		}elseif($length < 3 || $length > 16  ){
			$this->returnMsg(14,'user');// 请填写正确昵称
		}
		$find = $user->where(array('username'=>$data['username']))->find();
		if ($find) {
			$this->returnMsg(11,'user');// 该昵称已被占用
		}
		$res = $user->where('id ='.$id)->save($data);
		if ($res) {
			$UserMoreInfo->where(array('uid' => $id))->setField('edit_name',1);
			$this->returnMsg(0,'edit');// 更改成功
		}else{
			$this->returnMsg(1,'edit');// 更改失败
		}
	}
	// 显示用户称号
	public function get_user_rank(){
		$id = $this->_user['id']; // 用户id
		$rank = M('UserRank');
		$rank_info = M('UserRankInfo');
		$find = $rank_info->where(array('uid'=>$id))->find();
		$ranks = explode(',', $find['rank_id']);
		$where = '1 != 1';
		if (!$find) {
			$rank_data['uid'] = $id;
        	$rank_data['rank_id'] = 1;
        	M('UserRankInfo')->add($rank_data);
		}
		foreach ($ranks as $key => $value) {
			$where .=' or id ='.$value;
		}
		$rank = $rank->field('id,name,class_id,avatar_img')->where($where)->select();
		foreach ($rank as $k => $v) {
			if ($v['id'] == $this->_user['rank']) {
				$rank[$k]['type'] = 1; // 使用中
			}else{
				$rank[$k]['type'] = 2; // 已经拥有
			}
		}
		if ($rank) {
			$this->returnMsg(0,'room',$rank);// 获取成功
		}else{
			$this->returnMsg(1,'room');// 获取失败
		}
	}
	// 展示所有的称号
	public function show_all_rank(){
		$id = $this->_user['id']; // 用户id
		$rank = M('UserRankInfo');
		//$find = $rank->where(array('uid'=>$id))->find();
		//$ranks = explode(',', $find['rank_id']);
		$data = M('UserRank')->field('t1.id,t1.name,t1.avatar_img,t1.depict,t1.class_id,t2.name as class_name')->join('as t1 left join '.c('DB_PREFIX').'user_rank_class as t2 on t1.class_id = t2.id')->select();
		if ($data) {
			// foreach($data as $key => $value){
			// 	if (in_array($value['id'],$ranks)) {
			// 		if ($value['id'] == $this->_user['rank']) {
			// 			$value['type'] = 1; // 使用中
			// 		}else{
			// 			$value['type'] = 2; // 已经拥有
			// 		}	
			// 	}else{
			// 		$value['type'] = 3;// 为获取到
			// 	}
			// 	$last[$value['class_id']-1]['class_name']= $value['class_name'];
			// 	unset($value['class_name']);
			// 	$last[$value['class_id']-1]['listinfo'][]= $value;
			// }
			//$datas['user'] = M('UserRank')->field('id,name,avatar_img,depict')->where(array('id'=>$this->_user['rank']))->find();
			$this->returnMsg(0,'room',$data);// 获取成功
		}else{
			$this->returnMsg(1,'room');// 获取失败
		}
	}
	// 设置用户称号
	public function set_user_rank(){
		$rank_id = $this->_data['id']; // 称号id
		$id = $this->_user['id']; // 用户id
		$rank = M('UserRankInfo');
		$find = $rank->where(array('uid'=>$id))->find();
		if ($find) {
			$rank = explode(',', $find['rank_id']);
			if (in_array($rank_id,$rank)) {
				$res = M('UserUser')->where('id ='.$id)->setField('rank',$rank_id);
				if ($res) {
					$this->returnMsg(0,'rank');// 称号设置成功
				}
			}
		}
		$this->returnMsg(1,'rank');// 称号设置失败
	}
	// 查询用户的绑定信息
	public function check_bind(){
		$id = $this->_user['id'];
		$open_info = M('UserOpenInfo');
		$data['bind_phone'] = $this->_user['phone'] ;
		if ($data['bind_phone'] == '') {
			$data['bind_phone'] = '';
		}
		$open = $open_info->where('user_id ='.$id)->find();
		if ($open) {
			if ($open['qq_openid'] == '') {
				$open['qq_openid'] = '';
			}
			if ($open['wx_openid'] == '') {
				$open['wx_openid'] = '';
			}
			if ($open['wb_openid'] == '') {
				$open['wb_openid'] = '';
			}
			$data['bind_qq'] = $open['qq_openid'];
			$data['bind_wx'] = $open['wx_openid'];
			$data['bind_wb'] = $open['wb_openid'];
		}else{
			$data['bind_qq'] = '';
			$data['bind_wx'] = '';
			$data['bind_wb'] = '';
		}
		$this->returnMsg(0,'room',$data);
	}
	// 绑定手机号
	public function bind_phone(){
		$data['phone'] = $this->_data['phone']; //绑定的手机号
		$phone = $data['phone'];
		if(!preg_match("/^1[34578][0-9]{9}$/", $phone)){
			$this->returnMsg(7,'user');// 请正确输入手机号
		}
		$code = $this->_data['smsCode'];
		if (is_null($phone)) {
			$this->returnMsg(2,'customer');
		}
		if (is_null($code)) {
			$this->returnMsg(2,'customer');
		}
		$Sms = M('AdminSms');
		$Map['phone'] = $phone;
		$Map['sms_code'] = $code;
		$Map['todo'] = 3;
		$Map['status'] = 0;
		$result = $Sms->where($Map)->order('id desc')->find();
		if (is_null($result)) {
			$this->returnMsg(9,'sms');	
		}else{
			$limit = 60 * 10;
			$time = time();
        	$val = $time - $result['sms_time'];// 十分钟内验证
			if ($val > $limit) {
				$this->returnMsg(4,'sms');	
			}else{
				$data['status'] = 1;
				$Sms->where($Map)->save($data);// 更改验证状态
				// 判断有没有发送过短信，且通过短信验证
				$UserUser = M('UserUser');
				$res = $UserUser->where(array('phone'=>$data['phone']))->find();
				if ($res) {
					$this->returnMsg(5,'sms');// 该号码已被注册,请更换其他手机绑定
				}else{
					$result = $UserUser->where(array('id'=>$this->_user['id']))->setField('phone',$data['phone']);
					if ($result) {
						$this->returnMsg(0,'bind');// 绑定成功
					}else{
						$this->returnMsg(2,'bind');// 绑定失败
					}
				}
			}
		}
	}
	// 查询用户背包
	public function bag(){
		$page = $this->_data['page'];
        if(empty($page)){
            $start = 0;
        }else{
            $start = ($page-1)*10;
        }
        // 总记录数
        $count = M('ShopGoodsOrder')->where(array('user_id'=>$this->_user['id'],'address_id'=>0))->count();
        // 页码数
        $lastpage = ceil($count/10);
		$res = M('ShopGoodsOrder')->field('t1.id,t1.goods_id,t1.goods_nums,t1.price,t2.name,t2.avatar_img,t2.type')->join('as t1 LEFT JOIN '.c('DB_PREFIX').'shop_goods as t2 on t1.goods_id = t2.id')->where(array('t1.user_id'=>$this->_user['id'],'t1.address_id'=>0))->limit($start,10)->order('id desc')->select();
		if ($res) {
            $this->returnMsg(0,'order',$res,$lastpage);// 获取成功
        }else{
            $this->returnMsg(1,'room');// 无订单信息
        }
	}
	// 消息中心
	public function notice_list(){
		$res = M('UserNotice')->field("id,title,depict,FROM_UNIXTIME(addtime, '%Y-%c-%d %h:%i:%s' ) as time")->limit(10)->order('id desc')->select();
		if (!empty($this->_user['hide_notice'])) {
			$hide = explode(',',$this->_user['hide_notice']);// 不显示的文章编号
		}
		if (!empty($this->_user['notice_state'])) {
			$state = explode(',',$this->_user['notice_state']);// 未读记录
		}
		foreach ($res as $key => $value) {
			if (in_array($value['id'],$state)) {
				$res[$key]['state'] = 0;	
			}else{
				$res[$key]['state'] = 1;
			}
			if (in_array($value['id'],$hide)) {
				unset($res[$key]);
			}
		}
		if ($res) {
            $this->returnMsg(0,'order',array_values($res));// 获取成功
        }else{
            $this->returnMsg(1,'room');// 获取失败
        }
	}
	// 消息详情
	public function notice_detail(){
		$where['id'] = $this->_data['id'];
		$res = M('UserNotice')->field("id,title,notice,FROM_UNIXTIME(addtime, '%Y-%c-%d %h:%i:%s' ) as time")->where($where)->find();
		if ($res) {
			//更改当前的消息为已读
			$UserUser = M('UserUser');
			$data = $UserUser->where(array('id' => $this->_user['id']))->field('notice_state')->find();
			if($data['notice_state'] == ''){
				$UserUser->where('id = '.$this->_user['id'])->setField('notice_state',$this->_data['id']);
			}else{
				$n = explode(',', $data['notice_state']);
				if(!in_array($this->_data['id'], $n)){
					$n[] = $this->_data['id'];
					$str = implode(',', $n);
					$UserUser->where('id = '.$this->_user['id'])->setField('notice_state',$str);
				}

			}

            $this->returnMsg(0,'order',$res);// 获取成功
        }else{
            $this->returnMsg(1,'room');// 获取失败
        }
	}
	// 更改消息为已读
	public function set_notice_state(){
		$id = $this->_data['id'];// 改为已读的文章id
		$res = M('UserNotice')->limit(10)->order('id desc')->select();
		$ids = array_column($res,'id');
		// 判断是否在最新的10条文章中
		if (!in_array($id,$ids)) {
			$this->returnMsg('common');// 参数错误
		}
		if (!empty($this->_user['notice_state'])) {
			$state = explode(',',$this->_user['notice_state']);// 未读记录
			$nums = count($state);
			if ($nums == 10) {
				unset($state[0]);
			}
		}
		$state[10] = $id;// 添加文章id
		$notice_state = implode(',',$state);
		$result = M('UserUser')->where('id = '.$this->_user['id'])->setField('notice_state',$notice_state);
		if ($result) {
            $this->returnMsg(0,'edit');// 修改成功
        }else{
            $this->returnMsg(1,'edit');// 修改失败
        }
	}
	// 删除消息
	public function delete_notice(){
		$id = $this->_data['id'];// 改为已读的文章id
		$res = M('UserNotice')->limit(10)->order('id desc')->select();
		$ids = array_column($res,'id');
		// 判断是否在最新的10条文章中
		if (!in_array($id,$ids)) {
			$this->returnMsg('common');// 参数错误
		}
		if (!empty($this->_user['hide_notice'])) {
			$state = explode(',',$this->_user['hide_notice']);// 未读记录
			$nums = count($state);
			if ($nums == 10) {
				unset($state[0]);
			}
		}
		$state[10] = $id;// 添加文章id
		$hide_notice = implode(',',$state);
		$result = M('UserUser')->where('id = '.$this->_user['id'])->setField('hide_notice',$hide_notice);
		if ($result) {
            $this->returnMsg(0,'delete');// 修改成功
        }else{
            $this->returnMsg(1,'delete');// 修改失败
        }
	}
	//爱伐木充值
	public function recharge(){
		$scale = C('scale'); //充值比例
		$pay_type = array('wx_app','wx_pc','alipay_app','alipay_pc','alipay_wap','wx_wap'); //支付的类型

		$type = $this->_data['type'];
		if(!in_array($type, $pay_type)){ //检测支付类型是否正确
			$this->returnMsg(1);
		}
		$userData = $this->_user; //检测用户的状态

		// $userData['id'] = 4;

		$money = $this->_data['money'];//充值金额
		if(!is_int($money) && $money <= 0){
			$this->returnMsg(1,'charge');
		}

		// $money = 0.1;

		$now_time = time();
		$amount = $money * $scale;
		$orderNo = md5($now_time . $userData['id'] . rand(1,99999));//订单号
		
		$data['uid'] = $userData['id'];
		$data['amount'] = $amount;//实际充值的木头数目
		$data['order_no'] = $orderNo;
		$data['add_time'] = time();
		$data['sign_num'] = rand(1,10000);
		$data['client_ip'] = get_client_ip();
		$UserCharge = M('UserCharge');
		$subject = '爱伐木钻石充值'.floor($money).'元';//商品描述
		if($type == 'wx_app'){ //微信app支付
			$data['channel'] = $type;
			$result_pay = $UserCharge->add($data);
			if(!$result_pay){
				$this->returnMsg(1,'system');
			}

			$_data['appid'] = $this->wxappid; //微信appid
			$_data['mch_id'] = $this->wxmchid; //微信mchid
			$_data['nonce_str'] = md5($data['sign_num']); //随机字符串
			$_data['body'] = $subject; //商品描述
			// $_data['timeStamp'] = $now_time;
			$_data['out_trade_no'] = $orderNo; //商品订单号
			$_data['total_fee'] = $money*100; //订单总金额,单位分
			$_data['spbill_create_ip'] = get_client_ip(); //用户实际的ip地址
			$_data['notify_url'] = 'http://api.aifamu.com/pay/wx_pay/payback.php'; //回调地址
			$_data['trade_type'] = 'APP';//支付类型
			$_data['time_start'] = date('YmdHis');
			$_data['time_expire'] = date('YmdHis',time()+600);
			$_data['sign'] = $this->build_sign($_data); //签名
			$request_data = $this->toxml($_data); //生成xml数据
			$result = $this->c_url($this->wx_place_order,$request_data);
			// echo $result;die;
			$msg = (array) simplexml_load_string($result,'SimpleXMLElement',LIBXML_NOCDATA);
			// print_r($msg);
			if($msg['return_code'] != 'SUCCESS'){
				// $msg['return_msg']
				$this->returnMsg(2,'charge');
			}else{
				$msg['timestamp'] = $now_time;
				$msg['package'] = 'Sign=WXPay';

				$_sgin['appid'] = $msg['appid'];
				$_sgin['noncestr'] = $msg['nonce_str'];
				$_sgin['package'] = $msg['package'];
				$_sgin['partnerid'] = $msg['mch_id'];
				$_sgin['timestamp'] = $msg['timestamp'];
				$_sgin['prepayid'] = $msg['prepay_id'];
				
				$msg['sign'] = $this->build_sign($_sgin); //签名

				$this->returnMsg(0,'charge',$msg); //微信支付返回的正确数据
			}
		}elseif($type == 'alipay_wap'){//支付宝h5支付
			require_once("./pay/alipay/alipay.config.php");
			require_once("./pay/alipay/lib/alipay_submit.class.php");
			$data['channel'] = $type;
			$result_pay = $UserCharge->add($data);
			if(!$result_pay){
				$this->returnMsg(1,'system');
			}
			/**************************请求参数**************************/
	        //商户订单号，商户网站订单系统中唯一订单号，必填
	        $out_trade_no = $orderNo;
	        //订单名称，必填
	        $subject = $subject;
	        //付款金额，必填
	        $total_fee = $money;
	        //收银台页面上，商品展示的超链接，必填
	        $show_url = $_POST['WIDshow_url'];
	        // $show_url = 'http://api.aifamu'
	        //商品描述，可空
	        $body = '爱伐木钻石充值';
			/************************************************************/
			//构造要请求的参数数组，无需改动
			$parameter = array(
				"service"       => $alipay_config['service'],
				"partner"       => $alipay_config['partner'],
				"seller_id"  => $alipay_config['seller_id'],
				"payment_type"	=> $alipay_config['payment_type'],
				"notify_url"	=> $alipay_config['notify_url'],
				"return_url"	=> $alipay_config['return_url'],
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"show_url"	=> $show_url,
				"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
				"body"	=> $body,
				//其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
		        //如"参数名"	=> "参数值"   注：上一个参数末尾需要“,”逗号。
			);
			//建立请求
			$alipaySubmit = new AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
			echo $html_text;
		}elseif($type == 'wx_wap'){

			//这个支付接口是微信公众号支付,需检查用户的openid

			$openid_data = M('UserOpenInfo')->where(array('user_id' => $userData['id']))->find();
			if(!$openid_data || $openid_data['wx_pay_openid'] == ''){
				$this->returnMsg(3,'charge'); //用户openid不存在
			}

			$data['channel'] = $type;
			$result_pay = $UserCharge->add($data);
			if(!$result_pay){
				$this->returnMsg(1,'system');
			}

			$_data['openid'] = $openid_data['wx_pay_openid'];
			$_data['appid'] = $this->h5_wxappid; //微信appid
			$_data['mch_id'] = $this->h5_wxmchid; //微信mchid
			$_data['nonce_str'] = md5($data['sign_num']); //随机字符串
			$_data['body'] = $subject; //商品描述
			// $_data['timeStamp'] = $now_time;
			$_data['out_trade_no'] = $orderNo; //商品订单号
			$_data['total_fee'] = $money*100; //订单总金额,单位分
			$_data['spbill_create_ip'] = get_client_ip(); //用户实际的ip地址
			$_data['notify_url'] = 'http://api.aifamu.com/pay/wx_pay/payback.php'; //回调地址
			$_data['trade_type'] = 'JSAPI';//支付类型

			$_data['time_start'] = date('YmdHis');
			$_data['time_expire'] = date('YmdHis',time()+600);
			$_data['sign'] = $this->build_sign($_data,'h5'); //签名
			$request_data = $this->toxml($_data); //生成xml数据
			$result = $this->c_url($this->wx_place_order,$request_data);

			$msg = (array) simplexml_load_string($result,'SimpleXMLElement',LIBXML_NOCDATA);

			if($msg['return_code'] != 'SUCCESS'){
				file_put_contents('./pay.txt', json_encode($msg));
				$this->returnMsg(2,'charge');
			}else{
				$msg['timestamp'] = $now_time;
				$msg['package'] = 'prepay_id='.$msg['prepay_id'];

				$_sgin['appId'] = $msg['appid'];
				$_sgin['nonceStr'] = $msg['nonce_str'];
				$_sgin['package'] = 'prepay_id='.$msg['prepay_id'];
				// $_sgin['partnerid'] = $msg['mch_id'];
				$_sgin['timeStamp'] = $msg['timestamp'];
				$_sgin['signType'] = 'MD5';
				// $_sgin['prepayid'] = $msg['prepay_id'];
				$msg['sign'] = $this->build_sign($_sgin,'h5'); //签名
				$msg['timestamp'] = "$msg[timestamp]";
				// file_put_contents('./msg.txt', json_encode($ms));
				$this->returnMsg(0,'charge',$msg);
			}
		}else{

		}
	}
	//按照 ascii 排序生成签名.仅提供微信使用
	protected function build_sign($params,$type){
		if($type == 'h5'){
			$wx_k = $this->h5_wxkey;
		}else{
			$wx_k = $this->wxkey;
		}
	    if(empty($params) || !is_array($params)) return '';
	    // 排序
	    ksort($params);
	    $tmpStr = '';
	    foreach ($params as $key => $value) {
	        $tmpStr = sprintf('%s%s=%s&', $tmpStr, $key, $value);
	    }
	    $tmpStr .= 'key=' . $wx_k;
	    // echo $tmpStr;
	    return strtoupper(md5($tmpStr));
	}
	//生成xml数据,仅提供微信支付使用
	protected function toxml($data){
		if(!is_array($data) || empty($data)){
			return '';
		}
		$xml_str ='<xml>';
		foreach ($data as $key => $value) {
			$xml_str .= '<'.$key.'>'.$value.'</'.$key.'>';
		}
		return $xml_str.'</xml>';
	}
		/*
	上传头像
	param apiToken token uploadFile
	error code 
		0 上传成功
		1 token验证错误 或 文件大小超过服务器限制
		2 文件大小超过表单限制
		3 文件只有部分被上传
		4 没有文件被上传
		5 文件移动失败
		6 不是图片文件
		7 文件大小超过1MB
		8 缩略图创建失败
	*/
	public function upload(){
		$user = $this->_user['id'];
		define('PATH', './avator/%s/');

		file_put_contents('./upload.txt', json_encode($_FILES).date('Y-m-d H:i:s')."\r\n",FILE_APPEND);
		file_put_contents('./post.txt', json_encode($_POST).date('Y-m-d H:i:s')."\r\n",FILE_APPEND);
		if (isset($_FILES['file'])){
			
			$arrFiles = $_FILES['file'];
			if ($arrFiles['error'] == 0){		
				$arrInfo = pathinfo($arrFiles['name']);
				$fileExtension = $arrInfo['extension'];
				if (!in_array($fileExtension, array('png', 'jpg', 'jpeg', 'gif'))){
					$this->returnMsg(1,'upload');// 请上传后缀为'png', 'jpg', 'jpeg', 'gif'图片
				}
				$fileSize = @filesize($arrFiles['tmp_name']);
				$img_info = getimagesize($arrFiles['tmp_name']);
				$img_w = 400;
				$img_h = round($img_info[0] * $img_info[1] / $img_w); //等比例缩略
				if($fileSize > 2048*1024)
				{
					$this->returnMsg(2,'upload');// 文件太大
				}
				$userDir = floor($user/500);
				$Folder = sprintf(PATH, $userDir);
				if (!file_exists($Folder)) {
					mkdir($Folder,0777,true);
				}		
				$fileName = sprintf('%s.%s', $user, 'jpg');		
				$filePath = $Folder . $fileName;
				$result = $this->Thumb($arrFiles['tmp_name'], $filePath,'',$img_w, $img_h);
				if ($result) {
					$this->returnMsg(0,'upload');// 上传成功
				}else{
					$this->returnMsg(4,'upload');// 上传失败
				}
			}else{
				$this->returnMsg(3,'upload');// 无文件上传
			}	
		}else{
			$this->returnMsg(2,'reward');
		}
	}
	// 创建缩略图
	private function thumb($image, $thumbname, $type='', $maxWidth=200, $maxHeight=50, $interlace=true) {
        // 获取原图信息
        $info = $this->getImageInfo($image);
        if ($info !== false) {
            $srcWidth = $info['width'];
            $srcHeight = $info['height'];
            $type = empty($type) ? $info['type'] : $type;
            $type = strtolower($type);
            $interlace = $interlace ? 1 : 0;
            unset($info);
            $scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight); // 计算缩放比例
            if ($scale >= 1) {
                // 超过原图大小不再缩略
                $width = $srcWidth;
                $height = $srcHeight;
            } else {
                // 缩略图尺寸
                $width = (int) ($srcWidth * $scale);
                $height = (int) ($srcHeight * $scale);
            }

            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            if(!function_exists($createFun)) {
                return false;
            }
            $srcImg = $createFun($image);

            //创建缩略图
            if ($type != 'gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);
              //png和gif的透明处理 by luofei614
            if('png'==$type){
                imagealphablending($thumbImg, false);//取消默认的混色模式（为解决阴影为绿色的问题）
                imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息（为解决阴影为绿色的问题）    
            }elseif('gif'==$type){
                $trnprt_indx = imagecolortransparent($srcImg);
                 if ($trnprt_indx >= 0) {
                        //its transparent
                       $trnprt_color = imagecolorsforindex($srcImg , $trnprt_indx);
                       $trnprt_indx = imagecolorallocate($thumbImg, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                       imagefill($thumbImg, 0, 0, $trnprt_indx);
                       imagecolortransparent($thumbImg, $trnprt_indx);
              }
            }
            // 复制图片
            if (function_exists("ImageCopyResampled"))
                imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            else
                imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

            // 对jpeg图形设置隔行扫描
            if ('jpg' == $type || 'jpeg' == $type)
                imageinterlace($thumbImg, $interlace);

            // 生成图片
            $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
            $imageFun($thumbImg, $thumbname);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumbname;
        }
        return false;
    }
    protected function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo['mime']
            );
            return $info;
        } else {
            return false;
        }
    }
}