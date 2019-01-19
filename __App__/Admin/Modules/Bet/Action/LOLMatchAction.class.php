<?php
/**
 * @author chengy 2017.5.22
 */
class LOLMatchAction extends AdminAction{
	
	public function getteamjson(){ // 返回json 提供前端做项目塞选
		$Map = $this->_post();
		$team = M('MatchTeam')->field('id,name')->where($Map)->order('name asc')->select();
		echo json_encode($team);die;
	}


	protected function getmatchtype(){
		$match_type = $this->getcache('match_type_data');
		// print_r($match_type);
		$this->assign('match_type',$match_type);
	}

	//赛程列表
	public function index(){
		$MatchList = M('MatchList');
		$where ='project_id = 5';
		$team_id = $_REQUEST['team_id'];
		$id = $_REQUEST['id'];
		if($team_id){
			$where .=' and (team_a = '.$team_id.' or team_b = '.$team_id.'  )';
		}
		import('ORG.Util.Page');
		$count = $MatchList->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$data = $MatchList->where($where)->order('match_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("team_id", $team_id);
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->getmatchtype();
		$this->getteam(array(5));
		$this->getproject(true);
		$this->display();	
	}
	//赛程添加
	public function add(){
		$MatchList = M ('MatchList');
		$data = $MatchList->create();
		if ($data) {
			$data['add_time'] = time();
			$data['match_time'] = strtotime($data['match_time']);
			$result = $MatchList->add($data);
			if($result){
				$sc=new MatchAction();
				$sc->add_match_player_info($result,5);
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('index'));
			}
		} else {
			$this->getmatchtype();
			$this->getproject(true);
			$this->display();
		}
	}
	//赛程修改
	public function edit(){
		$MatchList = M('MatchList');
		$data = $MatchList->create();
		if ($data) {
			$data['match_time'] = strtotime($data['match_time']);
			$result = $MatchList->save($data);
			if($result){
				$season = date('Y',$data['match_time']);
				$date = date('m/d',$data['match_time']);
				// 修改成功后更改比赛的数据
				$player_data =  M('PlayerMatchDataLol');
				$match_data = $player_data->where('match_id = '.$data['id'])->select();
				if ($match_data) {
					$player = M('MatchPlayerWcg')->field('id,position,team_id')->where('project_id = 5')->select();
					foreach ($player as $k => $v) {
						$map_po[$v['id']] = $v['position'];
						$map_team[$v['id']] = $v['team_id'];
					}
					foreach ($match_data as $key => $value) {
						if ($map_team[$value['player_id']] == $data['team_a']) {
			                $save_data[$key]['score'] = $data['score_a'].':'.$data['score_b'];
			            }else{
			                $save_data[$key]['score'] = $data['score_b'].':'.$data['score_a'];
			            }
			            if ( ($save_data[$key]['score'] == '2:0' && $data['home_id'] != 2 )|| $save_data[$key]['score'] == '3:1' ) {
			                $save_data[$key]['remain'] = 1;
			            }elseif($save_data[$key]['score'] == '3:0'){
			                $save_data[$key]['remain'] = 2;
			            }else{
			                $save_data[$key]['remain'] =0;
			                $save_data[$key]['remain'] =0;
			            }
		                $save_data[$key]['season'] = $season;
		                $save_data[$key]['date'] = $date;
		                $save_data[$key]['addtime'] = time();
						if ( $map_po[$value['player_id']]== 6) {
							// 给队伍添加数据
				            $win = array('1:0','2:0','2:1','3:0','3:1','3:2');
				            if (in_array($save_data[$key]['score'],$win)) {
				                $save_data[$key]['is_win'] = 1;
				            }else{
				                $save_data[$key]['is_win'] = 0;
				            }
				            $value['is_win'] = $save_data[$key]['is_win'];
				            $value['times'] = $data['score_a']+$data['score_b'];
				            $value['remain'] = $save_data[$key]['remain'];
				            $scores = $this->scorerule_lol($value['player_id'],$value);
			                $save_data[$key]['scores'] = $scores*10;
				            $player_data->where('id = '.$value['id'])->save($save_data[$key]);
						}else{
							$value['is_win'] = $save_data[$key]['is_win'];
				            $value['remain'] = $save_data[$key]['remain'];
				            $scores = $this->scorerule_lol($value['player_id'],$value);
			                $save_data[$key]['scores'] = $scores*10;
							$player_data->where('id = '.$value['id'])->save($save_data[$key]);
						} 
					}
				}
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
			$data = $MatchList->where($Map)->find();
			$this->assign('data',$data);
			$this->getproject(true);
			$this->getmatchtype();
			$this->getteam();
			$this->display();
		}
	}
	//删除
	public function del(){
		$MatchList = M ('MatchList');
		$PlayerMatchData = M('PlayerMatchDataLol');
		$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
		if ($id <= 0) {
			$this->error ('参数错误',U('index'));
		}
		$Map['id'] = $id;		
		$result = $MatchList->where($Map)->Delete();
		if($result){
			// 判断match_data里面有没有数据
			$PlayerMatchData->where('match_id = '.$id)->delete();
			$this->success('成功',U('index'));
		}else{
			$this->error('失败',U('index'));
		}
	}
	// 添加比赛数据
	public function add_match_data(){
		$MatchList = M('MatchList');
		$wcg = M('MatchPlayerWcg');
		$PlayerMatchDataLol = M('PlayerMatchDataLol');
		$data = $this->_post();
		if ($data) {
			$Map['match_id'] = $data['match_id'];
			$Map['player_id'] = $data['player_id'];
			$match_data = M('PlayerMatchDataLol')->where($Map)->find();
			if ($data['tower'] != 0 ) {
				$data['is_join'] = 1;
			}
			//$data['score'] = str_replace('：',':',$data['score']);
			$data['scores'] = $this->scorerule_lol($Map['player_id'],$data)*10;
			$salary = $wcg->where('id = '. $data['player_id'])->getField('salary');
			$salary = $salary + ($data['scores']/10 - $salary)*0.15;
			if ($salary < 10 ) {
				$salary = 10;
			}
			if ($salary > 50 ) {
				$salary = 50;
			}
			$wcg->where('id = '. $data['player_id'])->setField('salary',$salary);
			if ($match_data) {
				$data['id'] = $match_data['id'];
				$res = M('PlayerMatchDataLol')->where('id = '.$data['id'])->save($data);
			}else{
				$data['addtime'] = time();
				$data['season'] = '2017';
				$res = M('PlayerMatchDataLol')->add($data);
			}
			if ($res) {
				$this->success('数据更新成功',U('add_match_data',array('id'=>$Map['match_id'])));exit;
			}else{
				$this->error('数据更新失败',U('add_match_data',array('id'=>$Map['match_id'])));exit;
			}
			dump($res);exit;
		}else{
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $MatchList->where($Map)->find();
			if ($data) {
				$team_a = $data['team_a'];
				$team_b = $data['team_b'];
				$playerlist['list'] = M('MatchPlayerWcg')->where("team_id =  $team_a or team_id = $team_b")->select(); 
				$playerlist['match_id'] = $id;
			}
			$this->assign('playerlist',$playerlist);// 球员列表
			$this->display();
		}
	}
	// 查询该场比赛的球员数据
	public function get_player_data(){
		$post = $this->_post();
		$Map['match_id'] = $post['match_id'];
		$Map['player_id'] = $post['player_id'];
		$team = M('PlayerMatchDataLol')->where($Map)->find();
		if (!$team) {
			$team = array('kill'=>'','death'=>'','assists'=>'','jungle'=>'','ten_kill'=>'','score'=>'','tower'=>'','dragons'=>'','barons'=>'','times'=>'','is_join'=>'','first_blood'=>'','is_win'=>'','is_fast'=>'','remain'=>'');
		}
		$player = M('MatchPlayerWcg')->where(array('id'=>$post['player_id']))->find();// 位置
		$team['position'] =$player['position'];// 球员位置
		$match_info = M('match_list')->where(array('id'=>$post['match_id']))->find();
		$team['date'] = date('m/d',$match_info['match_time']);// 比赛时间
		if ($player['team_id'] != $match_info['team_a']) {
			$team_id = $match_info['team_a'];// 比赛对手id
		}else{
			$team_id = $match_info['team_b'];// 比赛对手id
		}
		$team['opponents'] = M('MatchTeam')->where('id = '.$team_id)->getField('name');// 对手名称
		echo json_encode($team);die;
	}
	// 积分规则
    // $player_id 选手的id
    // $player_match_data选手比赛的得分数据
    // @return 返回积分
    protected function scorerule_lol($player_id,$player_match_data){
        $score_sum = 0;
        $position = M('MatchPlayerWcg')->where(array('id'=>$player_id))->getField('position');
        //var_dump($position);exit;
        if ($position == 6) {
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
}