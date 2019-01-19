<?php
/**
 * 轮播图片管理
 * @author wang
 */

class SlideAction extends AdminAction{
	/**
	* 图片列表
	*/
	public static $types = array(1=>'APP首页',2=>'微信商城',3 => 'APP商城',4 => '微信首页');

	public function index(){
		$AdminSlide = M ( 'AdminSlide' );
		import('ORG.Util.Page');
		$count = $AdminSlide->where($Map)->count();
		$page = new Page($count, 11);
		$show = $page->show();
		$data = $AdminSlide->where($Map)->order("id ASC")->limit($page->firstRow . ',' . $page->listRows)->select ();
		$this->assign("show", $show);
		$this->assign ( 'data', $data );	
		$this->assign('types', self::$types);
		$this->display();	
	}
	/**
	* 图片添加
	*/
	public function add(){
		$AdminSlide = M ( 'AdminSlide' );
		if(IS_POST){
            $data = $AdminSlide->create();
            if(!$data['type']){
            	$this->error('请选择类型',U('add'));
            }
            $data['add_time'] = time();
            if($AdminSlide->add($data)){
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败',U('index'));
            }
        }else{
        	$this->assign('types', self::$types);
            $this->display();
        }
	}
	/**
	* 图片编辑
	*/
	public function edit(){

		$AdminSlide = M ( 'AdminSlide' );

		if (IS_POST) {
            $data = $AdminSlide->create();

            if(!$data['type']){
            	$this->error('请选择类型',U('edit',array('id' => $data['id'])));
            }
			$data['add_time'] = time();
			if($AdminSlide->save ( $data )){
                $this->success ('修改成功',U('index'));
            }else{
                $this->error ('修改失败',U('index'));
            }
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $AdminSlide->where($Map)->find();
			$this->assign('types', self::$types);
			$this->assign('data',$data);
			$this->display ();
		}
	}
	/**
	* 图片删除
	*/
	public function del(){
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$AdminSlide = M ( 'AdminSlide' );
        $AdminSlide->delete($id);
		$this->success('删除成功',U('index',array('gid'=>$gid)));
	}

}