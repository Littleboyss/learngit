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
		$data = $UserNotice->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->display();	
	}
	//添加
	public function add(){
		$UserNotice = M ('UserNotice');
		$data = $UserNotice->create();
		if ($data) {
			$data['addtime'] = time();
			$result = $UserNotice->add($data);
			if($result){
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('add'));
			}
		} else {
			$this->display();
		}
	}
	//修改
	public function edit(){
		$UserNotice = M('UserNotice');
		$data = $UserNotice->create();
		if ($data) {
			$result = $UserNotice->save($data);
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
			$data = $UserNotice->where($Map)->find();
			$this->assign('data',$data);
			$this->display();
		}
	}
	//删除,最后做
	public function del(){
		exit('暂时关闭');
	}


	
}