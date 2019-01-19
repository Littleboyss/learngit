<?php
/**
 * @author wangh 2017.2.20
 */

class TeamAction extends AdminAction{

	//获取比赛的赛区和
	protected function match_loca_uoion(){
		$this->assign('location',C('TEAM_LOCATION'));
		$this->assign('union',C('TEAM_UNION'));
	}
	public function getteamjson(){ // 返回json 提供前端做项目塞选
		$Map = $this->_post();
		$team = M('MatchTeam')->field('id,name')->where($Map)->order('name asc')->select();
		echo json_encode($team);die;
	}

	//列表
	public function index(){
		$MatchTeam = M('MatchTeam');
		import('ORG.Util.Page');
		$Map = $_REQUEST;
		
		if($Map['team_id'] != ''){
			$Map['id'] = $Map['team_id'];
		}
		if($Map['name'] == ''){
			unset($Map['name']);
		}else{
			$Map['name'] = array('like',"%".$Map['name']."%");
		}
		if($Map['id'] == ''){
			unset($Map['id']);
		}
		if($Map['project_id'] == ''){
			unset($Map['project_id']);
		}
		$count = $MatchTeam->where($Map)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $MatchTeam->where($Map)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$project_id = $_REQUEST['project_id'];
		$team = $MatchTeam->order('name asc')->select();
		$this->assign("show", $show);
		$this->assign("team", $team);
		$this->assign("id", $Map['id']);
		$this->assign ('data', $data );
		$this->assign ('project_id',$project_id );
		$this->match_loca_uoion();
		$this->getproject(true);

		$this->display();	
	}
	//添加
	public function add(){
		$MatchTeam = M('MatchTeam');
		if (IS_POST) {
			$data = $MatchTeam->create();
			if ($data) {
				$data['add_time'] = time();
				$result = $MatchTeam->add($data);
				if($result){
					$this->success('成功',U('index'));
				}else{
					$this->error('失败',U('index'));
				}
			}
		} else {
			$this->match_loca_uoion();
			$this->getproject();
			$this->display();
		}
	}
	//修改
	public function edit(){
		$MatchTeam = M('MatchTeam');
		$data = $MatchTeam->create();
		if ($data) {
			$MatchTeam->save( $data );
			$this->success ('成功',U('index'));
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('index'));
			}
			$this->match_loca_uoion();
			$this->getproject();
			$Map['id'] = $id;
			$data = $MatchTeam->where($Map)->find();
			$this->assign('data',$data);
			$this->display ();
		}
	}
	//删除,最后做
	public function del(){
		$MatchTeam = M('MatchTeam');
		$MatchPlayer = M('MatchPlayer');
		$MatchPlayerWcg = M('MatchPlayerWcg');
		$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
		if ($id <= 0) {
			$this->error ('参数错误',U('index'));
		}
		$Map['id'] = $id;
		$result = $MatchTeam->where($Map)->Delete();
		if($result){
			$this->success('成功',U('index'));
		}else{
			$this->error('失败',U('index'));
		}
	}
}