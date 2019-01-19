<?php
/**
 * @author chengy 2017.3.21
 */

class ExchangeAction extends AdminAction{
	//列表
	public function index(){
		$ShopGoods = M('ShopGoods');
		$ShopSub = M('ShopSub');
		$sub = $ShopSub->select();
		import('ORG.Util.Page');
		$where = '1 = 1';
		$sub_id = $_REQUEST['sub_id'];
		if (!empty($sub_id)) {
			unset($where);
			$where['shop_sub_id'] = $sub_id;
		}
		$name = $_REQUEST['name'];
		if (!empty($name)) {
			unset($where);
			$where['name'] =  array('like',"%".$name."%");
		}
		$count = $ShopGoods->where($where)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $ShopGoods->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->assign ('projects', $sub );
		$this->assign ('sub_id', $sub_id );
		$this->display();	
	}

	//添加
	public function add(){
		$ShopGoods = M ('ShopGoods');
		$array = M('ShopSub')->field('pid,name,id')->select();
		if (IS_POST) {
			$post = $_POST;
			// 1 实物入属性表
			$sub_id =  $post['shop_sub_id'];
			$map['id'] = $sub_id;
			$pid = $this->getNavPid($sub_id);//  获取顶级分类
			//判断是否为实物
			if ($pid == 3) {
				$is_virtual = 1;
			}else{
				$is_virtual = 0;
				if (!empty($post['attr_value']) && !empty($post['attr_id'])) {
					foreach ($post['attr_value'] as $key => $value) {
						$attrdata['attr_id'] = $post['attr_id'][$key];
						$value= preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$value); 
						$attrdata['value'] = $value;
						$res = M('ShopGoodsAttribute')->add($attrdata);
						if ($res) {
							$ids[] = $res;
						}
					}
					$attr_id = implode(',' , $ids); 
				}
			}
			// 2 入相册表(至少上传一张)
			if (empty($post['img1'])) {
				$this->error('添加失败，至少上传一张相片',U('index'));
			}
			$datas['img1'] = $post['img1'];
			$datas['img2'] = $post['img2'];
			$datas['img3'] = $post['img3'];
			$datas['img4'] = $post['img4'];
			$datas['img5'] = $post['img5'];
			$datas['addtime'] = time();
			$res = M('ShopAlbum')->add($datas);
			if ($res) {
				$album_id = $res;
			}
			if ($post) {
				$data = $ShopGoods->create();
				$data['addtime'] = time();
				$data['is_virtual'] = $is_virtual;
				$data['attr_id'] = $attr_id;
				$data['album_id'] = $album_id;
				$result = $ShopGoods->add($data);
				if($result){
					$this->success('成功',U('index'));
				}else{
					$this->error('失败',U('index'));
				}
			}	
		}else {
			$this->assign ('projects', $array );
			$data['author'] = $_SESSION['admin']['nickname']; 
			$this->assign('data',$data);
			$this->display();
		}
	}
	//修改
	public function edit(){
		$ShopGoods = M('ShopGoods');
		$array = M('ShopSub')->field('pid,name,id')->select();
		if (IS_POST) {
			$post = $_POST;
			$id = $post['id'];
			$data = $ShopGoods->where('id = '.$id)->find();
			// 1 实物入属性表
			$sub_id =  $post['shop_sub_id'];
			$map['id'] = $sub_id;
			$pid = $this->getNavPid($sub_id);//  获取顶级分类
			//判断是否为实物
			if ($pid == 3) {
				$is_virtual = 1;
			}else{
				$is_virtual = 0;
				// 未改变分类信息 修改操作
				if (!empty($data['attr_id'])) {
					if (!empty($post['attr_value']) && !empty($post['attr_id'])) {
						$attr_id = $data['attr_id']; 
						$attr = explode(',', $data['attr_id']);
						foreach ($post['attr_value'] as $key => $value) {
							$attrdata['id'] = $attr[$key];
							$value= preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$value); 
							$attrdata['value'] = $value;
							$res = M('ShopGoodsAttribute')->save($attrdata);
						}
					}
				}else{
					// 作添加操作
					if (!empty($post['attr_value']) && !empty($post['attr_id'])) {
						foreach ($post['attr_value'] as $key => $value) {
							$attrdata['attr_id'] = $post['attr_id'][$key];
							// 把中文逗号改为英文逗号
							$value= preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$value); 
							$attrdata['value'] = $value;
							$res = M('ShopGoodsAttribute')->add($attrdata);
							if ($res) {
								$ids[] = $res;
							}
						}
						$attr_id = implode(',' , $ids); 
					}
				}
			}
			// 2 修改相册表(至少上传一张)
			if (empty($post['img1'])) {
				$this->error('添加失败，至少上传一张相片');
			}
			// 没有相册ID则为添加，有就为修改
			if (!empty($data['album_id'])) {
				$datas['id'] = $data['album_id'];
				$datas['img1'] = $post['img1'];
				$datas['img2'] = $post['img2'];
				$datas['img3'] = $post['img3'];
				$datas['img4'] = $post['img4'];
				$datas['img5'] = $post['img5'];
				$datas['addtime'] = time();
				$res = M('ShopAlbum')->save($datas);
				if ($res) {
					$album_id = $data['album_id'];
				}
			}else{
				$datas['img1'] = $post['img1'];
				$datas['img2'] = $post['img2'];
				$datas['img3'] = $post['img3'];
				$datas['img4'] = $post['img4'];
				$datas['img5'] = $post['img5'];
				$datas['addtime'] = time();
				$res = M('ShopAlbum')->add($datas);
				if ($res) {
					$album_id = $res;
				}
			}
			
			$update = $ShopGoods->create();
			if ($update) {
				$update['addtime'] = time();
				$update['is_virtual'] = $is_virtual;
				$update['attr_id'] = $attr_id;
				$update['album_id'] = $album_id;
				$update['id'] = $id;
				$result = $ShopGoods->save($update);
				if($result){
					$this->success('成功',U('index'));
				}else{
					$this->error('失败',U('index'));
				}
			} 
		}else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('index'));
			}
			$Map['t1.id'] = $id;
			// 获取商品信息
			$data = $ShopGoods->field('t1.*,t2.img1,t2.img2,t2.img3,t2.img4,t2.img5')->join('as t1 left join '.c('DB_PREFIX').'shop_album as t2 on t1.album_id = t2.id')->where($Map)->find();
			// 获取商品属性信息
			if(!empty($data['attr_id'])){
				$attr_ids = explode(',' , $data['attr_id']);
				foreach ($attr_ids as $key => $value) {
    				$temp[] = M('ShopGoodsAttribute')->field('t2.attr_name,t1.value,t1.id')->join('as t1 left join '.c('DB_PREFIX').'shop_attribute as t2 on t1.attr_id = t2.id')->where('t1.id = '.$value)->find();
    			}
    			if (empty($temp)) {
    				$ShopAttribute = M ('ShopAttribute');
					$map['sub_id'] = $data['shop_sub_id'];
					$temp  = $ShopAttribute->field('id,attr_name')->where($map)->select();
    			}
    			$data['attrdata']=$temp;
			}
			if (empty($data['attrdata'])) {
				$ShopAttribute = M ('ShopAttribute');
				$map['sub_id'] = $data['shop_sub_id'];
				$temp  = $ShopAttribute->field('id,attr_name')->where($map)->select();
				$data['attrdata']=$temp;
			}
			$this->assign ('projects', $array );
			$this->assign('data',$data);
			$this->display();
		}
	}
	// 更改商品状态
	public function set_status(){
		$ShopGoods = M('ShopGoods');
		$dataName = I('post.dataName');
		$setStatus = I('post.setStatus');
		$data['id'] = I('post.id');
		$data[$dataName] = $setStatus;
		$res = $ShopGoods->save($data);
		if ($res) {
			$msg['error'] = 0;
			$msg['msg'] = '修改成功';
			$this->ajaxReturn($msg);
		}else{
			$msg['error'] = 1;
			$msg['msg'] = '修改失败';
			$this->ajaxReturn($msg);
		}
	}
	// 虚拟商品卡号卡密查看
	public function view(){
    	$id = I('get.id');
    	$RechargeCard = M('ShopVirtualCode');
    	import('ORG.Util.Page');
    	$Map['virtual_id'] = $id;
    	$count = $RechargeCard->where($Map)->count();
		$page = new Page($count, 100);
		$show = $page->show();
    	$rs = $RechargeCard->where($Map)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('show',$show);
        $this->assign('gift_id',$id);
    	$this->assign('rs',$rs);
    	$this->display();
    }
    public function import(){
    	$id = I('get.id');	
        $this->assign('id',$id);
    	$this->display();
    }
    // 实物商品库存更改与添加
	public function product_edit(){
		$ShopGoodsProduct = M('ShopGoodsProduct');
	    $ShopGoods = M('ShopGoods');
		if (IS_POST) {
			$has_nums = 0;// 库存数
			// 判断是修改还是添加
			if(isset($_POST['id'])){
				$id = $_POST['id'];
				foreach ($id as $key => $value) {
					$data['id']=$value;
					$data['attr_value']=$_POST['attr_value'][$key];
					$data['nums']=$_POST['attr_nums'][$key];
					$has_nums +=$data['nums'];
					$ShopGoodsProduct->save($data);
				}
				// 更新商品表内的总库存数量
				$res = $ShopGoods->where('id = '.$_POST['goods_id'])->setField('has_nums',$has_nums);
				$this->success('修改成功');
			}else{
				
				foreach ($_POST['attr_value'] as $key => $value) {
					$data['attr_value']=$value;
					$data['goods_id']=$_POST['goods_id'];
					$data['nums']=$_POST['attr_nums'][$key];
					$has_nums +=$data['nums'];
					$ShopGoodsProduct->add($data);
				}
				// 更新商品表内的总库存数量
				$res = $ShopGoods->where('id = '.$_POST['goods_id'])->setField('has_nums',$has_nums);

				$this->success('修改成功');
			}
		}else{
	    	$id = I('get.id');
	    	
	    	$Map['goods_id'] = $id;
	    	$array = $ShopGoodsProduct->where($Map)->select();
	    	if (!$array) {
	    		$res = $ShopGoods->where('id = '.$id)->find();
	    		// 获取商品规格
	            $attr_ids = explode(',', $res['attr_id']);
	            foreach ($attr_ids as $key => $value) {
	                $temp[] = M('ShopGoodsAttribute')->field('t2.attr_name,t1.value')->join('as t1 left join '.c('DB_PREFIX').'shop_attribute as t2 on t1.attr_id = t2.id')->where('t1.id = '.$value)->find();
	                $temp[$key]['value'] = explode(',',$temp[$key]['value']);
	            }
	            $values = array_column($temp,'value');
	            if (count($values)>1) {
	            	$result = $this->combineDika($values);
		            foreach ($result as  $value) {
		            	$arrays[] = implode(',',$value);
		            }
	    			$this->assign('rs',$arrays);
	            }else{
	            	$this->assign('rs',$values[0]);
	            }
	    	}else{
	    		$this->assign('rs',$array);
	    	}
	        $this->assign('goods_id',$id);
	    	$this->display();
		}
    }
    // 清空库存
    public function product_delete(){
    	// 商品ID
    	$goods_id = I('get.id');
    	// 删除该商品库存信息
    	$res = M('ShopGoodsProduct')->where('goods_id = '.$goods_id)->delete();
    	if ($res) {
    		// 删除商品库存数量
    		M('ShopGoods')->where('id = '.$goods_id)->setField('has_nums ',0);
    		$this->success('清空成功');
    	}else{
    		$this->error('清空失败');
    	}
    }
	/**
     * 批量导入礼包（仅支持txt文件格式）
     */
    public function addCard() {
        $gift_id = I('post.gift_id');
        $file = $_FILES['file'];
        $this->check($file);
        $tempfile = $file['tmp_name'];
        $content = file_get_contents($tempfile);
        $content_arr = explode("\n", $content);
        $content_arr=  array_map('trim', $content_arr);
        $content_arr= array_filter($content_arr);
        $m = M('ShopVirtualCode');
        $time = time();
        if (is_array($content_arr)) {
            // 一次导入的号可能会很多，50个一组合并插入
            $total = count($content_arr);
            // $content_arr = array_chunk($content_arr, 50);
            $i = 0;//记录导入失败的个数
            foreach ($content_arr as $k => $group) {
            	$_data = explode('|', $group);
            	$data['codes'] = $_data[0];
                $data['pwd'] = $_data[1];
                $data['virtual_id'] = $gift_id;
                $data['addtime'] = $time;
                if (!$m->add($data)) {
                    $i++;
                }
            }
        }
        M('ShopGoods')->where('id = '.$gift_id)->setInc('has_nums ',$total-$i);
        $this->success('导入完成，'.$i.'个失败');
    }

    /**
     * 检查上传文件
     * @param type $file
     */
    private function check($file) {
        switch ($file['error']) {
            case 1:
                $this->error('上传的文件超过了php.ini中upload_max_filesize 选项限制的值');
                break;
            case 2:
                $this->error('上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值');
                break;
            case 3:
                $this->error('文件只有部分被上传');
                break;
            case 4:
                $this->error('没有文件被上传');
                break;
            case 6:
                $this->error('找不到临时文件夹');
                break;
            case 7:
                $this->error('文件写入失败');
                break;
        }
        if ($file['type'] != "text/plain") {
            $this->error("上传文件格式错误");
        }
    }

    public function delcard(){
        $admin = session('admin');
		// print_r($admin);die;
		$id = I('get.id');
		if($admin['username'] != 'admin'){
			$this->error('你没有权限',U('view',array('id'=>$id)));
		}
        if(!is_numeric($id)){
            $this->error('错误');
        }
        $RechargeCard = M('ShopVirtualCode');
        $Map['virtual_id'] = $id;
        $rs = $RechargeCard->where($Map)->delete();
        if ($rs) {
        	$res = M('ShopGoods')->where('id ='.$id)->setField('has_nums',0);
        }
        $this->redirect(U('view',array('id'=>$id)));
    }
    public function createCard(){
        $id = I('id');
        if(!$id){
            $this->ajaxReturn('参数错误');
        }
        $str = '';
        for ($i=0; $i < 100; $i++) {
            $c = rand(1000,9999);
            $a = md5($id.time().$c);//卡号
            $a = strtoupper('sg' . substr($a, 0, 8));

            $b = md5('sg'.$id.'_'.$id.time().$c);//卡密
            $b = strtoupper(substr($b, 0, 8) . $id);
            $str .= $a.'|'.$b."\r\n";
        }
        $fielname = './card/'.$id.'_'.date('Y_m_d_H_i_s').'.txt';
        file_put_contents($fielname, $str);
        //导入到数据库
        // $content = file_get_contents($fielname);
        $content_arr = explode("\n", $str);
        $content_arr=  array_map('trim', $content_arr);
        $content_arr= array_filter($content_arr);
        $m = M('ShopVirtualCode');
        $time = time();
        if (is_array($content_arr)) {
            // 一次导入的号可能会很多，50个一组合并插入
            $total = count($content_arr);
            // $content_arr = array_chunk($content_arr, 50);
            $i = 0;//记录导入失败的个数
            foreach ($content_arr as $k => $group) {
                $_data = explode('|', $group);
                $data['codes'] = $_data[0];
                $data['pwd'] = $_data[1];
                $data['virtual_id'] = $id;
                $data['addtime'] = $time;
                if (!$m->add($data)) {
                    $i++;
                }
            }
            $count = $m->field('count(id) as num')->where('virtual_id ='.$id.' and user_id = 0')->find();
            M('ShopGoods')->where('id = '.$id)->setfield('has_nums',$count['num']);
        }
        $this->ajaxReturn('导入完成，'.$i.'个失败');
    }
    /**
	* 所有数组的笛卡尔积
	* 
	* @param unknown_type $data
	*/
    protected function combineDika($data) {
	    $cnt = count($data);
	    $result = array();
	    foreach($data[0] as $item) {
	        $result[] = array($item);
	    }
	    for($i = 1; $i < $cnt; $i++) {
	        $result = $this->combineArray($result,$data[$i]);
	    }
	    return $result;
	}
	/**
	 * 两个数组的笛卡尔积
	 * 
	 * @param unknown_type $arr1
	 * @param unknown_type $arr2
	 */
	protected function combineArray($arr1,$arr2) {
	    $result = array();
	    foreach ($arr1 as $item1) {
	        foreach ($arr2 as $item2) {
	            $temp = $item1;
	            $temp[] = $item2;
	            $result[] = $temp;
	        }
	    }
	    return $result;
	}
	// 根据分类获取属性
	public function get_attribute(){
		$ShopAttribute = M ('ShopAttribute');
		$data = $ShopAttribute->create();
		$map['sub_id'] = $data['sub_id'];
		$list = $ShopAttribute->field('id,attr_name')->where($map)->select();
		$this->success('成功',$list);
	}
	// 获取无限极分类
	public function get_tree($array,$pid = 0){
		static $tree;
		foreach ($array as  $value) {
			if ($value['pid'] == $pid ) {
				$tree[] = $value;
				$this->get_tree($array,$value['id']);
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