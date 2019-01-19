<?php
/**
 * @author wangh 2017.2.20
 */

class ProjectAction extends AdminAction{
	//列表
	public function index(){
		$MatchProject = M('MatchProject');
		import('ORG.Util.Page');
		$count = $MatchProject->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $MatchProject->order("sort ASC")->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->display();	
	}
	//添加
	public function add(){
		$MatchProject = M ( 'MatchProject' );
		$data = $MatchProject->create();
		if ($data) {
			$data['add_time'] = time();
			$result = $MatchProject->add($data);
			if($result){
				$this->clearcache('project_data');
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('index'));
			}
		} else {
			$this->display();
		}
	}
	//修改
	public function edit(){
		$MatchProject = M('MatchProject');
		$data = $MatchProject->create();
		if ($data) {
			$result = $MatchProject->save ( $data );
			if($result){
				$this->clearcache('project_data');
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
			$data = $MatchProject->where($Map)->find();
			$this->assign('data',$data);
			$this->display();
		}
	}
	//删除,最后做
	public function del(){
		exit('暂时关闭');
	}
}