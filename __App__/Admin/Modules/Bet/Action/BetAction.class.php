<?php
/**
 * @author wangh 2017.2.20
 */

class BetAction extends AdminAction{
	//获取房间的类型
	protected function getmatchroomtype(){
		$roomtype = $this->getcache('room_type');
		$this->assign('roomtype',$roomtype);
	}

	//获取房间的阵容配置
	public function getroomlineup(){
		$this->assign('lineup',C('MATCH_ROOM_LINEUP'));
		$this->assign('room_tag',C('ROOM_TAG'));
	}
	//列表
	public function index(){
		$MatchRoom = M('MatchRoom');
		import('ORG.Util.Page');
		$count = $MatchRoom->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$data = $MatchRoom->limit($page->firstRow . ',' . $page->listRows)->order('match_start_time desc')->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->assign('api_url',C('API_URL'));
		$this->display();	
	}
	//添加
	public function add(){
		$MatchRoom = M ('MatchRoom');
		$data = $MatchRoom->create();
		if ($data) {
			// print_r($data);
			if ($data['type_id'] != 5) {
				unset($data['special_name']);
				unset($data['special_uid']);
			}
			if ($data['open_id'] != 2) {
				unset($data['open_num']);
			}
			if (!in_array($data['reward_id'],array(1,2,3,7,12))) {
				unset($data['prize_num']);
				// unset($data['reward_num']);
				$reward_info = M('RewardRule')->where(array('id' => $data['reward_id']))->find();
				if(!$reward_info){
					$this->error('奖励配置不存在',U('add'));
				}
				$data['reward_num'] = $reward_info['reward_num'];
			}
			$data['add_time'] = time();
			$data['match_start_time'] = strtotime($data['match_start_time']);
			$data['match_end_time'] = strtotime($data['match_end_time']);
			$data['end_time'] = strtotime($data['end_time']);
			if(empty($_POST['match_team'])){
				$this->error('请添加赛事',U('add'));
			}
			$result = $MatchRoom->add($data);
			if($result){
				// 添加赛事信息

				$info['show_date'] = $_POST['show_date'] ? strtotime($_POST['show_date']) : 0; //自动发布时间

				$info['room_id'] = $result;
				$info['match_team'] = implode(',',$_POST['match_team']); 
				$res = M('MatchRoomInfo')->add($info);
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('add'));
			}
		} else {
			$this->getmatchroomtype();
			$match_list = M('match_list')->field('t2.`short_name` as a_name,t2.img as a_img,t3.`short_name` as b_name,t3.img as b_img,t1.id,t1.match_time')->order("t1.match_time desc")->join('as t1 left join '.c('DB_PREFIX').'match_team as t2 on t1.team_a = t2.id  left join '.c('DB_PREFIX').'match_team as t3 on t1.team_b=t3.id')->where('t1.match_time > '.time())->select();
			$reward = M('RewardRule')->field('id,name')->where(array('status' =>1))->select();
			$project = M('MatchProject')->field('id,name')->select();
			foreach ($match_list as $key => $value) {
                $day = date('Y-m-d',$value['match_time']);
                $value['match_time'] = date('Y-m-d H:i:s',$value['match_time']);
                $list[$day][]=$value; 
            }
            $author = $_SESSION['admin']['nickname'];
            $this->assign('author',$author);
			$this->assign('reward',$reward);
			$this->assign('project',$project);
			$this->assign('match_list',$list);
			$this->getroomlineup();
			$this->display();
		}
	}
	//修改
	public function edit(){
		$MatchRoom = M('MatchRoom');
		// print_r($_POST);
		$data = $MatchRoom->create();
		if ($data) {
			// print_r($data);
			// print_r($_REQUEST);
			if ($data['type_id'] != 5) {
				unset($data['special_name']);
				unset($data['special_uid']);
			}
			if ($data['open_id'] != 2) {
				unset($data['open_num']);
			}
			if (!in_array($data['reward_id'],array(1,2,3,7,12))) {
				unset($data['prize_num']);

				$reward_info = M('RewardRule')->where(array('id' => $data['reward_id']))->find();
				if(!$reward_info){
					$this->error('奖励配置不存在',U('index'));
				}
				$data['reward_num'] = $reward_info['reward_num'];

			}
			$data['match_start_time'] = strtotime($data['match_start_time']);
			$data['match_end_time'] = strtotime($data['match_end_time']);
			$data['end_time'] = strtotime($data['end_time']);
			$Map['id'] = $data['id'];
			$res = $MatchRoom->where($Map)->find();
			if(empty($_POST['match_team'])){
				$this->error('请选择比赛',U('index'));
			}
			$match_team = implode(',',$_POST['match_team']);

			$show_date = $_POST['show_date'] ? strtotime($_POST['show_date']) : 0; //自动发布时间

			$res = M('MatchRoomInfo')->where('room_id = '.$data['id'])->save(array('match_team'=>$match_team,'show_date' => $show_date));
			$result = $MatchRoom->save($data);
			// print_r($data);
			// var_dump($res);var_dump($result);
			// echo $MatchRoom->getLastSql();
			// echo M('MatchRoomInfo')->getLastSql();
			// die;
			if($result || $res){
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('index'));
			}
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $MatchRoom->where($Map)->find();
			$this->getmatchroomtype();

			$reward = M('RewardRule')->field('id,name')->where(array('status' =>1))->select();
			$project = M('MatchProject')->field('id,name')->select();
			$match_list = M('match_list')->field('t2.`short_name` as a_name,t2.img as a_img,t3.`short_name` as b_name,t3.img as b_img,t1.id,t1.match_time')->join('as t1 left join '.c('DB_PREFIX').'match_team as t2 on t1.team_a = t2.id  left join '.c('DB_PREFIX').'match_team as t3 on t1.team_b=t3.id')->where('t1.match_time > '.time())->select();
			foreach ($match_list as $key => $value) {
                $day = date('Y-m-d',$value['match_time']);
                $value['match_time'] = date('Y-m-d H:i:s',$value['match_time']);
                $list[$day][]=$value; 
            }
            $match = M('MatchRoomInfo')->where('room_id = '.$id)->find();
            $data['show_date'] = $match['show_date'];
            $array = explode(',',$match['match_team']);
			$this->assign('reward',$reward);
			$this->assign('project',$project);
			$this->assign('match_list',$list);
			$this->assign('data',$data);
			$this->assign('array',$array);
			$this->getroomlineup();
			$this->display();
		}
	}
	//删除,最后做
	public function del(){
		exit('暂时关闭');
	}
	public function state() {
        $id = $this->_get('id');
        $m = M('MatchRoom');
        $status = $this->_get('status');
        $name = $this->_get('name');
        $map['id'] = $id;
        if ($m->where($map)->setField($name, $status)) {
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
        
    }
	// 下注详情
	public function guessCount(){
		$MatchRoom = M('MatchRoom');
		$where['id'] = $_GET['match_id'];
		$roomdetail = M('UserGuessRecord')->where('room_id ='.$_GET['match_id'])->select();
		$guess_num = 0;
		$count = 0;
		foreach ($roomdetail as $key => $value) {
			$guess_num += $value['guess_num'];
			$count +=$value['is_reward'];;
		}
		// 用户获取木头数量
		$this->assign('guess_all',$guess_num);
		$this->assign('guess_win',$count);
		$this->display();
	}
	// 赛事结算
	public function get_result(){
		$MatchRoom = M('MatchRoom');
		$where['id'] = $_GET['match_id'];
		$start = $_GET['start'];
		$roomdetail = $MatchRoom->where($where)->find();
		if (time() < $roomdetail['end_time']) {
			$this->msg (1,'还未到结算时间');
		}
		$prize_type = $roomdetail['prize_type'];// 奖品类型1门票2木头
		// 后台发送消息通知
		// 1、找到中奖的用户消息
		$UserGuessRecord = M('UserGuessRecord');
		$data = $UserGuessRecord->where('match_status = 2 and settlement_status = 1 and room_id ='.$_GET['match_id'])->limit(100)->select();
		
		 //var_dump($total);exit;
		if ($data) {
			$success = 0;
			$error = 0;
			// 2、入竞猜消息表	
			foreach ($data as $key => $value) {
				if ($value['match_status'] == 2 && $value['settlement_status'] == 1 ) {
					$UserGuessRecord->where('id ='.$value['id'])->save(array('match_status' => 3,'settlement_status' => 2)); //更改比赛状态和结算状态
					// $UserGuessRecord->where('id ='.$value['id'])->setField('',2);
					if($value['is_reward'] == 0){
						continue;
					}
					$table_name = $this->get_hash_table('UserBetGess',$value['uid']);
					$UserBetGess = M($table_name);
					$msg['room_id']=$value['room_id'];
					$msg['uid']=$value['uid'];
					$msg['nums']=$value['is_reward'];
					// $msg['nums']=$value['is_reward']*$value['guess_num'];
					$msg['lineup_score']=$value['lineup_score'];// 阵容积分
					$msg['type']=$prize_type; // 奖品类型1门票3木头
					$msg['ranking']=$value['ranking']; // 最终排名
					$msg['addtime']=time();
					$res = $UserBetGess->add($msg);
					if ($res) {
						$success += 1; 
					}else{
						$error += 1; 
					}
				}
			}
			$this->msg(1,'成功结算'.$success.'个,失败'.$error.'个');
		}else{
			$MatchRoom->where($where)->setField('settlement_status',2);
			$this->msg(0,'结算完成');;
		}
	}
	// 赛事流盘
	public function giveup(){
		$MatchRoom = M('MatchRoom');
		$where['id'] = $_GET['match_id'];
		$start = $_GET['start'];
		$roomdetail = $MatchRoom->where($where)->find();
		$prize_type = $roomdetail['prize_type'];// 奖品类型1门票2木头
		if ($price_type != 1) {
			$prize_type = 3;
		}
		//
		// 后台发送消息通知
		// 1、找到投注的用户消息
		$total  =  M('UserGuessRecord')->where('room_id ='.$_GET['match_id'])->count();
		$data = M('UserGuessRecord')->where('room_id ='.$_GET['match_id'])->limit($start,50)->select();
		$MatchRoom->where($where)->setField('settlement_status',3);
		 //var_dump($total);exit;
		if ($data) {
			if (($start+1) > $total) {
				$this->msg(0,'完成流盘',$data[]);
			}
			// 2、入竞猜消息表	
			foreach ($data as $key => $value) {
				if ($value['match_status'] == 2 && $value['settlement_status'] == 1 ) {
					M('UserGuessRecord')->where('id ='.$value['id'])->setField('match_status',3);
					M('UserGuessRecord')->where('id ='.$value['id'])->setField('settlement_status',3);
					$this->insert_account(13,$prize_type,$value['uid'],$value['guess_num']*$roomdetail['price'],true,$value['room_id']);
				}
			}
			$precess = floor(($start + 1) / $total *100);
			$msgs = "完成进度" . $precess . "%";
			$this->msg(0,$msgs,$datas['']);
		}else{
			$this->msg(1,'完成流盘');;
		}
	}
	
	//获取用户名分表表的名称
	protected function get_hash_table($table,$userid) {  
		$str = crc32($userid);  
		if($str<0){  
		$hash = substr(abs($str), 0, 1);  
		}else{  
		$hash = substr($str, 0, 1);  
		}  

		return $table."_".$hash;  
	} 
	//获取分表表的名称
    protected function gettable($id){
        // UserGuessRcord
        $table_num = $id%10;
        return 'UserGuessRecord_' . $table_num;
    }
    private function msg($error,$msg,$finish = 0){
		$data['error'] = $error;
		$data['msg'] = $msg;
		$data['finish'] = $finish;
		echo json_encode($data);
		exit();
	}
	// 查询该场比赛的球员数据
	public function get_match_list(){
		$post = $this->_post();
		$project_id = $post['project_id'];
		$room_id = $post['room_id'];
		if ($room_id !='') {
			$match = M('MatchRoomInfo')->where('room_id = '.$room_id)->getField('match_team');
			$list['match'] = explode(',',$match);
		}

		$match_list = M('match_list')->field('t2.`short_name` as a_name,t2.img as a_img,t3.`short_name` as b_name,t3.img as b_img,t1.id,t1.match_time')->join('as t1 left join '.c('DB_PREFIX').'match_team as t2 on t1.team_a = t2.id  left join '.c('DB_PREFIX').'match_team as t3 on t1.team_b=t3.id')->where('t1.project_id = '.$project_id.' and t1.match_time > '.time())->order("t1.match_time desc")->select();
		foreach ($match_list as $key => $value) {
            $day = date('Y-m-d',$value['match_time']);
            $value['match_time'] = date('Y-m-d H:i:s',$value['match_time']);
            $list['match_list'][$day][]=$value; 
        }
        if (!$match_list) {
        	$list['error'] = 1;
        }else{
        	$list['error'] = 0;
        }
		echo json_encode($list);die;
	}
	//更新房间
	public function updateroom(){
		$this->display();
	}
	//更新房间
	public function updatetop(){
		if (IS_POST) {
			$map['project_id'] = $_POST['project_id'];
			if($map['project_id'] == 5){
	        	$PlayerMatchData = M('PlayerMatchDataLol');
	        }elseif($map['project_id'] == 6){
	        	$PlayerMatchData = M('PlayerMatchDataDota2');
	        }
	        $gttime = strtotime($_POST['day']);
	        $lttime = strtotime($_POST['day'])+24*3600;
			$map['match_start_time'] = array(array('egt',$gttime),array('elt',$lttime),'and');
			$room_id = M('MatchRoom')->field('id')->where($map)->select();
			if ($room_id) {
				$where = '(1 != 1 ';
				foreach ($room_id as $keys => $values) {
					$where .= ' or room_id =' . "'" . $values['id'] . "'";
				}
				$where .= ' ) and ( is_reward > 100  and settlement_status = 2 )';// and settlement_status = 2
				$data = M('UserGuessRecord')->where($where)->order('lineup_score desc')->find();// 获取对应房间
				if (!$data) {
					echo  M('UserGuessRecord')->getlastsql();
					$this->error('无数据更新失败');exit;
				}
				$linup_data = M('lineup')->where('id = '.$data['lineup_id'])->find();
				$linup = unserialize($linup_data['lineup']);// 还原阵容
				$match_id = M('MatchRoomInfo')->where('room_id ='.$data['room_id'])->find();// 获取赛事id 
				$team = explode(',',$match_id['match_team']);
				$where2 = '1 != 1 ';
				foreach ($team as $keys => $values) {
					$where2 .= ' or match_id =' . "'" . $values . "'";
				}
				$player_data = $PlayerMatchData->where($where2)->select();// 获取场次id 
				//var_dump($where2);exit;
				//var_dump($where2);exit;
				foreach ($player_data as $key => $value) {
					foreach ($linup as $k => $v) {
						if ($v == $value['player_id']) {
							$lineups['match_data'][$k] =  $value;
							if ($map['project_id'] == 5) {
								$lineups['match_data'][$k]['last_score'] = $this->scorerule_lol($k,$value);
							}elseif($map['project_id'] == 6){
								$lineups['match_data'][$k]['last_score'] = $this->scorerule_dota2($k,$value);
							}
							if($value['death']==0){
								$value['death']==1;
							}
							$lineups['match_data'][$k]['KDA'] = number_format(($value['kill']+$value['assists'])/$value['death'],1);
							$lineups['match_data'][$k]['player_data'] = M('MatchPlayerWcg')->field('name,img,salary,position,team_id')->where('id ='.$v)->find();
							$team_data = M('MatchTeam')->field('name,img')->where('id = '.$lineups['match_data'][$k]['player_data']['team_id'])->find();
							$lineups['match_data'][$k]['player_data']['team_name'] = $team_data['name'];
							$lineups['match_data'][$k]['player_data']['team_img'] = $team_data['img'];
						}
					}
				}
				$uid = $data['uid'];
				$lineups['campion'] = M('MatchRoom')->field('prize_type')->where('id = '.$data['room_id'])->find();
				$lineups['campion']['name'] = M('UserUser')->where('id ='.$uid)->getfield('username');
				$lineups['campion']['avatar_img'] = 'http://api.aifamu.com/avator/icon.php?id='.$uid;
				$lineups['campion']['nums'] = $data['is_reward'];
				$lineups['campion']['room_id'] = $data['room_id'];
				$lineups['campion']['date'] = $_POST['day'];
				$datas["error"]= 0;
				$datas["msg"] ="获取成功";
				$datas["data"] =$lineups;
				$datas["extra_data"] ='';
				file_put_contents('lineup_top'.$map['project_id'].'.txt',json_encode($datas));
				$this->success('更新成功');exit;
			}
		}else{
			$this->display();
		}
	}
	
	protected function scorerule_dota2($position,$player_match_data){
        if ($position == 7) {
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
        }else{
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
        }
        foreach ($player_match_data as $key => $value) {
            $score_sum += $socre_rule[$key] * $value;
        }

        return number_format($score_sum,1);
    }
    protected function scorerule_lol($position,$player_match_data){
        if ($position == 7) {
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
        }else{
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
        }
        foreach ($player_match_data as $key => $value) {
            if ($key == 'remain') {
                if ($player_match_data['is_win'] == 0) {
                    continue;
                }
            }else{
                $score_sum += $socre_rule[$key] * $value;
            }
        }

        return number_format($score_sum,1);
    }
    // 更新平均积分和kda
    public function update_player_data(){
    	$room_id = $_REQUEST['room_id'];
		$IMap['room_id'] = $Map['id'] = $room_id;
        $data = M('MatchRoomInfo')->field('match_team')->where($IMap)->find();// 获取赛事列表
        $project_id = M('MatchRoom')->where($Map)->getField('project_id');// 获取项目id
        $match_list = explode(',', $data['match_team']);
        $MatchList = M('MatchList');
        $teams = array();//存储该房间所有比赛的队伍id
        foreach ($match_list as $key => $value) {
            $_data = $MatchList->field('team_a,team_b')->where(array('id' =>$value))->find();
            $teams[] = $_data['team_a'];
            $teams[] = $_data['team_b'];
        }
        //获取该该房间所有球员包括工资
        $teams = array_values(array_unique($teams)); //去除重复的队伍
        $player_wcg = M('MatchPlayerWcg');
        if($project_id == 5){
            // lol
            $PlayerMatch = M('PlayerMatchDataLol');
        }elseif ($project_id == 6) {
            // dota2
            $PlayerMatch = M('PlayerMatchDataDota2');
        }
        $where = '1 != 1';
        foreach ($teams as $k => $v) {
            $where .=' or team_id ='.$v .' ' ;
        }
        $player = $player_wcg->field('id,average,KDA')->where($where)->select();
        foreach ($player as $k => $vs) {
        	$Maps['player_id'] = $vs['id']; // 球员的id
        	$match_data = $PlayerMatch->field('kill,death,assists,scores')->where($Maps)->select();
        	if (!$match_data) {
        		continue;
        	}
        	$all_avg = $this->array_avg($match_data);// 所有的数据平均值
			$Kda = number_format(($all_avg['kill'] +$all_avg['assists'])/$all_avg['death'],1);
			if (floor($all_avg['scores']) != $vs['average']) {
				M('MatchPlayerWcg')->where(array('id'=>$vs['id']))->setField('average',$all_avg['scores']);
			}
			if ($Kda != $vs['KDA']/10) {
				M('MatchPlayerWcg')->where(array('id'=>$vs['id']))->setField('KDA',$Kda*10);
			}
        }
        $this->msg(0,'更新成功');
    }
    // 求二维数组各个元素平均值
    protected function array_avg($array, $avgby = NULL) { 
        $array_avg = array (); 
        $number = count ( $array ); 
        foreach ( $array as $key => $value ) { 
            if ($avgby) { 
                $avg_key = $value[$avgby]; 
                $array_avg[$avg_key]['count'] ++; 
                foreach ( $value as $k => $v ) { 
                    $array_avg[$avg_key][$k] += $v; 
                } 
            } else { 
                foreach ( $value as $k => $v ) { 
                    $array_avg[$k] += $v; 
                } 
            } 
        } 
        $array = array (); 
        foreach ( $array_avg as $key => $value ) { 
            if ($avgby) { 
                foreach ( $value as $k => $v ) { 
                    $array[$key][$k] = $v / $value['count']; 
                } 
            } else { 
                $array[$key] = $value / $number; 
            } 
        } 
        return $array; 
    }

    //自动投注房间
    public function guess_auto(){
    	if(IS_POST){
    		sleep(3);
    		$room_id = I('room_id'); //需要投注房间的id
    		$room_guess_num = I('room_guess_num'); //房间的投注次数
    		$user_guess_num = I('user_guess_num'); //用户下注的次数
    		$cache_name = 'cache_user_all_'.$room_id.'_'.$room_guess_num;

    		if($room_guess_num >= 131){
    			$this->returnMsg(100,'没有这么多用户');
    		}

    		$UserUser = M('UserUser');
    		// $_arr = array();

    		$all_user = S($cache_name) ? S($cache_name) : array();
    		
    		$room_data = M('MatchRoom')->where(array('id' => $room_id))->find(); //获取房间的信息

			//获取一个不重复的uid
			$uid = rand(1,131);
			$j = 1;
			while (in_array($uid, $all_user)) {
				$uid = rand(1,131);
				$j++;
				if($j >= 130){
					$this->returnMsg(1,'已经没有可选用户,程序中止');
				}
			}
			$user_data = $UserUser->where(array('id' => $uid))->find();
			if(!$user_data){
				$this->returnMsg(1,'没有获取到用户的信息');
			}

			//获取推荐的阵容
			$t_url = 'http://api.aifamu.com/index.php?g=api&m=bet&a=recommendlineup';

			$post_data = $this->c_url($t_url,array('user_token' => $user_data['token'],'id' => $room_id,'lineup_id' => $room_data['lineup_id']));
			$_data = json_decode($post_data,true);

			if($_data['error'] != 0){
				$this->returnMsg(1,'推荐阵容错误,错误信息:'.$_data['msg']);
			}

			$g_data = array();
			$g_data['team_info'] = $_data['data']; //阵容
			$g_data['guess_num'] = $user_guess_num ? $user_guess_num : 1; //下注次数
			$g_data['user_token'] = $user_data['token'];
			$g_data['id'] = $room_id;
    		
			$g_url = 'http://api.aifamu.com/index.php?g=api&m=bet&a=guess';


			$p_json = $this->c_url($g_url,http_build_query($g_data));
			$r_data = json_decode($p_json,true);
			if($r_data['error'] == 0){
				$all_user[] = $user_data['id'];
				S($cache_name,$all_user,3600*12);
				$this->returnMsg(0,'投注成功,投注的用户是:'.$user_data['username']);
			}else{
				$all_user[] = $user_data['id'];
				S($cache_name,$all_user,3600*12);
				$this->returnMsg(1,'投注失败,错误原因:'.$r_data['msg']);
			}
    	}else{
    		$this->display();
    	}
    }

    protected function c_url($url,$tree,$request='POST'){
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        //设置请求的方式,GET或POST
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, $request );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        //请求的头信息
        if(!empty($header)){
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );   
        }
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $tree );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $res = curl_exec ( $ch );
        curl_close ( $ch );
        return $res;
    }
}