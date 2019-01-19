<?php
/**
 * @author chengy 2017.5.22
 */
class DOTA2MatchAction extends AdminAction{
	
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
		$where ='project_id = 6';
		$team_id = $_REQUEST['team_id'];
		$id = $_REQUEST['id'];
		if($team_id){
			$where .=' and (team_a = '.$team_id.' or team_b = '.$team_id.'  )';
		}
		import('ORG.Util.Page');
		$count = $MatchList->where($where)->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$data = $MatchList->where($where)->order('match_time  desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("team_id", $team_id);
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->getmatchtype();
		$this->getteam(array(6));
		$this->getproject(true);
		$this->display();	
	}
	//赛程添加
	public function add(){
		$MatchList = M ('MatchList');
		$data = $MatchList->create();
		if ($data) {
			$data['only_id'] = implode(',',$data['only_id']);
			$data['add_time'] = time();
			$data['match_time'] = strtotime($data['match_time']);
			//var_dump($data);exit;
			$result = $MatchList->add($data);
			if($result){
				$sc=new MatchAction();
				$sc->add_match_player_info($result,6);
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
			$data['only_id'] = implode(',',$data['only_id']);
			$data['match_time'] = strtotime($data['match_time']);
			$result = $MatchList->save($data);
			if($result){
				$season = date('Y',$data['match_time']);
				$date = date('m/d',$data['match_time']);
				// 修改成功后更改比赛的数据
				$player_data =  M('PlayerMatchDataDota2');
				$match_data = $player_data->where('match_id = '.$data['id'])->select();
				if ($match_data) {
					$player = M('MatchPlayerWcg')->field('id,position,team_id')->where('project_id = 6')->select();
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
				            $value['remain'] = $save_data[$key]['remain'];
				            $value['times'] = $data['score_a']+$data['score_b'];
				            $scores = $this->scorerule_dota2($value['player_id'],$value);
			                $save_data[$key]['scores'] = $scores*10;
				            $player_data->where('id = '.$value['id'])->save($save_data[$key]);
						}else{
							$value['is_win'] = $save_data[$key]['is_win'];
				            $value['remain'] = $save_data[$key]['remain'];
				            $scores = $this->scorerule_dota2($value['player_id'],$value);
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
	//删除,最后做
	public function del(){
		$MatchList = M ('MatchList');
		$PlayerMatchData = M('PlayerMatchDataDota2');
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
		$PlayerMatchDataDota2 = M('PlayerMatchDataDota2');
		$wcg = M('MatchPlayerWcg');
		$data = $this->_post();
		if ($data) {
			$Map['match_id'] = $data['match_id'];
			$Map['player_id'] = $data['player_id'];
			$match_data = $PlayerMatchDataDota2->where($Map)->find();
			if ($data['tower'] != 0 ) {
				$data['is_join'] = 1;
			}
			//$data['score'] = str_replace('：',':',$data['score']);
			$data['scores'] = $this->scorerule_dota2($Map['player_id'],$data)*10;
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
				$res = M('PlayerMatchDataDota2')->where('id = '.$data['id'])->save($data);
			}else{
				$data['addtime'] = time();
				$data['season'] = date('Y',time());
				$res = M('PlayerMatchDataDota2')->add($data);
			}
			if ($res) {
				$this->success ('数据更新成功',U('add_match_data',array('id'=>$Map['match_id'])));exit;
			}else{
				$this->error ('数据更新失败',U('add_match_data',array('id'=>$Map['match_id'])));exit;
			}
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
		$team = M('PlayerMatchDataDota2')->where($Map)->find();
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
    public function get_match_data(){
    	$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0; // 比赛id
    	$wcg = M('MatchPlayerWcg');
    	$PlayerMatchData = M('PlayerMatchDataDota2');
		$match_data = M('MatchList')->where('id = '.$id)->find();
		$all_player = M('MatchPlayerWcg')->field('id,only_id,team_id')->where('project_id = 6')->select();
		foreach ($all_player as $k2 => $v2) {
		    $only_ids[] = $v2['only_id'];
		    $map[$v2['only_id']] = $v2['id'];
		    $map_team[$v2['id']] = $v2['team_id'];
		}
    	$only_id = $match_data['only_id'];
	    $match_id = explode(',',$only_id);
	    foreach ($match_id as $k => $v) {
	        $datas = file_get_contents('https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id='.$v.'&key=094A15ACFD6CA245D0FC24A0B6378D96');
	        $data = json_decode($datas,true);
	        if (!$data['result']) {
	        	$this->error ('暂时无法获取数据',U('index'));die;
	        }
	        if ($k == 0) {
	            $match_time = $data['result']['start_time']; // 添加比赛开始时间
	        }
	        $team_a = $map[$data['result']['dire_team_id']];
	        $team_b = $map[$data['result']['radiant_team_id']];  
	        // 获取一血和肉山数据
	        $other_data = file_get_contents('http://test.neverwinter.sg/v2/risewinter/matches/first_info/'.$v.'?token=611be107c14b4459c2b2bdfcb060f24a');
	        $other_datas = json_decode($other_data,true);
	        //var_dump($other_datas);exit;
	        $match[$k]['team'][$team_a]['first_kill'] = 0;
	        $match[$k]['team'][$team_b]['first_kill'] = 0;
	        if (!$other_datas) {
	        	echo '浮冬无数据 ';
	        }else{
	        	if ($other_datas['first_blood_info']['is_radiant_fb']) {
	        		$match[$k]['team'][$team_b]['first_kill'] = 1;
	        	}else{
	        		$match[$k]['team'][$team_a]['first_kill'] = 1;
	        	}
	        	$match[$k]['team'][$team_b]['roshan'] = $other_datas['roshan_info']['radiant_roshan_count'];
	        	$match[$k]['team'][$team_a]['roshan'] = $other_datas['roshan_info']['dire_roshan_count'];
	        }
            if ($data['result']['duration']<1800) {
            	if ($data['result']['radiant_win']) {
	                $match[$k]['team'][$team_b]['is_fast'] = 1;
	                $match[$k]['team'][$team_a]['is_fast'] = 0;
            	}else{
            		$match[$k]['team'][$team_b]['is_fast'] = 0;
	                $match[$k]['team'][$team_a]['is_fast'] = 1;
            	}
            }
	        
	        if ($data['result']['tower_status_radiant'] == 0) {
	        	$match[$k]['team'][$team_a]['tower'] = 11;
	        }else{
	        	$match[$k]['team'][$team_a]['tower'] = substr_count(decbin($data['result']['tower_status_radiant']),'0'); // 先转成二进制，数零的个数
	        }
	        if ($data['result']['tower_status_dire'] == 0) {
	        	$match[$k]['team'][$team_b]['tower'] = 11;
	        }else{
	        	$match[$k]['team'][$team_b]['tower'] = substr_count(decbin($data['result']['tower_status_dire']),'0'); // 先转成二进制，数零的个数
	        }
	        $match[$k]['team'][$team_a]['opp'] = $data['result']['radiant_name'];
	        $match[$k]['team'][$team_b]['opp'] = $data['result']['dire_name'];
	        //echo decbin($data['result']['tower_status_radiant']);
	        foreach ($data['result']['players'] as $k1 => $v1) {
	            @$player_id = $map[$v1['account_id']]?$map[$v1['account_id']] : false;
	            if (!@$map[$v1['account_id']]) {
	                continue;
	            }
	            if ($v1['player_slot'] > 5) {
	                $match[$k]['player'][$player_id]['team_id'] = $team_a;// 队伍id
	                $match[$k]['player'][$player_id]['opp'] = $match[$k]['team'][$team_a]['opp'];// 对手
	            }else{
	                $match[$k]['player'][$player_id]['team_id'] = $team_b;// 队伍id
	                $match[$k]['player'][$player_id]['opp'] = $match[$k]['team'][$team_b]['opp'];// 对手
	            }
	            $match[$k]['player'][$player_id]['kills'] = $v1['kills'];    // 击杀
	            $match[$k]['player'][$player_id]['deaths'] = $v1['deaths'];  // 死亡
	            $match[$k]['player'][$player_id]['assists'] = $v1['assists']; // 助攻
	            $match[$k]['player'][$player_id]['last_hits'] = $v1['last_hits']; // 补刀
	            $match[$k]['player'][$player_id]['times'] = 1; // 上场场数
	            $match[$k]['player'][$player_id]['ten_kill'] = 0;
	            $flag = false;
	            if ($v1['kills'] >= 10) {
	                $flag = true;
	            }
	            if ($v1['assists'] >= 10) {
	            	$flag = true;
	            }
	            if ($flag) {
	                $match[$k]['player'][$player_id]['ten_kill']++; // 十杀或助攻
	            }
	        }
	        
	    }
	    $teams = array();  
	    foreach ($match as $k3 => $v3) {
	        foreach ($v3['team'] as $k4 => $v4) {
	            if ($map_team[$k4] == $match_data['team_a']) {
	                $score = $match_data['score_a'].':'.$match_data['score_b'];
	            }else{
	                $score = $match_data['score_b'].':'.$match_data['score_a'];
	            }
	            if ( ($score == '2:0' && $match_data['home_id'] != 2 )|| $score == '3:1' ) {
	                $remain = 1;
	            }elseif($score == '3:0'){
	                $remain = 2;
	            }else{
	                $remain =0;
	            }
	            $win = array('1:0','2:0','2:1','3:0','3:1','3:2');
	            if (in_array($score,$win)) {
	                $is_win = 1;
	            }else{
	                $is_win = 0;
	            }
	            $teams[$k4]['id'] = $k4;
	            $teams[$k4]['tower'] += $v4['tower'];
	            $teams[$k4]['is_fast'] += $v4['is_fast'];
	            $teams[$k4]['first_blood'] += $v4['first_kill'];
	            $teams[$k4]['barons'] += $v4['roshan'];
	            $teams[$k4]['remain'] = $remain;
	            $teams[$k4]['is_win'] = $is_win;
	            $scores = $this->scorerule_dota2($k4,$teams[$k4]);
	            $teams[$k4]['score'] = $score;
	            $teams[$k4]['opp'] = $v4['opp'];
	            $teams[$k4]['scores'] = $scores*10;
	        }
	        foreach ($v3['player'] as $k5 => $v5) {
	            if ($map_team[$v5['team_id']] == $match_data['team_a']) {
	                $score = $match_data['score_a'].':'.$match_data['score_b'];
	            }else{
	                $score = $match_data['score_b'].':'.$match_data['score_a'];
	            }
	            if ( ($score == '2:0' && $match_data['home_id'] != 2 )|| $score == '3:1' ) {
	                $remain = 1;
	            }elseif($score == '3:0'){
	                $remain = 2;
	            }else{
	                $remain =0;
	            }
	            $times ++;
	            $player[$k5]['id'] = $k5;
	            $player[$k5]['kill'] += $v5['kills'];
	            $player[$k5]['death'] += $v5['deaths'];
	            $player[$k5]['assists'] += $v5['assists'];
	            $player[$k5]['jungle'] += $v5['last_hits'];
	            $player[$k5]['ten_kill'] += $v5['ten_kill'];
	            $player[$k5]['times'] += $v5['times'];
	            $player[$k5]['remain'] = $remain;
	            $scores = $this->scorerule_dota2($k5,$player[$k5]);
	            $player[$k5]['scores'] = $scores*10;
	            $player[$k5]['opp'] = $v5['opp'];
	            $player[$k5]['score'] = $score;
	        }    
	    }
	    $date = date('m/d',$match_time);
	    $season = date('Y',$match_time);
	    $match_id = $match_data['id'];
	    $addtime = time();
	    foreach ($player as $k6 => $v6) {
			$salary = $wcg->where('id = '. $v6['id'])->getField('salary');
			$salary = $salary + ($v6['scores']/10 - $salary)*0.15;
			if ($salary < 10 ) {
				$salary = 10;
			}
			if ($salary > 50 ) {
				$salary = 50;
			}
			$wcg->where('id = '. $v6['id'])->setField('salary',$salary);
			$v6['player_id'] = $v6['id'];
			$v6['is_join'] = 1;
			$v6['match_id'] = $match_id;
			$v6['season'] = $season;
			$v6['date'] = $date;
			$v6['addtime'] = $addtime;
	      	unset($v6['id']);
	      	$check = $PlayerMatchData->where("match_id = $match_id and player_id = ".$v6['player_id'])->find();
	      	if ($check) {
	      		$res3 = $PlayerMatchData->where('id ='.$check['id'])->save($v6);
	      	}else{
	        	$res3 = $PlayerMatchData->add($v6);
	      	}
	        if ($res3) {
	            echo '数据写入成功 ';
	        }else{
	            echo '数据写入失败 ';
	        }
	    }
	    foreach ($teams as $k7 => $v7) {
	    	$salary = $wcg->where('id = '. $v7['id'])->getField('salary');
			$salary = $salary + ($v7['scores']/10 - $salary)*0.15;
			if ($salary < 10 ) {
				$salary = 10;
			}
			if ($salary > 50 ) {
				$salary = 50;
			}
			$wcg->where('id = '. $v7['id'])->setField('salary',$salary);
	        $v7['player_id'] = $v7['id'];
	        $v7['match_id'] = $match_id;
	        $v7['season'] = $season;
			$v7['date'] = $date;
			$v7['addtime'] = $addtime;
	      	unset($v7['id']);
	        $check = $PlayerMatchData->where("match_id = $match_id and player_id = ".$v7['player_id'])->find();
	      	if ($check) {
	      		$res4 = $PlayerMatchData->where('id ='.$check['id'])->save($v7);
	      	}else{
	        	$res4 = $PlayerMatchData->add($v7);
	      	}
	      	if ($res4) {
	            echo '数据写入成功 ';
	        }else{
	            echo '数据写入失败 ';
	        }
	    }
    }
}