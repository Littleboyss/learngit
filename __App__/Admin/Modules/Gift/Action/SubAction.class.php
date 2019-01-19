<?php
/**
 * @author chengy 2017.3.22
 */

class SubAction extends AdminAction{
	//列表
	public function index(){
		$sub = M('ShopSub');
		$data = $sub->select();
		$data = $this->get_tree($data);
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->display();	
	}
	//添加
	public function add(){
		$ShopSub = M ( 'ShopSub' );
		if (IS_POST) {
            $data['name'] = $_POST['catename'];
            $data['pid'] = $_POST['pid'];
        	$data['update_time'] = time();
        	$res = $ShopSub->add($data);
			if($res){
				$pid = $this->getNavPid($data['pid']);
				if ( $pid == 4) {
		            $attr_names= preg_replace("/(，)/" ,',' ,$_POST['attr_name']); 
					$attr_name = explode(',' , $attr_names);
					foreach ( $attr_name as $key => $value) {
						$datas['sub_id'] =  $res;
						$datas['attr_name'] =  $value;
						$res = M('ShopAttribute')->add($datas);
					}  
	                $this->success ('添加成功',U('index'));
				}else{
					$this->success ('添加成功',U('index'));
				}
            }else{
                $this->error ('添加失败',U('index'));
            }
			
		} else {
			$datas = $ShopSub->select();
			$array = $this->get_tree($datas);
			$pid = $this->getNavPid($id);//  获取顶级分类
			//判断是否为实物
			if ($pid == 4) {
				$attr  = M('ShopAttribute')->where('sub_id ='. $id)->select();
				// 获取属性值字段
				$arr = array_column($attr, 'attr_name');
				$this->assign('attribute',$arr);

			}
			$this->assign('projects',$array);
			$this->display ();
		}
	}
	//修改
	public function edit(){
		$ShopSub = M ( 'ShopSub' );
		if (IS_POST) {
            $data['name'] = $_POST['catename'];
            $data['id'] = $_POST['id'];
            $_data = $ShopSub->where(array('id' => $_POST['id']))->find();
            if($_data['id'] == $_POST['pid']){
            	$this->error('分类选择错误',U('index'));
            }
            $data['pid'] = $_POST['pid'];
        	$data['update_time'] = time();
			if($ShopSub->save($data)){
	            $attr_names= preg_replace("/(，)/" ,',' ,$_POST['attr_name']); 
				$attrs = M('ShopAttribute')->field("GROUP_CONCAT(attr_name,'') as attr")->where(array('sub_id' => $data['id']))->select();
				if ($attr_names != $attrs[0]) {
					M('ShopAttribute')->where(array('sub_id' => $data['id']))->delete();
					$attr_name = explode(',' , $attr_names);
					foreach ( $attr_name as $key => $value) {
						$datas['sub_id'] =  $data['id'];
						$datas['attr_name'] =  $value;
						$res = M('ShopAttribute')->add($datas);
					}
				}
                $this->success ('更新成功',U('index'));
            }else{
                $this->error('更新失败',U('index'));
            }
			
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('数据异常',U('index'));
			}
			$Map['id'] = $id;
			$data  = $ShopSub->where($Map)->find();
			$datas = $ShopSub->select();
			$array = $this->get_tree($datas);
			$pid = $this->getNavPid($id);//  获取顶级分类
			//判断是否为实物
			if ($pid == 4) {
				$attr  = M('ShopAttribute')->where('sub_id ='. $id)->select();
				// 获取属性值字段
				$arr = array_column($attr, 'attr_name');
				$this->assign('attribute',$arr);

			}
			$this->assign('data',$data);
			$this->assign('projects',$array);
			$this->display ();
		}
	}

	
	//删除
	public function del(){
		$ShopSub = M ( 'ShopSub' );
		// 删除父类时同时删除该类下的子类
		$datas = $ShopSub->select();
		// 要删除的分类
		$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
		// 判断该分类下有无商品
		$res = $this->find_sub_goods($id);
		if ($res) {
			$this->error('删除失败该分类下还有商品',U('index'));
		}
		$array = $this->get_tree($datas,$id);
		$arr = array_column($array, 'id');
		if ($arr) {
			foreach ($arr as $key => $value) {
				// 判断该分类下有无商品
				$res = $this->find_sub_goods($value);
				if ($res) {
					$this->error('删除失败该分类下还有商品',U('index'));
				}else{
					$ShopSub->where('id ='.$value)->delete();
					$this->success ('删除成功',U('index'));
				}
			}
		}else{
			$ShopSub->where('id ='.$id)->delete();
			$this->success ('删除成功',U('index'));
		}
	}
	// 判断该分类下有无商品
	protected function find_sub_goods($id){
		$res = M('ShopGoods')->where('shop_sub_id ='.$id)->find();
		return (bool)$res;
	}
	// 判断分类是否是为实物分类
	public function check_sub(){
		$id = $_POST['sub_id'];
		$pid = $this->getNavPid($id);
		$this->success ('获取成功',$pid);
	}

	// 获取无限极分类
	public function get_tree($array,$pid = 0,$level =0){
		static $tree;
		foreach ($array as  $value) {
			if ($value['pid'] == $pid ) {
				$value['level'] = $level;
				$tree[] = $value;
				$this->get_tree($array,$value['id'],$level +1);
			}
		}
		return $tree;
	}
	// 获取顶级分类
	protected function getNavPid($id){
	    $nav = M('ShopSub')->find($id);
	    if($nav['pid'] != 0){ return $this->getNavPid($nav['pid']); }
	    return $nav['id'];
	}
}