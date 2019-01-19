<?php
/**
 * @author wangh 2017.2.20
 */

class MatchAction extends AdminAction{

	public function getteamjson(){ // 返回json 提供前端做项目塞选
		$Map = $this->_post();
		$team = M('MatchTeam')->where($Map)->getField('id,name');
		echo json_encode($team);die;
	}

	protected function getmatchtype(){
		$match_type = $this->getcache('match_type_data');
		// print_r($match_type);
		$this->assign('match_type',$match_type);
	}


	public function add_match_player_info($match_id,$project_id){
		if(!is_numeric($match_id)){
			exit('参数错误');
		}
		$data = M('MatchList')->where('id='.$match_id)->find();
		if(!$data){
			exit('没有查询到比赛信息');
		}
		if ($project_id == 4) {
			$matchPlay = M('MatchPlayer');
			$Model = M('PlayerMatchData');
			$addtime = 'add_time';
		}elseif ($project_id == 5) {
			$matchPlay = M('MatchPlayerWcg');
			$Model = M('PlayerMatchDataLol');
			$addtime = 'addtime';
		}elseif ($project_id == 6) {
			$matchPlay = M('MatchPlayerWcg');
			$Model = M('PlayerMatchDataDota2');
			$addtime = 'addtime';
		}
		$players = $matchPlay->where('team_id='.$data['team_a'] . ' or team_id='.$data['team_b'])->select();
		if(!$players){
			exit('没有查询到球员信息');
		}
		foreach ($players as $key => $value) {
			$result = $Model->where(array('player_id' => $value['id'],'match_id' => $match_id))->find();
			if(!$result){
				$data_['player_id'] = $value['id']; //球员的id
				$data_['match_id'] = $match_id;//比赛的id
				$data_['match_time'] = $data['match_time']; //比赛时间
				$data_[$addtime] = $data['match_time'];
				$data_['season'] = date('Y',$data['match_time']);
				$match_info = M('match_list')->where(array('id'=>$match_id))->find();
				if ($project_id != 4) {
					if ( $value['team_id'] != $match_info['team_a']) {
						$team_id = $match_info['team_a'];// 比赛对手id
					}else{
						$team_id = $match_info['team_b'];// 比赛对手id
					}
					$data_['opp'] = M('MatchTeam')->where('id = '.$team_id)->getField('name');// 对手名称
					$data_['date'] = date('m/d',$data['match_time']);
				}
				$ss = $Model->add($data_);
			}
		}
	}



	//赛程列表
	public function index(){
		$MatchList = M('MatchList');
		import('ORG.Util.Page');
		$count = $MatchList->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$data = $MatchList->order("id desc")->where('project_id=4')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->getmatchtype();
		$this->getteam();
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
				$this->add_match_player_info($result,4);
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
		$PlayerMatchData = M('PlayerMatchData');
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

	//赛事列表
	public function match(){
		$MatchType = M('MatchType');
		import('ORG.Util.Page');
		$count = $MatchType->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $MatchType->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->display();	
	}


	//赛事添加
	public function matchadd(){
		$MatchType = M('MatchType');
		$data = $MatchType->create();
		if ($data) {
			$data['add_time'] = time();
			$result = $MatchType->add($data);
			if($result){
				$this->success('添加成功',U('match'));
			}else{
				$this->error('添加失败');
			}
		} else {
			$this->display();
		}
	}

	//赛程修改
	public function matchedit(){
		$MatchType = M('MatchType');
		$data = $MatchType->create();
		if ($data) {
			// $data['match_time'] = strtotime($data['match_time']);
			$result = $MatchType->save($data);
			if($result){
				$this->success('修改成功',U('match'));
			}else{
				$this->error('修改失败');
			}
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('match'));
			}
			$Map['id'] = $id;
			$data = $MatchType->where($Map)->find();
			$this->assign('data',$data);
			$this->getproject(true);
			$this->getmatchtype();
			// $this->getteam();
			$this->display();
		}
	}


}