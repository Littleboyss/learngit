<?php
/**
 * 商城模块
 * @author chengy 2017.03.16
 */
class ShopAction extends LoginAction{
    // 门票购买
    public function ticket_pay(){
        $nums = (int)$this->_data['nums'];// 购买的数量
        // nums必须大于10小于1000
        if($nums < 10 || $nums > 1000 || !is_integer($nums)){
            $this->returnMsg(1,'buy');
        }elseif($this->_user['diamond'] < $nums){
            $this->returnMsg(1,'turnplate'); // 砖石数量不足
        }else{
            $uid = $this->_user['id']; // 获取到的用户id
            // 扣除用户账号内的砖石，增加用户账号内的门票数
            $res = M('UserUser')->where(array('id' => $uid))->setDec('diamond',$nums);
            if ($res) {
                // 帐变记录
                $this->insert_account(6,2,$uid,$nums);
                $result = M('UserUser')->where(array('id' => $uid))->setInc('entrance_ticket',$nums);
                if ($result) {
                    $data['user_id']=$uid;   // 用户ID
                    $data['nums']=$nums;     // 充值数量
                    $data['addtime']=time(); // 充值时间
                    M('ShopTicketPay')->add($data); // 写入门票充值记录表
                    // 帐变记录
                    $this->insert_account(7,1,$uid,$nums,true);
                }
            }

            $this->returnMsg(0,'buy'); //购买成功 
        }
    }
    // 砖石购买
    public function diamond_pay(){
        
    }
    // 显示商品列表
    public function goods_list(){

        $page = $this->_data['page'] ? $this->_data['page'] : 1;
        $limit = 10; // 每次查询数目,默认10条
        $start = ($page - 1) * $limit;

        $order = $this->_data['order'];  // 排序类型 1 价格 2销量
        $order_type = $this->_data['order_type'];  // 价格方式 1 正序 2倒序
        $is_virtual  = $this->_data['is_virtual'];  // 是否为虚拟商品
        if ($is_virtual == 1) {
            $Map['is_virtual'] = 1;
        }else{
            $Map['is_virtual'] = array('NEQ',1);
        }
        if($order == 1){
            $order = 'price';
        }elseif($order == 2){
            $order = 'sell_num';
        }else{
            $order = 'hot_sort';
        }
        //排序类型
        if($order_type == 1){
            $order .= ' asc';
        }elseif($order_type == 2){
            $order .= ' desc';
        }else{
            $order .= ' asc';
        }
        $ShopGoods = M('ShopGoods'); 
        $Map['state'] = 1;
        $data = $ShopGoods->field('id,name,price,type,avatar_img,shop_sub_id,sell_num,is_virtual')->where($Map)->order($order)->limit($start,$limit)->select();
        $count = $ShopGoods->where($Map)->count();
        $sum = ceil($count/$limit);
        // echo $ShopGoods->getLastSql();
        if ($data) {
            $this->returnMsg(0,'turnplate',$data,$sum);
        }else{
            $this->returnMsg(1,'room');
        }
    }
    // 商品详情页
    public function goods_show(){
        $goods_id = $this->_data['id']; // 商品ID
        $ShopGoods = M('ShopGoods'); 
        $ShopGoodsProduct = M('ShopGoodsProduct');
        $map['id'] = $goods_id; // 商品ID
        $map['state']=1;// 商品必须为已经上架的商品
        $data = $ShopGoods->field('id,name,price,avatar_img,attr_id,remain_num,state,detail,is_virtual,intro,hot_sort,has_nums,type,album_id')->where($map)->find();
        if ($data ) {
            // 获取商品相册
            $album = M('ShopAlbum')->field('img1,img2,img3,img4,img5')->where('id = '.$data['album_id'])->find();
            foreach ($album as $key => $value) {
                if (empty($value)) {
                    unset($album[$key]);
                }else{
                    $album_list[]=$value;
                }
            }
            $data['album'] = $album_list;
        
            if($data['is_virtual'] == 1){
                $this->returnMsg(0,'turnplate',$data); // 获取成功
            }else {
                // 获取商品规格
                $attr_ids = explode(',', $data['attr_id']);
                foreach ($attr_ids as $key => $value) {
                    $temp[] = M('ShopGoodsAttribute')->field('t2.attr_name,t1.value')->join('as t1 left join '.c('DB_PREFIX').'shop_attribute as t2 on t1.attr_id = t2.id')->where('t1.id = '.$value)->find();
                    $temp[$key]['value'] = explode(',',$temp[$key]['value']);
                }
                $Map['goods_id'] = $goods_id;
                $data['product'] = $ShopGoodsProduct->field('attr_value,nums')->where($Map)->select();
                $this->returnMsg(0,'turnplate',$data,$temp); // 获取成功
            }
        }else{
            $this->returnMsg(1,'room');  // 获取失败
        }
    }
    // 添加收货人信息接口
    public function add_address(){
        $ShopUserInfo = M('ShopUserInfo');
        $data['user_id']  = $this->_user['id'];      // 用户ID
        $count  = $ShopUserInfo->where(array('user_id'=>$this->_user['id']))->count();
        if ($count >= 5) {
            $this->returnMsg(3,'address');
        }
        $data['name']     = $this->_data['name'];    // 收货人姓名
        $data['phone']    = $this->_data['phone'];   // 收货人手机号
        if(!preg_match("/^1[34578]\d{9}$/", $data['phone'])){
            $this->returnMsg(7,'user');// 请正确输入手机号
        }
        $data['address']  = $this->_data['address']; // 收货人地址
        $data['post_num'] = $this->_data['post_num'];// 邮编
        // $data['is_default'] = $this->_data['is_default'];// 是否为默认地址
        $data['addtime']  = time();                  // 添加时间
        if (!empty($data['name'])  && !empty($data['address']) && !empty($data['post_num'])) {
            // 查出用户所有的收货地址
            $result = $ShopUserInfo->where(array('user_id'=>$this->_user['id'],'is_default'=>1))->find();
            if (!$result) {
                $data['is_default'] = 1; // 默认收货地址
            }else{
                // 有其他收货人信息又把这收货地址设为默认
                if ($data['is_default'] == 1) {
                    $ShopUserInfo->where(array('id'=>$result['id']))->setField('is_default',0);
                }
            }
            $res  = $ShopUserInfo->add($data);
            if ($res) {
                $this->returnMsg(0,'address'); //地址添加成功
            }else{
                $this->returnMsg(1,'address'); //地址添加失败
            }
        }else{
            $this->returnMsg(2,'customer'); // 输入数据异常
        }
    }
    // 显示用户收货地址信息
    public function show_address(){
        $user_id  = $this->_user['id'];      // 用户ID
        $data = M('ShopUserInfo')->field('id,name,phone,address,post_num,is_default')->where(array('user_id'=>$user_id))->select();
        if ($data) {
            $this->returnMsg(0,'order',$data); // 获取成功
        }else{
            $this->returnMsg(1,'room'); // 获取失败
        }
    }
    // 修改用户收货地址信息
    public function edit_address(){
        $id       = $this->_data['id'];     // ID
        $ShopUserInfo = M('ShopUserInfo');
        $data['user_id']  = $this->_user['id']; // 用户ID
        $data['name']     = $this->_data['name'];    // 收货人姓名
        $data['phone']    = $this->_data['phone'];   // 收货人手机号
        if(!preg_match("/^1[34578]\d{9}$/", $data['phone'])){
            $this->returnMsg(7,'user');// 请正确输入手机号
        }
        $data['address']  = $this->_data['address']; // 收货人地址
        // $data['post_num'] = $this->_data['post_num'];// 邮编
        $res = $ShopUserInfo->where(array('id'=>$id , 'user_id'=>$this->_user['id']))->save($data);
        if ($res) {
            $this->returnMsg(0,'edit');//修改成功
        }else{
            $this->returnMsg(1,'edit');//修改失败
        }
    }
    // 设为默认收货地址信息
    public function set_address_status(){
        $user_id  = $this->_user['id'];  // 用户ID
        $id       = $this->_data['id'];  // 地址ID
        $ShopUserInfo = M('ShopUserInfo');
        $res = $ShopUserInfo->field('id,name,phone,address,post_num,is_default')->where(array('user_id'=>$user_id,'id'=>$id))->find();
        // 判断传过来的地址ID是否正确
        if (!$res) {
            $this->returnMsg(8,'user'); // 数据异常
        }else{
            // 修改之前的默认的地址状态
            $ShopUserInfo->where(array('user_id'=>$user_id,'is_defaut'=>1))->setField('is_default',0);
            // 设为默认
            $data = $ShopUserInfo->where(array('id'=>$id))->setField('is_default',1);
        }

        if ($data) {
            $this->returnMsg(0,'order',$data); // 获取成功
        }else{
            $this->returnMsg(1,'room'); // 获取失败
        }
    }
    // 删除收货地址信息
    public function delete_address(){
        $user_id  = $this->_user['id'];  // 用户ID
        $id       = $this->_data['id'];  // 地址ID
        $ShopUserInfo = M('ShopUserInfo');
        $res = $ShopUserInfo->field('id,name,phone,address,post_num,is_default')->where(array('user_id'=>$user_id,'id'=>$id))->find();
        // 判断传过来的地址ID是否正确
        if (!$res ) {
            $this->returnMsg(8,'user'); // 数据异常
        }else{
            // if ($res['is_default'] == 1) {
            //     $this->returnMsg(2,'address'); // 默认地址无法删除
            // }
            // 删除
            $data = $ShopUserInfo->where(array('id'=>$id))->delete();
        }

        if ($data) {
            $this->returnMsg(0,'delete'); // 删除成功
        }else{
            $this->returnMsg(1,'delete'); // 删除失败
        }
    }
    // 购买商品
    public function goods_buy(){
        $goods_id = (int)$this->_data['goods_id']; // 商品ID
        $nums     = (int)$this->_data['nums']; // 商品数量
        if (!empty($goods_id) && !empty($nums)) {
            $map['id'] = $goods_id;
            // 获取商品信息
            $list = M('ShopGoods')->field('price,is_virtual,has_nums,sell_num,remain_num,type')->where($map)->find();
            if (!$list) {
                $this->returnMsg(6,'buy');// 获取不到商品信息
            }
            // 判断是否超过了个人最大购买数量
            if ($list['remain_num']<$nums) {
                $this->returnMsg(2,'buy'); // 超过个人最大购买数量
            }
            // 判断是否为虚拟商品
            if ($list['is_virtual'] == 1) {
                $data['goods_type'] = 1; // 商品类型1为虚拟2为实物
                // 查看空余的激活码数量
                $ids = M('ShopVirtualCode')->field('id')->where(array('virtual_id'=>$goods_id,'user_id'=>0))->limit($nums)->select();
                if ($ids) {
                    foreach($ids as $k => $v) {
                        $datas['user_id']=$this->_user['id'];
                        $datas['updatetime']=time();
                        $res = M('ShopVirtualCode')->where('id = '.$v['id'])->save($datas);
                        $temp[] = $v['id']; 
                    }
                }else{
                    $this->returnMsg(7,'buy'); // 商品库存不足
                }
            }else{
                $value    = $this->_data['value']; // 商品规格
                // 查询库存表
                $where['goods_id']=$goods_id;
                $where['attr_value']=$value;
                $res = M('ShopGoodsProduct')->field('nums')->where($where)->find();
                if($res){
                    if($nums > $res['nums'] ){
                        $this->returnMsg(7,'buy'); // 商品库存不足
                    }
                }else{
                    $this->returnMsg(8,'buy'); // 商品信息填写不完整
                }
                $address_id = (int)$this->_data['address_id'];  // 收货地址ID
                $res = M('ShopUserInfo')->field('id')->where(array('id'=>$address_id))->find(); 
                if (!empty($address_id) && $res) {
                    $data['address_id'] = $address_id;// 收货人信息ID
                    $data['status']  = 1; // 默认为未发货
                    $data['goods_type'] = 2; // 商品类型1为虚拟2为实物
                }else{
                    $this->returnMsg(9,'buy'); // 请填写收货人信息
                }
            }
            // 判断用户的余额是否充足
            $types = 'gold';
            $errors = 3;
            $type = 3;
            if ($list['type'] == 1) {
                $types = 'diamond'; 
                $errors = 4;
                $type = 2;
            }
            if($this->_user[$types] < $list['price']*$nums){
                $this->returnMsg($errors,'buy'); // 资源数量不足
            }else{
                // 扣除用户木头或砖石
                $res = M('UserUser')->where(array('id' => $this->_user['id']))->setDec($types,$list['price']*$nums);
                if ($res) { 
                    
                    // 销售数量增加
                    M('ShopGoods')->where(array('id'=>$goods_id))->setInc('sell_num',$nums);
                    // 库存减少
                    M('ShopGoods')->where(array('id'=>$goods_id))->setDec('has_nums',$nums);
                    // 帐变记录
                    $this->insert_account(9,$type,$this->_user['id'],$list['price']*$nums);

                    // 商品销售量增加，入订单表
                    $data['numbers'] = 'sn_'.date('Ymdhis').mt_rand(0,10000); // 订单编号
                    $data['goods_id'] = $goods_id; // 商品ID
                    $data['user_id'] = $this->_user['id']; // 用户ID
                    if ($list['is_virtual'] == 1) {
                        $data['status'] = 2;// 虚拟商品购买默认为已处理
                    }
                    $data['goods_nums'] = $nums; // 商品数量
                    $data['price'] = $list['price']*$nums; // 商品总价格
                    $data['addtime'] = time(); // 添加时间
                    $order_id = M('ShopGoodsOrder')->add($data);
                    if ($order_id) {
                        // 入订单详情表
                        unset($data);
                        if ($list['is_virtual'] == 1) {
                            $data['order_id'] = $order_id;// 订单编号
                            $data['code_id'] = implode(',',$temp);// 激活码编号ID
                            $data['user_id'] = $this->_user['id'];
                            $data['nums'] = $nums;// 数量
                            $res = M('VirtualOrderInfo')->add($data);
                        }else{
                            // 库存表数量减少
                            M('ShopGoodsProduct')->field('nums')->where(array('goods_id'=>$goods_id,'attr_value'=>$value))->setDec('nums',$nums);
                            $data['order_id'] = $order_id;// 订单编号
                            $data['user_id'] = $this->_user['id'];
                            $data['attribute'] = $value;// 属性值
                            $data['address_id'] = $address_id;// 收货人信息ID
                            $data['nums'] = $nums;// 数量
                            $res = M('ShopPhysicalOrder')->add($data);
                            //商品属性 $value
                        }
                        $this->returnMsg(0,'buy');// 购买成功
                    }
                }
            }
        }else{
            $this->returnMsg(5,'buy');// 输入数据不合法
        }
    }

    // 显示订单列表信息
    public function order_list(){
        $page = $this->_data['page'];
        if(empty($page)){
            $start = 0;
        }else{
            $start = ($page-1)*10;
        }
        // 总记录数
        $count = M('ShopGoodsOrder')->where(array('user_id'=>$this->_user['id']))->count();
        // 页码数
        $lastpage = ceil($count/10);
        $res = M('ShopGoodsOrder')->field('t1.id,t1.goods_type,t2.type,t1.address_id,t1.numbers,t1.goods_nums,t1.status,t1.price,t2.name,t2.avatar_img,t2.shop_sub_id,t1.addtime')->join('as t1 LEFT JOIN '.c('DB_PREFIX').'shop_goods as t2 on t1.goods_id = t2.id')->where(array('t1.user_id'=>$this->_user['id']))->limit($start,10)->order('id desc')->select();
        foreach ($res as $key => $value) {
            unset($res[$key]['goods_type']);
            if ($value['goods_type'] == 2 && $value['status'] == 2) {
                $maps['user_id'] = $this->_user['id'];
                $maps['order_id'] = $value['id'];
                $res[$key]['detail'] = M('ShopPhysicalOrder')->field('nums,company,track_num')->where($maps)->find();
            }
        }
        if ($res) {
            $this->returnMsg(0,'order',$res,$lastpage);// 获取成功
        }else{
            $this->returnMsg(3,'order');// 无订单信息
        }
    }

    // 显示订单详情
    public function order_detail(){
        $order_id = $this->_data['order_id']; // 订单ID
        if (empty($order_id)) {
            $this->returnMsg(1,'order'); // 订单号为空
        }
        $map['t1.id'] = $order_id;
        //$map['t1.goods_type'] = 1;
        $map['t1.user_id'] = $this->_user['id'];
        $res = M('ShopGoodsOrder')->field('t1.id,t1.goods_nums,t1.goods_type,t1.numbers,t1.status,t2.name')->join('as t1 LEFT JOIN '.c('DB_PREFIX').'shop_goods as t2 on t1.goods_id = t2.id')->where($map)->find();
        if(!$res){
            $this->returnMsg(3,'order');// 无订单信息
        }
        // 以下为虚拟商品
        $maps['user_id'] = $this->_user['id'];
        $maps['order_id'] = $order_id;
        if ($res['goods_type'] == 1) {
            $list = M('VirtualOrderInfo')->field('code_id,nums')->where($maps)->find();
            if($list){
                $list['name'] = $res['name']; // 商品名称
                $ids = explode(',',$list['code_id']);
                foreach ($ids as $key => $value) {
                    $list['codes_list'][] = M('ShopVirtualCode')->field('codes,pwd')->where('id = '.$value)->find();
                }
                unset($list['code_id']);
                $this->returnMsg(0,'order',$list); // 获取成功
            }else{
                $this->returnMsg(2,'order'); // 查询不到订单信息
            }
        }else{
            // 以下为实物订单详情
            if ($res['status'] == 1 ) {
                $this->returnMsg(4,'order');// 未发货
            }else{
                // 已发货
                $list = M('ShopPhysicalOrder')->field('nums,company,track_num')->where($maps)->find();
                $list['name'] = $res['name']; // 商品名称
                $this->returnMsg(0,'order',$list); // 获取成功
            }
        }
    }

    // 联系客服
    public function customer_care(){
        $data['user_id']  = $this->_user['id'];      // 用户ID
        $data['content']  = $this->remove_xss($this->_data['content']); // 问题描述
        $data['addtime']  = time();                  // 添加时间
        if (empty($data['content'])) {
            $this->returnMsg(2,'customer'); // 输入数据异常
        }else{
            $res = $order_id = M('ShopCustomerCare')->add($data);
            if ($res) {
                $this->returnMsg(0,'customer'); //问题提交成功
            }else{
                $this->returnMsg(1,'customer'); //问题提交失败
            }
        }
    }

    // 热门商品展示
    public function hot_goods(){
        // 获取10个一周内兑换最多的商品
        $one_week = time()-3600*24*7;//一周前的时间戳
        $res = M('ShopGoodsOrder')->field('t2.id,sum(t1.goods_nums) AS nums,t2.price,t2. name,t2.type,t2.shop_sub_id,t2.avatar_img,t2.sell_num ')->join('as t1
LEFT JOIN '.c('DB_PREFIX').'shop_goods AS t2 ON t1.goods_id = t2.id')->where('t2.state = 1 and t1.addtime > '.$one_week)->group(' t2.id')->order(' nums desc')->limit(4)->select();
        $data['hot_goods'] = $res;
        $ShopGoods = M('ShopGoods');
        $res1 = $ShopGoods->field('id,name,price,type,avatar_img,shop_sub_id,sell_num,is_virtual')->where('state = 1 and is_virtual = 1')->order('sell_num desc')->limit(4)->select();
        $data['virtual'] = $res1;
        $res2 = $ShopGoods->field('id,name,price,type,avatar_img,shop_sub_id,sell_num,is_virtual')->where('state = 1 and is_virtual <> 1')->order('sell_num desc')->limit(4)->select();
        $data['phy_goods'] = $res2;
        if ($data) {
            $this->returnMsg(0,'order',$data); // 获取成功
        }else{
            $this->returnMsg(1,'room'); // 获取失败
        }
    }
    // 移除xss
    protected function remove_xss($val) {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
          // ;? matches the ;, which is optional
          // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

          // @ @ search for the hex values
          $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
          // @ @ 0{0,7} matches '0' zero to seven times
          $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
          $val_before = $val;
          for ($i = 0; $i < sizeof($ra); $i++) {
             $pattern = '/';
             for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                   $pattern .= '(';
                   $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                   $pattern .= '|';
                   $pattern .= '|(&#0{0,8}([9|10|13]);)';
                   $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
             }
             $pattern .= '/i';
             $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
             $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
             if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
             }
          }
        }
        return $val;
    }
}