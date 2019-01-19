<?php
/**
 * @author chengy 2017.3.27
 */

class OrderAction extends AdminAction{
	//列表
	public function index(){
		$order = M('ShopGoodsOrder');
		$ShopSub = M('ShopSub');
		$sub = $ShopSub->select();
		import('ORG.Util.Page');
		$where = ' t1.status = 1';
		$status = 1;
		if (!empty(I('request.status'))) {
			$status = I('request.status');
			if ($status != 4) {
				$where =' t1.status ='.$status;
			}else{
				$where = '1 = 1';
			}
		}
		$sub_id = I('request.sub_id');
		if (!empty($sub_id)) {
			$where .= ' and t2.shop_sub_id ='.$sub_id;
		}
		$numbers = I('request.numbers');
		if (!empty($numbers)) {
			$where .= ' and t1.numbers ="'.$numbers.'"';
		}
		$count = $order->field('t1.*')->join('as t1 left join '.c('DB_PREFIX').'shop_goods as t2 on t1.goods_id = t2.id')->where($where)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $order->field('t1.*,t2.name,t3.phone,t3.username')->join('as t1 left join '.c('DB_PREFIX').'shop_goods as t2 on t1.goods_id = t2.id left join '.c('DB_PREFIX').'user_user as t3 on t1.user_id = t3.id')->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('t1.addtime desc')->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->assign ('numbers', $numbers );
		$this->assign ('projects', $sub );
		$this->assign ('sub_id', $sub_id );
		$this->assign ('status', $status );
		$this->display();	
	}
	// 订单详情
	public function view(){
		if (IS_POST) {
            $data['id'] = $_POST['id'];
            $data['order_id'] = $_POST['order_id'];
            $data['company'] = $_POST['company'];
            $data['track_num'] = $_POST['track_num'];
            if (!empty($data['company']) && !empty($data['track_num'])) {
            	M('ShopPhysicalOrder')->save($data);
            	// 修改订单状态
            	$res = M('ShopGoodsOrder')->where('id = '.$_POST['order_id'])->setField('status',2);
            	if ($res) {
            		$this->success ('修改成功',U('view',array('id'=>$data['order_id'])));
            	}else{
            		$this->error ('修改失败',U('view',array('id'=>$data['order_id'])));
            	}
            }else{
            	$this->error ('修改失败,参数异常',U('view',array('id'=>$data['order_id'])));
            }

        }else{
			$order_id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			//$user_id = isset ( $_GET ['user_id'] ) ? ( int ) $_GET ['user_id'] : 0;
	    	$map['t1.id'] = $order_id;
	    	//$map['t1.user_id'] = $user_id;
	    	$res = M('ShopGoodsOrder')->field('t1.*,t2.name')->join('as t1 left join '.c('DB_PREFIX').'shop_goods as t2 on t1.goods_id = t2.id')->where($map)->find();
			// 判断是否为实物
	    	if ($res['goods_type'] == 1) {
	    		// 以下为虚拟商品
		    	$maps['user_id'] = $res['user_id'];
		    	$maps['order_id'] = $order_id;
		    	$list = M('VirtualOrderInfo')->field('code_id,nums')->where($maps)->find();
		    	if($list){
		    		$list['name'] = $res['name']; // 商品名称
		    		$ids = explode(',',$list['code_id']);
		    		foreach ($ids as $key => $value) {
		    			$list['codes_list'][] = M('ShopVirtualCode')->field('codes,pwd')->where('id = '.$value)->find();
		    		}
		    		unset($list['code_id']);
		    	}
	    	}else{
				$list = M('ShopPhysicalOrder')->field('id,order_id,nums,company,track_num,attribute')->where("order_id = $order_id")->find();
				$list['name'] = $res['name']; // 商品名称
				$list['status'] = $res['status']; // 订单状态
				$info = M('ShopUserInfo')->where('id ='.$res['address_id'])->find();
				$this->assign ('info', $info );
	    	}
			$this->assign ('data', $list );
			$this->display();	
        }
	}
	public function setStatus(){
		$id = I('post.id');
		$ShopGoodsOrder = M('ShopGoodsOrder');
		$info = $ShopGoodsOrder->where('id = '.$id)->find();
		if (empty($info['address_id']) || $info['status']!=2) {
			$this->ajaxReturn('此商品无法修改为已签收');
		}else{
			$res = M('ShopGoodsOrder')->where('id = '.$id)->setField('status',3);
			if ($res) {
				$this->ajaxReturn('更改订单状态成功');
			}else{
				$this->ajaxReturn('更改订单状态失败');
			}
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
	public function get_goods_room(){
		$room = M('MatchRoom');
		$ShopGoods = M('ShopGoods');
		$User = M('UserUser');
		$Record = M('UserGuessRecord');
		 
		$room_data = $room->where('reward_id = 12 and settlement_status = 2 ')->select();// 查询实物房间 and status = 1
		foreach ($room_data as $key => $value) {
			$data = $Record->where('room_id = '.$value['id'].' and is_reward = 1')->select();
			foreach ($data as $k => $v) {
				$v['room_name'] = $value['name'];
				$v['room_id'] = $value['id'];
				$v['goods_name'] = $ShopGoods->where('id = '.$value['prize_goods_id'])->getfield('name');
				$v['user_name'] = $User->where('id = '.$v['uid'])->getfield('username');
				// 获取用户数据存在哪个分表之中
		        $table_name = $this->get_hash_table('UserBetGess',$v['uid']);
		        $UserBetGess = M($table_name); 
		        $v['status'] = $UserBetGess->where('room_id = '.$value['id'].' and uid = '.$v['uid'])->getfield('status') ;// 领取状态 
				$user_data[] = $v;
			}
		}
		import('ORG.Util.Page');
		$count = count($user_data);
		$page = new Page($count, 10);
		$datas = array_slice($user_data,$page->firstRow,$page->listRows);
		$show = $page->show();
		$this->assign("show", $show);
		$this->assign ('data', $datas );
		$this->display();
	}
	 // 获取用户名分表的名称
    protected function get_hash_table($table,$userid) {  
        $str = crc32($userid);  
        if($str<0){  
        $hash = substr(abs($str), 0, 1);  
        }else{  
        $hash = substr($str, 0, 1);  
        }  

        return $table."_".$hash;  
    } 
}