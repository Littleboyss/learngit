<?php
/**
 * 领奖模块
 * @author chengy 2017.03.10
 */
class AwardAction extends LoginAction{
    // 签到天数查询接口
    public function attendance(){
        $attend = M('AwardAttendance');
        $map['user_id'] = $this->_user['id'];
        $list = $attend->field('attend_day,is_get')->where($map)->find();
        $lists['is_get'] = explode(',',$list['is_get']);
        // 如果签到天数大于30天隐藏签到入口
        if(count($lists['is_get']) >= 30){
            $this->returnMsg(1,'attendance');
        }else{
            $lists['attend_day'] = $list['attend_day'];
            if ($list['is_get']) {
                $lists['is_get'] = explode(',',$list['is_get']);
            }
            // 签到天数未满30,返回已签到天数
            $this->returnMsg(0,'attendance',$lists);
        }
    }
    // 领取签到奖励
    public function get_attendance(){
        $attend = M('AwardAttendance');
        $UserUser = M('UserUser');
        $map['user_id'] = $this->_user['id'];
        $list = $attend->field('attend_day,is_get')->where($map)->find();
        $id = $this->_data['id'];
        if($id <= 0){
            $this->returnMsg(8,'user');// 输入数据异常
        }
        if ($id > $list['attend_day']) {
            $this->returnMsg(8,'user');// 输入数据异常
        }
        if (!empty($list['is_get'])) {
            $day = explode(',',$list['is_get']);
            if(in_array($id, $day)){
                $this->returnMsg(8,'user');// 输入数据异常
            }else{
                $result = $UserUser->where('id ='.$this->_user['id'])->setInc('entrance_ticket',10);// 门票加十
                if ($result) {
                    $this->insert_account(8,1,$this->_user['id'],10,true);
                    array_push($day, $id);
                    sort($day);
                    $ids = implode(',',$day);// 数组排序
                    $attend->where($map)->setField('is_get',$ids);// 添加已签到的天数
                    $this->returnMsg(0,'room');// 签到奖励领取成功
                }else{
                    $this->returnMsg(3,'attendance');// 签到奖励领取失败
                }
            }
        }else{
            $result = $UserUser->where('id ='.$this->_user['id'])->setInc('entrance_ticket',10);// 门票加十
            if ($result) {
                $this->insert_account(8,1,$this->_user['id'],10,true);
                $attend->where($map)->setField('is_get',$id);// 添加已签到的天数
                $this->returnMsg(0,'room');// 签到奖励领取成功
            }else{
                $this->returnMsg(3,'attendance');// 签到奖励领取失败
            }
        }
    } 
    // 显示活动签到的天数
    public function activity_day(){
        $data['user_id'] = $this->_user['id'];
        $find = M('AwardActivity')->field('attend_day,is_get')->where($data)->find();
        if ($find) {
            $this->returnMsg(0,'room',$find);// 获取成功
        }else{
            $this->returnMsg(1,'room');// 获取失败
        }
    }
    // 领取签到活动奖励
    public function award_activity(){
        // 判断是否符合领取奖励要求
        $data['user_id'] = $this->_user['id'];
        $award = M('AwardActivity');
        $find = $award->field('attend_day,is_get')->where($data)->find();
        if($find['attend_day'] >= 3 && $find['is_get'] == 2){
            $res = $award->where($data)->setField('is_get',1);
            if ($res) {
                $result = M('UserUser')->where(array('id' => $data['user_id']))->setInc('entrance_ticket',10);
                $this->insert_account(15,1,$data['user_id'],10,true);
                $this->returnMsg(0,'reward');
            }
        }
        $this->returnMsg(3,'reward'); // 不符合领取要求
    }
    public function check_redcode_back(){
        $UserErrorTry=M(c('DB_NAME2').'.UserErrorTry'); // 用户表
        $error = $UserErrorTry->field('redcode_error,redcode_time')->where(array('user_id'=>$this->_user['id']))->find();
        if ($error['redcode_error'] >= 9) {
            $time = time()-$error['redcode_time'];
            if($time<1800){
                $times = 1800 - $time; //还需等待的秒数
                $this->returnMsg(5,'redcode',array('times'=>$times)); // 错误尝试次数过多
            }else{
                $error['redcode_error']=0;// 错误尝试次数清零
            }
        }
        $Redcode = M('AwardRedcode');
        $codes = $this->_data['codes'];
        if($codes){
            $map['codes'] = $codes;
            $list = $Redcode->field('id,codes,type,status,prize,bonus_id')->where($map)->find();
        }else{
            $this->returnMsg(1,'redcode'); // 兑换码不能为空
        }
        if(!$list){
            $data['redcode_error']=$error['redcode_error']+1;
            $data['redcode_time']=time();
            // 更新错误尝试时间
            $UserErrorTry->where(array('user_id'=>$this->_user['id']))->save($data);
            $array['error_try'] = 10-$data['redcode_error'];         
            $this->returnMsg(2,'redcode',$array); // 兑换码错误
        }elseif( $list['status'] == 2){
            $data['redcode_error']=0;
            $UserErrorTry->where(array('user_id'=>$this->_user['id']))->save($data);
            $this->returnMsg(3,'redcode'); // 该兑换码已被兑换
        }else{
            $data['redcode_error']=0;
            $UserErrorTry->where(array('user_id'=>$this->_user['id']))->save($data);
            $uid = $this->_user['id'];
            // 奖品的类型 1为门票，2为砖石，3为点券
            if( $list['type'] == 1){
                $types ='entrance_ticket';
            }elseif( $list['type'] == 2){
                $types ='diamond';
            }else{
                $types ='gold';
            }
            // 更改用户金额
            $res = M('UserUser')->where(array('id' => $uid))->setInc($types,$list['prize']);
            if($res){
                $result = $Redcode->where(array('id' => $list['id']))->save(array('status'=>2,'user_id'=>$uid,'updatetime'=>time()));
                M('AwardRedcodeBonus')->where('id = '.$list['bonus_id'])->setDec('has_nums',1);
                unset($list['codes']);
                unset($list['status']);
                if ($result) {
                    // 帐变记录
                    $this->insert_account(12,$list['type'],$uid,$list['prize'],true);
                }
                $this->returnMsg(0,'redcode',$list);// 兑换成功
            }
        }
    }
    public function check_redcode(){
        $error_try = session('error_try'); // 获取错误尝试次数
        if($error_try > 10){ // 错误尝试次数暂定10次
            $time = time()-session('error_time');
            if($time<1800){
                $data['time'] = 1800 - $time; //还需等待的秒数
                $this->returnMsg(5,'redcode',$data); // 错误尝试次数过多
            }else{
                $_SESSION['error_try']=0;// 错误尝试次数清零
            }
        }
        $Redcode = M('AwardRedcode');
        $codes = $this->_data['codes'];
        if($codes){
            $map['codes'] = $codes;
            $list = $Redcode->field('id,codes,type,status,prize,bonus_id')->where($map)->find();
        }else{
            $this->returnMsg(1,'redcode'); // 兑换码不能为空
        }
        if(!$list){
            $error_try++;
            session('error_try',$error_try);// 每错误尝试一次就加一
            if(time()-session('error_time')>=1800){
                session('error_try',0);// 错误尝试次数清零
            }
            session('error_time',time());// 每次把最后错误尝试的时间存入session中
            $this->returnMsg(2,'redcode'); // 兑换码错误
        }elseif( $list['status'] == 2){
            session('error_try',0);// 错误尝试次数清零
            $this->returnMsg(3,'redcode'); // 该兑换码已被兑换
        }else{
            session('error_try',0);// 错误尝试次数清零
            $uid = $this->_user['id'];
            // 奖品的类型 1为门票，2为砖石，3为点券
            if( $list['type'] == 1){
                $types ='entrance_ticket';
            }elseif( $list['type'] == 2){
                $types ='diamond';
            }else{
                $types ='gold';
            }
            // 更改用户金额
            $res = M('UserUser')->where(array('id' => $uid))->setInc($types,$list['prize']);
            if($res){
                $result = $Redcode->where(array('id' => $list['id']))->save(array('status'=>2,'user_id'=>$uid,'updatetime'=>time()));
                M('AwardRedcodeBonus')->where('id = '.$list['bonus_id'])->setDec('has_nums',1);
                unset($list['codes']);
                unset($list['status']);
                if ($result) {
                    // 帐变记录
                    $this->insert_account(12,$list['type'],$uid,$list['prize'],true);
                }
                $this->returnMsg(0,'redcode',$list);// 兑换成功
            }
        }
    }
    // 获取兑换记录
    public function redcode_list(){
        $Redcode = M('AwardRedcode');
        $uid = $this->_user['id'];
        $map = "user_id = $uid and status = 2";
        $list = $Redcode->field('id,type,prize,FROM_UNIXTIME(updatetime,"%Y-%m-%d %H:%i:%S") as updatetime ')->where($map)->select();
        if($list){
            $this->returnMsg(0,'room',$list);// 获取数据成功
        }else{
            $this->returnMsg(4,'redcode');// 暂无兑换记录
        }
    }
    // 大转盘，返回奖品，消耗资源数量
    /**
     *
     */
    public function get_turnplate(){
        $user_error=M('UserErrorTry');
        $Turnplate = M('AwardTurnplate');
        $map['class_id'] = $this->_data['class_id'];
        // 获取奖品信息
        $data = $this->cache('get','turnplate_bonus');
        if (!$data) {
            $data = $Turnplate->field('t1.id,t1.level,t2.type,t2.nums,t2.name,t2.goods_id')->join('as t1 LEFT JOIN '.c('DB_PREFIX').'award_turnplate_bonus t2 on t1.bonus_id = t2.id')->where($map)->select();
            foreach ($data as $key => $value) {
                if ($value['type'] == 4) {
                    $img = M('ShopGoods')->where(array('id'=>$value['goods_id']))->getField('avatar_img');
                    $data[$key]['img'] = $img;
                }else{
                    unset($data[$key]['goods_id']);
                }
            }
            $this->cache('set','turnplate',$data);
        }
        if (!$data) {
            $this->returnMsg(1);
        }
        // 获取消耗资源与数量
        $extra_data = M('award_turnplate_class')->field('cost,type')->where(array('id'=>$this->_data['class_id']))->find();
        $extra_data['black_hand'] = $user_error->where('user_id = '.$this->_user['id'])->getField('black_hand_'.$map['class_id']);
        $this->returnMsg(0,'turnplate',$data,$extra_data);
    }
    //获取转盘中奖名单
    public function turnplate_list(){
        $list = $this->cache('get','turnplate_list');
        if (!$list) {
            $data =  M('AwardTurnplateList')->field('id,user_name,prize_name,prize_num')->order('id desc')->limit(20)->select();
            foreach ($data as $key => $value) {
                $list[floor($key/2)][] = $value;
            }
            $this->cache('set','turnplate_list',$list,300);
        }
        $this->returnMsg(0,'turnplate',$list);
    }
    // 验证用户资源数量是否足够用于抽奖，并返回获得奖品
    public function try_turnplate(){
        $Turnplate = M('AwardTurnplate');
        $user_error=M('UserErrorTry');
        $class_id = $this->_data['class_id'];       
        if ($class_id == 1) {
            $types = 'diamond';   // 要扣除的资源类型
            $money = $this->_user['diamond'];// 用户资源
        }else{
            $class_id = 2;
            $types = 'gold';     // 要扣除的资源类型
            $money = $this->_user['gold'];// 用户资源
        }
        $list = M('award_turnplate_class')->field('cost')->where(array('id'=>$this->_data['class_id']))->find();
        if ($money-$list['cost'] < 0) {
            $this->returnMsg($class_id,'turnplate');// 验证用户资源数量不足够用于抽奖
        }else{
            $map['class_id']=$class_id;
            // 获取转盘数据
            $data = $Turnplate->field('t1.id,t1.chance,t1.level,t2.type,t2.nums,t2.name')->join('as t1 LEFT JOIN '.c('DB_PREFIX').'award_turnplate_bonus t2 on t1.bonus_id = t2.id')->where($map)->select();
            // 用户ID
            $uid = $this->_user['id'];
            // 手气值
            //$black_hand = (int)$this->en_de_crypt('de',cookie(c('HAND_'.$class_id)));
            //$black_hand=session('black_hand_'.$class_id);
            $black_hand = $user_error->where('user_id = '.$this->_user['id'])->getField('black_hand_'.$class_id);
            // 扣除用户资源
            $res = M('UserUser')->where(array('id' => $uid))->setDec($types,$list['cost']);
            if ($res) {
                // 入账变表
                $this->insert_account(5,$class_id+1,$uid,$list['cost']);
                // 获取奖品ID
                if ($black_hand > 0) {
                    foreach ($data as $key => $value) {
                        // 增大中好奖品的概率
                        if ($value['level'] > 3){
                            $value['chance'] -= $black_hand;
                        }else{
                            $value['chance'] += $black_hand;
                        }
                        $data[$key] = $value;
                    }
                }elseif($black_hand >= 20){
                    foreach ($data as $key => $value) {
                        // 必中好奖品的概率
                        if ($value['level'] > 3) {
                            unset($data[$key]);
                        }
                    }
                    $user_error->where('user_id = '.$this->_user['id'])->setField('black_hand_'.$class_id,0);
                    //$memcache->set('black_hand_'.$this->_user['id'].$class_id,0);
                }
                $id = $this->get_turnplate_result($data);
                foreach ($data as $key => $value) {
                    if ($id == $value['id']) {
                        // 获取奖品信息
                        $prize = $value;
                        break;
                    }
                }
                // 判断抽中的奖品类型1,2,3就直接存入账号
                if( $prize['type'] == 1){
                    $types ='entrance_ticket';
                }elseif( $prize['type'] == 2){
                    $types ='diamond';
                }elseif( $prize['type'] == 3){
                    $types ='gold';
                }elseif( $prize['type'] == 4 ){// 如果是商品
                    $is_goods = true;
                    //暂未定还未完成用户中心
                }
                // 更改用户金额
                $result = M('UserUser')->where(array('id' => $uid))->setInc($types,$prize['nums']);
                if ($result) {
                    // 把中奖信息存入中奖信息表中
                    $data['user_name'] = $this->_user['username']; // 用户名
                    $data['prize_name'] = $prize['name'];// 奖品名
                    $data['prize_num'] = $prize['nums'];// 奖品数量
                    $data['addtime'] = time();// 添加时间
                    M('AwardTurnplateList')->add($data);
                    // 入账变表
                    $this->insert_account(11,$prize['type'],$uid,$prize['nums'],true);
                }
                unset($prize['chance']); // 不返回概率值
                // 奖级越高奖品越差
                if ($prize['level'] > 3) {
                    $black_hand++; // 手气值加一
                }else{
                    $black_hand = 0; // 手气值清零
                }
                //$string = $this->en_de_crypt('en',$black_hand);
                //cookie(C('HAND_'.$class_id),$string);
                //session('black_hand_'.$class_id,$black_hand);
                $user_error->where('user_id = '.$this->_user['id'])->setField('black_hand_'.$class_id,$black_hand);
                $this->returnMsg(0,'turnplate',$prize,$black_hand);// 获取抽中奖品信息
            }
        }
    }
    // 获取用户竞猜奖励数据
    public function reward_list(){
        $uid = $this->_user['id'];
        $page = $this->_data['page'];
        if(empty($page)){
            $start = 0;
        }else{
            $start = ($page-1)*10;
        }
        // 获取用户数据存在哪个分表之中
        $table_name = $this->get_hash_table('UserBetGess',$uid);
        $UserBetGess = M($table_name); 
        $ShopGoods = M('ShopGoods');
        // 总记录数
        $count = $UserBetGess->where(array('uid'=>$uid))->count();
        // 页码数
        $lastpage = ceil($count/10);
        // 连表查询 （为获取房间名称）
        $data = $UserBetGess->field('t1.*,t2.name,t2.type_id,t2.match_start_time,t2.prize_goods_id')->join('as t1 left join '.c('DB_PREFIX').'match_room as t2 on t1.room_id = t2.id')->where(array('t1.uid'=>$uid))->order('t2.match_start_time desc')->limit($start,10)->select();
        if ($data) {
            // 输出前数组进行转换
            $Roomtype = $this->getdata('room_type_name_all');
            foreach ($data as $key => $value) {
                if ($value['type'] == 4  && $value['prize_goods_id'] != 0) {
                    $data[$key]['goods_name'] = $ShopGoods->where('id = '.$value['prize_goods_id'])->getfield('name');
                }
                $data[$key]['lineup_score']  = (int)$value['lineup_score']/10; 
                $data[$key]['addtime'] = date('Y-m-d H:i:s',$value['match_start_time']);
                $data[$key]['tag_img'] = $Roomtype[$value['type_id']]['tag_img'];
                $day = date('Y-m-d',$value['match_start_time']);
                if ($value['awardtime'] != 0) {
                    $data[$key]['awardtime'] = date('Y-m-d H:i:s',$value['awardtime']);
                }  
                $list[$day][]=$data[$key]; 
            }

            $this->returnMsg(0,'turnplate',$list,$lastpage);// 获取抽中奖品信息
        }else{
            $this->returnMsg(1,'reward'); // 获取失败
        }
    } 
    // 领取竞猜奖励
    public function get_reward(){
        $uid = $this->_user['id'];
        // 获取用户数据存在哪个分表之中
        $table_name = $this->get_hash_table('UserBetGess',$uid);
        $UserBetGess = M($table_name);
        // 判断传过来的ID是否正确
        $id = $this->_data['id'];
        $data = $UserBetGess->where(array('id' => $id,'uid' => $uid,'status' => 0))->find();
        if(!$data){
            $this->returnMsg(5,'reward');//已经领取过,或者领取信息不存在
        }
        // 判断奖品类型
        if( $data['type'] == 1){ //门票
            $types ='entrance_ticket';
        }elseif( $data['type'] == 2){ //木头
            $types ='gold';
        }elseif( $data['type'] == 4){ //实物

            $awards['type'] = $data['type'];
            $awards['nums'] = $data['nums'];
            $UserBetGess->save(array('id'=>$data['id'],'status'=>1,'awardtime'=>time())); //更改实物领取的状态
            $this->returnMsg(0,'reward');//实物领取请联系客服
        }
        // 更改用户金额
        $result = M('UserUser')->where(array('id' => $uid))->setInc($types,$data['nums']);
        if ($result) {
            // 更改用户竞猜奖励数据
            $UserBetGess->save(array('id'=>$data['id'],'status'=>1,'awardtime'=>time()));
            // 入账变表
            $this->insert_account(4,$data['type'],$uid,$data['nums'],true);
        }

        $awards['type'] = $data['type'];

        $awards['nums'] = $data['nums'];

        $this->returnMsg(0,'reward',$awards); // 领取成功
    }

    // 一键领取所有竞猜奖励
    public function get_reward_all(){
        $uid = $this->_user['id'];
        // 获取用户数据存在哪个分表之中
        $table_name = $this->get_hash_table('UserBetGess',$uid);
        $UserBetGess = M($table_name);
        // 判断传过来的ID是否正确
        $id = $this->_data['id'];
        $Map['uid'] = $uid;
        $Map['status'] = 0;
        // $Map['type'] = array('neq',4); //不查询实物的类型

        $data = $UserBetGess->where(array('uid' => $uid,'status' => 0))->select();

        if(!$data){
            $this->returnMsg(5,'reward');//已经领取过,或者领取信息不存在
        }
        $entrance_ticket = 0; //门票
        $gold = 0;//木头
        $goods = array(); //实物的名称
        foreach ($data as $key => $value) {
            // 判断奖品类型
            if( $value['type'] == 1){ //门票
                $types ='entrance_ticket';
            }elseif( $value['type'] == 2){ //木头
                $types ='gold';
            }else{ //实物
                $this->returnMsg(1);//其他类型奖励,返回错误信息
            }
            if($value['type'] == 4){ //实物领取
                $room_data = M('MatchRoom')->field('prize_goods_id')->where(array('id' => $value['room_id']))->find();

                $goods_data = M('ShopGoods')->field('name')->where(array('id' => $room_data['prize_goods_id']))->find();
                $goods[] = $goods_data['name']; 
                $UserBetGess->save(array('id'=>$value['id'],'status'=>1,'awardtime'=>time()));

                continue;
            }
            // 更改用户金额
            $result = M('UserUser')->where(array('id' => $uid))->setInc($types,$value['nums']);
            if ($result) {
                // 更改用户竞猜奖励数据
                $UserBetGess->save(array('id'=>$value['id'],'status'=>1,'awardtime'=>time()));
                // 入账变表
                $this->insert_account(4,$value['type'],$uid,$value['nums'],true);
                //统计领取的奖励总和
                if( $value['type'] == 1){ //门票
                    $entrance_ticket = $value['nums'];
                }elseif( $value['type'] == 2){ //木头
                    $gold += $value['nums'];
                }
            }
        }
        $awards = array();
        $awards['gold'] = $gold;
        $awards['entrance_ticket'] = $entrance_ticket;
        $awards['goods'] = $goods;

        $this->returnMsg(0,'reward',$awards); // 领取成功
    }
 
    // 转盘算法的实现
    protected function get_turnplate_result($data){
        // 实现概率获取
        $sum = 0;
        foreach($data as $k => $v) {
            $weight = $v['chance'];
            $sum   += $v['chance'];
            for ($i=0; $i <=$weight ; $i++) {
                $temp[] = $v['id'];
            }
        }
        $res = mt_rand(1,$sum);
        return $temp[$res];
    }
}
