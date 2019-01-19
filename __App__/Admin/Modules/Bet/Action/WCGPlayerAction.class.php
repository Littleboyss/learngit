<?php
/**
 * @author wangh 2017.2.20
 */
class WCGPlayerAction extends AdminAction{

	private $wcg_project = array(5,6); //电竞项目的id,统一配置
	public function getteamjson(){ // 返回json 提供前端做项目塞选
		$Map = $this->_post();
		$team = M('MatchTeam')->field('id,name')->where($Map)->order('name asc')->select();
		echo json_encode($team);die;
	}
	//选手的国籍
	protected function getplayercountry(){
		$playercountry = $this->getcache('playercountry_data');
		$this->assign('playercountry',$playercountry);
	}
	//列表
	public function index(){
		$MatchPlayerWcg = M('MatchPlayerWcg');
		import('ORG.Util.Page');
		$Map['team_id'] = I('team_id');
		$name =  I('name');
		$Map['project_id'] = I('project_id');
		$Map['only_id'] = I('only_id');
		$sql ='1=1 ';
		if( !empty(I('team_id'))){
			$sql .=" and team_id = ".$Map['team_id'];
			$this->assign('team_id',$Map['team_id']);
		}
		if ($name) {
			$sql .=" and name like '%$name%'";
			$this->assign('name',$name);
		}
		if($Map['project_id']){
			$sql .=" and project_id = ".$Map['project_id'];
			$this->assign('project_id',$Map['project_id']);
		}
		if($Map['only_id']){
			$sql .=" and only_id = ".$Map['only_id'];
			$this->assign('only_id',$Map['only_id']);
		}
		$count = $MatchPlayerWcg->where($sql)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $MatchPlayerWcg->where($sql)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$sqls = 'project_id = 5 or project_id = 6';
		if ($Map['project_id']) {
			$sqls = 'project_id = '.$Map['project_id'];
		}
		$team = M('MatchTeam')->field('id,name')->where($sqls)->order('name asc')->select();
		foreach ($team as $key => $value) {
			$teams[$value['id']] = $value;
		}
		$this->assign("show", $show);
		$this->assign("team", $teams);
		$this->assign ('data', $data );
		$this->getproject(true);
		$this->display();
	}
	//添加
	public function add(){
		$MatchPlayerWcg = M('MatchPlayerWcg');
		$data = $MatchPlayerWcg->create();
		if ($data) {
			$data['add_time'] = time();
			// $data['position'] = implode(',', $data['position']);
			$result = $MatchPlayerWcg->add($data);
			if($result){
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('index'));
			}
		} else {
			$team = M('MatchTeam')->field('id,name')->where('project_id = 5 or project_id = 6')->order('name asc')->select();
			foreach ($team as $key => $value) {
				$teams[$value['id']] = $value;
			}
			$this->assign("team", $teams);
			$this->getproject();
			$this->display();
		}
	}
	//修改
	public function edit(){
		$MatchPlayerWcg = M('MatchPlayerWcg');
		if ($MatchPlayerWcg->create()) {
			$data = $MatchPlayerWcg->create();
			// $data['position'] = implode(',', $data['position']);
			$result = $MatchPlayerWcg->save($data);
			if($result){
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('index'));
			}
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $MatchPlayerWcg->where($Map)->find();
			$team = M('MatchTeam')->field('id,name')->where('project_id = '.$data['project_id'])->order('name asc')->select();
			foreach ($team as $key => $value) {
				$teams[$value['id']] = $value;
			}
			$this->assign("team", $teams);
			$this->getproject();
			$this->assign('data',$data);
			$this->display();
		}
	}
	//删除,最后做
	public function del(){
		die;
	}
}