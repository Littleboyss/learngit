<?php
/**
 * @author wangh 2017.2.20
 */

class RoomAction extends AdminAction{

	//列表
	public function index(){
		$MatchRoomType = M('MatchRoomType');
		import('ORG.Util.Page');
		$count = $MatchRoomType->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $MatchRoomType->order("sort asc")->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->display();	
	}
	//添加
	public function add(){
		$MatchRoomType = M ('MatchRoomType');
		$data = $MatchRoomType->create();
		if ($data) {
			$data['add_time'] = time();
			$result = $MatchRoomType->add($data);
			if($result){
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
		$MatchRoomType = M('MatchRoomType');
		$data = $MatchRoomType->create();
		if ($data) {
			$result = $MatchRoomType->save($data);
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
			$data = $MatchRoomType->where($Map)->find();
			$this->assign('data',$data);
			$this->display();
		}
	}
	//删除,最后做
	public function del(){
		exit('暂时关闭');
	}
}