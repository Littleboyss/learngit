<?php
/**
 * @author wangh 2017.2.20
 */

class NoticeAction extends AdminAction{

	//列表
	public function index(){
		$UserNotice = M('UserNotice');
		import('ORG.Util.Page');
		$count = $UserNotice->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$data = $UserNotice->limit($page->firstRow . ',' . $page->listRows)->order('match_start_time desc')->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->display();	
	}
	//添加
	public function add(){
		$MatchRoom = M ('MatchRoom');
		$data = $MatchRoom->create();
		if ($data) {
			if ($data['type_id'] != 5) {
				unset($data['special_name']);
				unset($data['special_uid']);
			}
			if ($data['open_id'] != 2) {
				unset($data['open_num']);
			}
			if (!in_array($data['reward_id'],array(1,2,3,7))) {
				unset($data['prize_num']);
				unset($data['reward_num']);
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
				$info['room_id'] = $result;
				$info['match_team'] = implode(',',$_POST['match_team']); 
				$res = M('MatchRoomInfo')->add($info);
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('add'));
			}
		} else {
			$this->getmatchroomtype();
			$match_list = M('match_list')->field('t2.`short_name` as a_name,t2.img as a_img,t3.`short_name` as b_name,t3.img as b_img,t1.id,t1.match_time')->join('as t1 left join '.c('DB_PREFIX').'match_team as t2 on t1.team_a = t2.id  left join '.c('DB_PREFIX').'match_team as t3 on t1.team_b=t3.id')->where('t1.match_time > '.time())->select();
			$reward = M('RewardRule')->field('id,name')->select();
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
		$data = $MatchRoom->create();
		if ($data) {
			if ($data['type_id'] != 5) {
				unset($data['special_name']);
				unset($data['special_uid']);
			}
			if ($data['open_id'] != 2) {
				unset($data['open_num']);
			}
			if (!in_array($data['reward_id'],array(1,2,3,7))) {
				unset($data['prize_num']);
				unset($data['reward_num']);
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
			$res = M('MatchRoomInfo')->where('room_id = '.$data['id'])->setField('match_team',$match_team);
			$result = $MatchRoom->save($data);
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

			$reward = M('RewardRule')->field('id,name')->select();
			$project = M('MatchProject')->field('id,name')->select();
			$match_list = M('match_list')->field('t2.`short_name` as a_name,t2.img as a_img,t3.`short_name` as b_name,t3.img as b_img,t1.id,t1.match_time')->join('as t1 left join '.c('DB_PREFIX').'match_team as t2 on t1.team_a = t2.id  left join '.c('DB_PREFIX').'match_team as t3 on t1.team_b=t3.id')->where('t1.match_time > '.time())->select();
			foreach ($match_list as $key => $value) {
                $day = date('Y-m-d',$value['match_time']);
                $value['match_time'] = date('Y-m-d H:i:s',$value['match_time']);
                $list[$day][]=$value; 
            }
            $match = M('MatchRoomInfo')->where('room_id = '.$id)->find();
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
		if ($price_type != 1) {
			$prize_type = 3;
		}
		//
		// 后台发送消息通知
		// 1、找到中奖的用户消息
		$total  =  M('UserGuessRecord')->where('match_status = 2 and settlement_status = 1 and room_id ='.$_GET['match_id'].' and is_reward > 0 ')->count();
		$data = M('UserGuessRecord')->where('match_status = 2 and settlement_status = 1 and room_id ='.$_GET['match_id'].' and is_reward > 0 ')->limit($start,50)->select();
		$MatchRoom->where($where)->setField('settlement_status',2);
		 //var_dump($total);exit;
		if ($data) {
			if (($start+1) > $total) {
				$this->msg(0,'结算完毕',$data[]);
			}
			// 2、入竞猜消息表	
			foreach ($data as $key => $value) {
				if ($value['match_status'] == 2 && $value['settlement_status'] == 1 ) {
					M('UserGuessRecord')->where('id ='.$value['id'])->setField('match_status',3);
					M('UserGuessRecord')->where('id ='.$value['id'])->setField('settlement_status',2);
					$table_name = $this->get_hash_table('UserBetGess',$value['uid']);
					$UserBetGess = M($table_name);
					$msg['room_id']=$value['room_id'];
					$msg['uid']=$value['uid'];
					$msg['nums']=$value['is_reward']*$value['guess_num'];
					$msg['lineup_score']=$value['lineup_score'];// 阵容积分
					$msg['type']=$prize_type; // 奖品类型1门票3木头
					$msg['ranking']=$value['ranking']; // 最终排名
					$msg['addtime']=time();
					$UserBetGess->add($msg);
				}
			}
			$precess = floor(($start + 1) / $total *100);
			$msgs = "完成进度" . $precess . "%";
			$this->msg(0,$msgs,$datas['']);
		}else{
			$this->msg(1,'结算完成');;
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
					$this->insert_account(13,$prize_type,$value['uid'],$value['guess_num']*$roomdetail['price'],true);
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

		$match_list = M('match_list')->field('t2.`short_name` as a_name,t2.img as a_img,t3.`short_name` as b_name,t3.img as b_img,t1.id,t1.match_time')->join('as t1 left join '.c('DB_PREFIX').'match_team as t2 on t1.team_a = t2.id  left join '.c('DB_PREFIX').'match_team as t3 on t1.team_b=t3.id')->where('t1.project_id = '.$project_id.' and t1.match_time > '.time())->select();
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
	
}