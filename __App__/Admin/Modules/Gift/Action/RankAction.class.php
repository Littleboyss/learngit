<?php
/**
 * @author chengy 2017.4.6
 */

class RankAction extends AdminAction{
	//列表
	public function index(){
		$sub = M('UserRankClass')->select();
		import('ORG.Util.Page');
		$where = '1 = 1';
		$sub_id = I('post.sub_id');
		if (!empty($sub_id)) {
			unset($where);
			$where['class_id'] = $sub_id;
		}
		$count = M('UserRank')->where($where)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = M('UserRank')->field('t1.*,t2.name as class_name')->join('as t1 left join '.c('DB_PREFIX').'user_rank_class as t2 on t1.class_id = t2.id')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->assign ('projects', $sub );
		$this->assign ('sub_id', $sub_id );
		$this->display();	
	}
	//添加
	public function add(){
		if (IS_POST) {
			$data= M('UserRank')->create();
        	$data['addtime'] = time();
        	$res = M('UserRank')->add($data);
			if($res){
                $this->success ('添加成功',U('index'));
            }else{
                $this->error ('添加失败',U('index'));
            }
		} else {
			$class = M ( 'UserRankClass' )->select();
			$data['author'] = $_SESSION['admin']['nickname']; 
			$this->assign('data',$data);
			$this->assign('projects',$class);
			$this->display ();
		}
	}
	//修改
	public function edit(){
		$UserRank = M ( 'UserRank' );
		$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
		if (IS_POST) {
			$data= $UserRank->create();
			$data['id'] = $id;
			if($UserRank->save($data)){
                $this->success ('更新成功',U('index'));
            }else{
                $this->error('更新失败',U('index'));
            }
		} else {
			if ($id <= 0) {
				$this->error ('数据异常',U('index'));
			}
			$Map['id'] = $id;
			$data  = $UserRank->where($Map)->find();
			$class = M ( 'UserRankClass' )->select();
			$this->assign('data',$data);
			$this->assign('projects',$class);
			$this->display ();
		}
	}
	// 分类展示
	public function class_list(){
		$data = M('user_rank_class')->select();
		$this->assign('data',$data);
		$this->display();
	}
	// 分类添加
	public function class_add(){
		if (IS_POST) {
			$data= M('UserRankClass')->create();
        	$data['addtime'] = time();
        	$res = M('UserRankClass')->add($data);
			if($res){
                $this->success ('添加成功',U('class_list'));
            }else{
                $this->error ('添加失败',U('class_list'));
            }
		} else {
			$this->display ();
		}
	}
	// 分类修改
	public function class_edit(){
		$UserRankClass = M ( 'UserRankClass' );
		$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
		if (IS_POST) {
			$data= $UserRankClass->create();
			$data['id'] = $id;
			if($UserRankClass->save($data)){
                $this->success ('更新成功',U('class_list'));
            }else{
                $this->error('更新失败',U('class_list'));
            }
		} else {
			if ($id <= 0) {
				$this->error ('数据异常',U('class_list'));
			}
			$Map['id'] = $id;
			$data  = $UserRankClass->where($Map)->find();
			$this->assign('data',$data);
			$this->display ();
		}
	}
	
}