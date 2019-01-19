<?php
/**
 * @author wangh 2017.2.20
 */

class UserAction extends AdminAction{

	public function index(){
		$type = I('post.keyword');
		if($type){
			$Map['username'] = $type;
			$Map['phone'] = $type;
			$Map['_logic'] = 'OR';
		}
		$UserUser = M('UserUser');
		import('ORG.Util.Page');

		$count = $UserUser->where($Map)->count();
		$page = new Page($count, 15);
		$show = $page->show();
		$data = $UserUser->where($Map)->order("id DESC")->limit($page->firstRow . ',' . $page->listRows)->select ();
		$this->assign("show", $show);
		$this->assign ( 'data', $data );	
		$this->display();	
	}

	//测试用户
	public function testuser(){
		$where = 'id<=131 or (id>706 and id<1022)';
		$data = M('UserUser')->field('id,username,token,entrance_ticket,diamond,gold')->where($where)->select();
		$count = M('UserUser')->where($where)->count();
		$this->assign('count',$count);
		$this->assign('data',$data);
		$this->display();
	}



}