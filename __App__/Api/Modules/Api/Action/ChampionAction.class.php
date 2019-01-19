<?php
//冠军猜页面接口
class ChampionAction extends LoginAction{

    private static $champion_id = 1; //定义活动的冠军猜id

		//冠军猜的阵容配置 1(NBA) 2(LOL和DOTA2)
	private $conf = array(
        1 => array(
            'num' => 5,'pay' => 125,'position' => array(1 => '控卫',2 => '分卫',3 =>'小前',4=>'大前',5=>'中锋')
            ),
        2 => array(
            'num' => 5,'pay' => 125,'position' => array(1=>'上单',2=>'打野',3=>'中单',4=>'ADC',5=>'辅助')
            )
    );

	//冠军猜详情
	public function detail(){
		$id = $this->_data['id'] ? $this->_data['id'] : self::$champion_id;// 冠军猜的id
		if(!is_numeric($id)){
			$this->returnMsg(1);
		}

		$Champion = M('Champion');
		$data = $Champion->where(array('id' => $id,'status' => 1))->find();
		if(!$data){
			$this->returnMsg(2,'champion');
		}

		$teams = $this->get_match_team($data['match_id']);//赛事的id,用来获取所有的比赛和球员信息
		if(!$teams){
			$this->returnMsg(3,'champion');
		}
        // print_r($teams);
		$players = $this->get_all_player($data['project_id'],$teams);
		if(!$players){
			$this->returnMsg(4,'champion');
		}
        $all_team = $this->get_all_team($data['project_id']);

        foreach ($players as $key => $value) {
            $players[$key]['team_name'] = $all_team[$value['team_id']]['name'];
            $players[$key]['team_img'] = $all_team[$value['team_id']]['img'];
        }

        if($data['match_end_time'] > time()){
            $data['is_end'] = 2;
        }else{
            $data['is_end'] = 1;
        }


        // $_data['team'] = $all_team;
		$_data['detail'] = $data;
		$_data['players'] = $players;

		$this->returnMsg(0,'champion',$_data);

	}

    //获取所有的队伍
    private function get_all_team($project_id){
        $cache_name = 'get_all_team'.$project_id;
        $data = $this->cache('get',$cache_name);
        if(!$data){
            $data = M('MatchTeam')->getField('id,name,e_name,img');
            $this->cache('set',$cache_name,$data,3600*5);
        }
        return $data;
    }

	//获取参加比赛的所有的球员
	private function get_all_player($project_id,$teams){
		$cache_name = 'get_all_player'.md5(json_encode($teams));
		$data = $this->cache('get',$cache_name);
		if(!$data){
			$sql = $this->create_sql($teams);
			if(!$sql){
				return false;
			}
			if($project_id == 4){
				$Model = M('MatchPlayer');
			}elseif($project_id == 5){
				$Model = M('MatchPlayerWcg');
			}elseif($project_id == 6){
				$Model = M('MatchPlayerWcg');
			}else{
				return false;
			}
			$data = $Model->where($sql)->getField('id,name,team_id,img,e_name,salary,position');
            // echo $Model->getLastSql();
			$this->cache('set',$cache_name,$data,3600*5);
		}
		return $data;
	}
	//创建sql语句
	private function create_sql($teams){
		if(!$teams){
			return false;
		}
		$sql = '';
		$teams = array_values($teams);
		foreach ($teams as $key => $value) {
			if($key == (count($teams) - 1)){
				$sql .= 'team_id='.$value;
			}else{
				$sql .= 'team_id='.$value.' or ';
			}	
		}
		return $sql;
	}

	//获取去除重复后所有的队伍id
	//$id 赛事(赛程)类型的id
	private function get_match_team($id){
		$cache_name = 'get_match_team'.$id;
		$teams = $this->cache('get',$cache_name);
		if(!$teams){
			
			$MatchList = M('MatchList');
			$data = $MatchList->where(array('match_name_id' => $id))->select();
			// print_r($data);die;
			if(!$data){
				return false;
			}
			$teams = array();
			foreach ($data as $key => $value) {
				$teams[] = $value['team_a'];
				$teams[] = $value['team_b'];
			}
			$this->cache('set',$cache_name,$teams,3600);
		}
		return array_unique($teams); //返回去除重复的所有队伍
	}

    //投注,竞猜
    public function guess(){
        $id = $this->_data['id'] ? $this->_data['id'] : self::$champion_id; // 冠军猜的id
        $team_info = $this->_data['team_info']; //所选阵容,数组
        $is_share_in = $this->_data['is_share_in']; //是否是分享投注
        if(!is_numeric($id)){
            $this->returnMsg(1);
        }

        $Map['status'] = 1; //发布中
        $Map['settlement_status'] = 1; //未结算
        $data = M('Champion')->where(array('id' => $id))->find();
        if(!$data){ //房间是否存在
            $this->returnMsg(2,'champion');
        }
        if($data['bet_end_time'] < time()){ //竞猜是否截止
            $this->returnMsg(3,'room');
        }

        $UserChampion = M('UserChampion');

        //检测用户是否投注过该房间
        $uid = $this->_user['id'];
        $user_guess = $UserChampion->where(array('uid' => $uid,'champion_id' =>$id))->find();
        if($user_guess){
            $this->returnMsg(6,'champion'); //已经投注过了
        }

		if($data['project_id'] == 4){

			$lineup_data = $this->conf[1];

		}elseif($data['project_id'] == 5){

			$lineup_data = $this->conf[2];

		}elseif($data['project_id'] == 6){

			$lineup_data = $this->conf[2];

		}else{
			$this->returnMsg(1); //项目id不正确,弹出参数错误
		}

        if(count(array_unique($team_info)) != $lineup_data['num']){ //检测人数是否选择正确
            $this->returnMsg(7,'champion');
        }


		$teams = $this->get_match_team($data['match_id']);//赛事的id,用来获取所有的比赛和球员信息
		if(!$teams){
			$this->returnMsg(3,'champion');
		}
		$players = $this->get_all_player($data['project_id'],$teams);
		if(!$players){
			$this->returnMsg(4,'champion');
		}

        $players_id = array_keys($players); //房间所有球员的一维数组

        $salary = 0; //所选球员的工资和
        $lineup_check = array();
        $lineup_team = array(); //存储所选阵容的队伍id
        foreach ($team_info as $key => $value) { //检测所选阵容是否在房间的选手列表中

            $lineup_team[] = $players[$value]['team_id'];//将所选球员的队伍写到数组

            if(!in_array($value, $players_id)){
                $this->returnMsg(1); //检测所选球员是否在房间球员中
            }
            $salary += $players[$value]['salary']; //计算所选球员的工资

            $lineup_check[] = $players[$value]['position'] == $key ? true : false;

        }

        // $team_len = count(array_unique($lineup_team)); //所选阵容队伍长度
        // if($team_len <= 1){ //所选的选手不能是同一个队伍
        //     $this->returnMsg(8,'champion');
        // }
       
        foreach (array_count_values($lineup_team) as $key => $value) {
        	if($value >= 4){
        		$this->returnMsg(8,'champion');
        	}
        }

        //检测工资是否满足配置要求
        if($salary > $lineup_data['pay']){
            $this->returnMsg(9,'champion',$players,$salary);
        }
        //判断选择的整容位置是否满足配置要求
        foreach ($lineup_check as $key => $value) {
            if($value === false){
                $this->returnMsg(10,'champion');
            }
        }

        //参数验证正确,进行投注操作
        $Lineup = M('ChampionLineup');
        // var_dump($Lineup);die;
        $lineup_token = md5(serialize($team_info)); //阵容的md5值

        $lineup_data = $Lineup->where(array('lineup_token' => $lineup_token,'champion_id' => $data['id']))->find(); //检测该阵容是否已经使用过(获取阵容的信息)

        //添加新纪录
        $_data['lineup'] = serialize($team_info);
        $_data['champion_id'] = $data['id'];
        $_data['lineup_token'] = $lineup_token;
        $_data['add_time'] = time();
        $_data['guess_num'] = 1;

        if ($lineup_data) { //用户选取的新阵容存在
            $Lineup->where(array('id' => $lineup_data['id']))->setInc('guess_num',1);
            $result = $lineup_data['id'];
        }else{
            $result = $Lineup->add($_data);
        }
        if(!$result){
            $this->returnMsg(7,'room');
        }
        unset($_data['lineup_token']);
        unset($_data['lineup']);
        unset($_data['guess_num']);
        $UserChampion = M('UserChampion');
        $_data['is_share_in'] = $is_share_in ? 1 : 0;
        $_data['lineup_id'] = $result;
        $_data['uid'] = $this->_user['id'];
        $res = $UserChampion->add($_data); //添加一条记录

        if(!$res){
            $this->returnMsg(11,'champion'); //参数错误
        }
        M('Champion')->where(array('id' => $data['id']))->setInc('guess_num',1);
        // M('UserMoreInfo')->where(array('uid' => $this->_user['id']))->setField('is_guess',1); //投注成功,更新状态
        //中奖信息
        $try_res = $this->try_turnplate();
        if($try_res === false){
            $try_res = $this->try_turnplate();
        }
        if($try_res['type'] == 1){
            $field = 'entrance_ticket';
        }
        if($try_res['type'] == 2){
            $field = 'diamond';
        }
        if($try_res['type'] == 3){
            $field = 'gold';
        }
        $list = M('UserPriceList');
        // 必得的10到100门票
            // 入记录表
        $datas['uid']  = $this->_user['id'];
        $datas['type'] =  1;
        $datas['status'] =  1;
        $datas['num']  = mt_rand(10,100);
        $datas['add_time']  = time();
        $res = $list->add($datas);
        M('UserUser')->where(array('id' => $uid))->setInc($field,$datas['num']);
        //中奖信息
        $this->returnMsg(0,'champion'); //投注成功
    }

    // 抽奖
    private function try_turnplate(){
        $Turnplate = M('AwardTurnplate');
        $bonus = M('AwardTurnplateBonus');
        $list = M('UserPriceList');
        $class_id = 3;       
        $map['class_id']=$class_id;
        $map['t2.nums']=array(array('GT',0));
        // 获取转盘数据
        $data = $Turnplate->field('t1.id,t1.chance,t1.level,t2.type,t2.nums,t2.name,t2.goods_id')->join('as t1 LEFT JOIN '.c('DB_PREFIX').'award_turnplate_bonus t2 on t1.bonus_id = t2.id')->where($map)->select();
        // 用户ID
        $uid = $this->_user['id'];

        $id = $this->get_turnplate_result($data);
        foreach ($data as $key => $value) {
            if ($id == $value['id']) {
                // 获取奖品信息
                $prize = $value;
                break;
            }
        }
        if( $prize['type'] == 4 ){// 如果是商品
        	// 商品默认为1
        	$prize['nums'] = 1;
           // award_turnplate_bonus表数量减一
           $bonus->where('goods_id ='.$prize['goods_id'])->setDec('nums',1);

        }
       	// 入记录表
       	$datas['uid']  = $this->_user['id'];
		$datas['type'] =  $prize['type'];
        if ($prize['type'] == 1) {
            M('UserUser')->where(array('id' => $uid))->setInc('entrance_ticket',$prize['num']);
            $datas['status'] = 1;
        }
		$datas['num']  = $prize['nums'];
		$datas['goods_id']  = $prize['goods_id'];
		$datas['add_time']  = time();
		$res =$list->add($datas);
       	if ($res) {
       		return $datas;
       	}else{
       		return false;
       	}
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
    // 获取积分红包
    public function get_red_packet($wx_openid = false,$is_return = false){
        $open_info = M('UserOpenInfo');
        $more_info = M('UserMoreInfo');
        $where['wx_openid'] = $this->_data['uid'];// 分享者微信uid
        if($wx_openid !== false){
            $where['wx_openid'] = $wx_openid;// 分享者微信uid
        }
        
        $uid = $open_info->where($where)->find();// user_id
        if (!$uid) {
            $this->returnMsg(8,'user');// 参数异常
        }
        $time1 = $more_info->where('uid ='.$this->_user['id'])->find;// 自己的剩余领取次数
        if ($time1['prize_num'] >= 5) {
            $this->returnMsg(13,'champion');// 已达到领取上限
        }
        //用户投注检测
        if ($time1['is_guess'] != 1) {
            // $this->returnMsg(15,'champion');// 用户没有投注
        }

        $time = $more_info->where('uid ='.$uid['user_id'])->find;// 分享者的剩余分享次数
        if ($time['share_num'] >= 5) {
            $this->returnMsg(12,'champion');// 积分红包已被领完
        }
        if ($time['is_guess'] != 1) {
            // $this->returnMsg(14,'champion');// 分享者没有投注
        }
        // 剩余积分
        $red = M('RedPacketRecord');
        $red_data = $red->where(array('s_uid' => $uid['user_id']))->select();

        $map['s_uid'] = $uid['user_id'];
        $map['g_uid'] = $this->_user['id'];
        $check = $red->where($map)->find();
        if ($check) {
            $this->returnMsg(16,'champion');// 您已领取过该积分红包
        }
        //定义红包的最大值和最小值
        $max_red_packet = 1000;
        $first_red_packet = 550;
        // $min_red_packet = 1;
        $min_red_packet = 50;
        if(!$red_data){
            // 第一个人领取
            $score = mt_rand($min_red_packet,$first_red_packet);// 积分
            $num = 1;// 领取次数为1
        }else{
            $last_score = 0;
            foreach ($red_data as $key => $value) {
                $last_score += $value['score'];
            }

            $s_red_num = 5 - count($red_data);

            if (count($red_data) == 4) {
                $score = $max_red_packet - $last_score;// 积分
            }else{
                $have_score = $max_red_packet - $last_score -  $s_red_num*50;
                $score = mt_rand($min_red_packet,$have_score);// 积分
            }
            $num = count($red_data) + 1;// 领取次数
        }
        // print_r($red_data);
        // echo $score;die;

        // echo $wx_openid;
        // print_r($this->_user);
        // print_r($datas);die;

        $datas['score'] = $score;
        $datas['g_uid'] = $this->_user['id'];
        $datas['s_uid'] = $uid['user_id'];
        $datas['time'] = time();
        $res = $red->add($datas); //添加一条红包领取记录

        //分享者红包剩余积分
        $more_info->where(array('uid' => $uid['user_id']))->setDec('share_score',$score);
        //分享者红包领取次数
        $more_info->where(array('uid' => $uid['user_id']))->setInc('share_num',1); 
        //领取者领取次数
        $more_info->where('uid ='.$this->_user['id'])->setField('prize_num',$num);// 红包被领取次数
        if($is_return === true){
            return $score/100;
        }
        $this->returnMsg(0,'reward',$score/100);// 领取成功
    }

    // 获取积分红包.return用
    public function get_red_packet_a($wx_openid = false,$is_return = false){
        $open_info = M('UserOpenInfo');
        $more_info = M('UserMoreInfo');

        $where['wx_openid'] = $wx_openid;// 分享者微信uid

        
        $uid = $open_info->where($where)->find();// user_id
        if (!$uid) {
            return array('error' => 1,'msg' => '无法获取用户信息');
        }
        $time1 = $more_info->where('uid ='.$this->_user['id'])->find();// 自己的剩余领取次数
        if ($time1['prize_num'] >= 5) {
            // $this->returnMsg(13,'champion');// 已达到领取上限
            return array('error' => 1,'msg' => '已达到领取上限');
        }

        $time = $more_info->where('uid ='.$uid['user_id'])->find();// 分享者的剩余分享次数
        if ($time['share_num'] >= 5) {
            // $this->returnMsg(12,'champion');// 积分红包已被领完
            return array('error' => 1,'msg' => '积分红包已被领完');
        }

        // 剩余积分
        $red = M('RedPacketRecord');
        $red_data = $red->where(array('s_uid' => $uid['user_id']))->select();

        $map['s_uid'] = $uid['user_id'];
        $map['g_uid'] = $this->_user['id'];
        $check = $red->where($map)->find();
        if ($check) {
            // $this->returnMsg(16,'champion');// 您已领取过该积分红包
            return array('error' => 1,'msg' => '您已领取过该积分红包');
        }
        //定义红包的最大值和最小值
        $max_red_packet = 500; //防止一次性领完
        $max_red_packet_t = 1000;
        $min_red_packet = 50;

        if(!$red_data){
            // 第一个人领取
            $score = mt_rand($min_red_packet,$max_red_packet);// 积分
            $num = 1;// 领取次数为1
        }else{
            $last_score = 0;
            foreach ($red_data as $key => $value) {
                $last_score += $value['score'];
            }
            $s_num = 5 - count($red_data);
            if (count($red_data) == 4) {
                $score = $max_red_packet_t - $last_score;// 积分
            }else{
                $have_score = $max_red_packet_t - $last_score - $s_num*50;
                $score = mt_rand($min_red_packet,$have_score);// 积分
            }
            $num = count($red_data) + 1;// 领取次数
        }

        $datas['score'] = $score;
        $datas['g_uid'] = $this->_user['id'];
        $datas['s_uid'] = $uid['user_id'];
        $datas['time'] = time();
        $res = $red->add($datas); //添加一条红包领取记录

        //分享者红包剩余积分
        $more_info->where(array('uid' => $uid['user_id']))->setDec('share_score',$score);
        //分享者红包领取次数
        $more_info->where(array('uid' => $uid['user_id']))->setInc('share_num',1); // 红包被领取次数
        //领取者领取次数
        $more_info->where('uid ='.$this->_user['id'])->setInc('prize_num',1);// 领取红包次数
        if($is_return === true){
            // return $score/100;
            return array('error' => 0,'msg' => $score/100);
        }
        $this->returnMsg(0,'reward',$score/100);// 领取成功
    }


    // 奖品信息
    public function award_show(){
        $uid = $this->_user['id'];
        $list = M('UserPriceList');
        $goods = M('ShopGoods');
        $data = $list->where('uid = '.$uid)->select();
        $sum = 0;
        foreach ($data as $key => $value) {
            if ($value['goods_id']) {
                $data[$key]['goods'] = $goods->field('name,avatar_img,is_virtual')->where('id ='.$value['goods_id'])->find();
            }
            if ($value['price_type'] == 1) {
                if ($value['type'] == 1) {
                    $sum += $value['num'];
                    unset($data[$key]);
                }
            }
            unset($data[$key]['add_time']);
        }
        foreach ($data as $k => $v) {
            if ($v['price_type'] == 1) {
                $data[$k]['sum'] = $sum;
            }
        }

        $this->returnMsg(0,'room',$data);
    }
    // 领取奖励
    public function get_award(){
        $list = M('UserPriceList');
        $goods = M('ShopGoods');
        $uid = $this->_user['id'];
        $goods_id = $this->_data['goods_id'];
        $phone = $this->_data['phone'];
        $price_type = $this->_data['price_type'];
        if ($price_type == 1) {
            if (time() > 1502639999) {
                $this->returnMsg(1,'reg'); // 已过领取时间
            }
        }else{
            if (time()> 1505232000) {
                $this->returnMsg(1,'reg'); // 已过领取时间
            }
        }
        // 判断有没有发送过短信，且通过短信验证
        if(!preg_match("/^1[34578]\d{9}$/", $phone)){
            $this->returnMsg(7,'user');// 请正确输入手机号
        }
        $map1['status'] = 1;
        $map1['todo'] = 4;
        $map1['phone'] = $phone;
        $check1 = M('AdminSms')->where($map1)->find();
        if (!$check1) {
            // 未通过短信验证
            $this->returnMsg(1,'reg');
        }
        $maps['phone'] = $phone;
        $maps['price_type'] = $price_type;
        // 判断手机是否已经使用过
        $checks = $list->where($maps)->find();
        if($checks){
            $this->returnMsg(4,'reward'); // 该手机已经领过该奖项
        }
        $map['uid'] = $uid;
        $map['goods_id'] = $goods_id;
        $map['price_type'] = $price_type;
        // 判断商品id是否为用户获得的id
        $check = $list->where($map)->find();
        if (!$check) {
            $this->returnMsg(2,'reward'); // 参数错误
        }else{
            // 判断商品是否为虚拟商品
            $is_virtual  = $goods->where('id ='.$goods_id)->getField('is_virtual');
            $price  = $goods->where('id ='.$goods_id)->getField('price');
            if ($is_virtual == 1) {
                // 查看空余的激活码数量
                $ids = M('ShopVirtualCode')->field('id')->where(array('virtual_id'=>$goods_id,'user_id'=>0))->limit(1)->select();
                if ($ids) {
                    M('ShopVirtualCode')->startTrans();
                    foreach($ids as $k => $v) {
                        $datas['user_id']=$this->_user['id'];
                        $datas['updatetime']=time();
                        $res = M('ShopVirtualCode')->where('id = '.$v['id'])->save($datas);
                        $temp[] = $v['id']; 
                        if ($res) {
                            M('ShopVirtualCode')->commit();
                        }else{
                            M('ShopVirtualCode')->rollback();
                        }
                    }
                }else{
                    $this->returnMsg(7,'buy'); // 商品库存不足
                }
            }else{
                $ShopUserInfo = M('ShopUserInfo');
                $data['user_id']  = $this->_user['id'];      // 用户ID
                $data['name']     = $this->_data['name'];    // 收货人姓名
                $data['phone']    = $this->_data['phone'];   // 收货人手机号
                $data['address']  = $this->_data['address']; // 收货人地址
                $data['addtime']  = time();                  // 添加时间
                if (!empty($data['name'])  && !empty($data['address']) ) {
                    // 查出用户所有的收货地址
                    $address_id  = $ShopUserInfo->add($data);
                }else{
                    $this->returnMsg(2,'address'); // 输入数据异常
                }
                $res = M('ShopUserInfo')->field('id')->where(array('id'=>$address_id))->find(); 
                if (!empty($address_id) && $res) {
                    $data['address_id'] = $address_id;// 收货人信息ID
                    $data['status']  = 1; // 默认为未发货
                    $data['goods_type'] = 2; // 商品类型1为虚拟2为实物
                }else{
                    $this->returnMsg(9,'buy'); // 请填写收货人信息
                }
            }
            // 添加手机号和更改状态
            $update['phone'] = $phone;
            $update['status'] = 1;
            $update['price_type'] = $price_type;
            $update['goods_id'] = $goods_id;
            $list->where($map)->save($update);;
            // 商品销售量增加，入订单表
            $data['numbers'] = 'sn_'.date('Ymdhis').mt_rand(0,10000); // 订单编号
            $data['goods_id'] = $goods_id; // 商品ID
            $data['user_id'] = $this->_user['id']; // 用户ID
            $data['goods_type'] = 2; 
            if ($is_virtual == 1) {
                $data['status'] = 2;// 虚拟商品购买默认为已处理
                $data['goods_type'] = 1; 
            }
            $data['goods_nums'] = 1; // 商品数量
            $data['price'] = $price; // 商品总价格
            $data['addtime'] = time(); // 添加时间
            $order_id = M('ShopGoodsOrder')->add($data);
            if ($order_id) {
                $list->where('id ='.$check['id'])->setField('order_id',$order_id);
                // 入订单详情表
                unset($data);
                if ($is_virtual == 1) {
                    $data['order_id'] = $order_id;// 订单编号
                    $data['code_id'] = implode(',',$temp);// 激活码编号ID
                    $data['user_id'] = $this->_user['id'];
                    $data['nums'] = 1;// 数量
                    $res = M('VirtualOrderInfo')->add($data);
                }else{
                    // 库存表数量减少
                    M('ShopGoodsProduct')->field('nums')->where(array('goods_id'=>$goods_id,'attr_value'=>$value))->setDec('nums',1);
                    $data['order_id'] = $order_id;// 订单编号
                    $data['user_id'] = $this->_user['id'];
                    $data['attribute'] = '默认';// 属性值
                    $data['address_id'] = $address_id;// 收货人信息ID
                    $data['nums'] = 1;// 数量
                    $res = M('ShopPhysicalOrder')->add($data);
                    //商品属性 $value
                }

                $this->returnMsg(0,'turnplate');// 领取成功
            }
        }
    }
    // 订单详细
    public function order_show(){
        $order_id = $this->_data['order_id'];
        //$user_id = isset ( $_GET ['user_id'] ) ? ( int ) $_GET ['user_id'] : 0;
        $map['t1.id'] = $order_id;
        //$map['t1.user_id'] = $user_id;
        $res = M('ShopGoodsOrder')->field('t1.*,t2.name,t2.avatar_img,t2.detail')->join('as t1 left join '.c('DB_PREFIX').'shop_goods as t2 on t1.goods_id = t2.id')->where($map)->find();
        // 判断是否为实物
        if ($res['goods_type'] == 1) {
            // 以下为虚拟商品
            $maps['user_id'] = $res['user_id'];
            $maps['order_id'] = $order_id;
            $list = M('VirtualOrderInfo')->field('code_id,nums')->where($maps)->find();
            if($list){
                $list['name'] = $res['name']; // 商品名称
                $list['avatar_img'] = $res['avatar_img']; // 商品缩略图
                $list['detail'] = $res['detail']; // 商品缩略图
                $ids = explode(',',$list['code_id']);
                foreach ($ids as $key => $value) {
                    $list['codes_list'][] = M('ShopVirtualCode')->field('codes,pwd')->where('id = '.$value)->find();
                }
                unset($list['code_id']);
            }
        }else{
            $list = M('ShopPhysicalOrder')->field('id,order_id,nums,company,track_num,attribute')->where("order_id = $order_id")->find();
            $list['name'] = $res['name']; // 商品名称
            $list['avatar_img'] = $res['avatar_img']; // 商品缩略图
            $list['status'] = $res['status']; // 订单状态
            $list['detail'] = $res['detail']; // 商品缩略图
            $list['addresses'] = M('ShopUserInfo')->where('id ='.$res['address_id'])->find();
        }
        if ($list) {
            $this->returnMsg(0,'turnplate',$list);
        }else{
            $this->returnMsg(1,'reward');
        }
    }

    //添加虚拟的用户投注,需要注意投注的时间,和投注的总数目
    public function virtual_guess(){
        exit('停用');
        $uid = I('uid');
        if($uid > 1100){
            exit('投注完成');
        }

        $team_info = $this->tuijian_lineup();

        $this->guess_a($team_info,$uid);

        sleep(3);
        // echo '111';die;
        $uid = $uid + 1;
        echo '<script>';
        echo 'document.location.href="http://api.aifamu.com/index.php?g=api&m=champion&a=virtual_guess&user_token=agdashasdfeq&uid='.$uid.'";';
        echo '</script>';
        //1.循环获取用户的信息资料
    }
    //获取随机的推荐的阵容
    public function tuijian_lineup(){
        $Champion = M('Champion');
        $data = $Champion->where(array('id' => 1,'status' => 1))->find();
        $teams = $this->get_match_team($data['match_id']);//赛事的id,用来获取所有的比赛和球员信息
        $players = $this->get_all_player($data['project_id'],$teams);

        $cache_name = 'tuijian_lineup';
        $lineup_array = $this->cache('get',$cache_name);
        if(!$lineup_array){
            $player_position = array(); // 存储球员位置的
            foreach ($players as $key => $value) { //把所有的选手的阵容位置分割出来
                if($value['position'] == 1){
                    $player_position[1]['salary'][] = $value['salary'];
                    $player_position[1]['players'][] = $value['id'];
                }
                if($value['position'] == 2){
                    $player_position[2]['salary'][] = $value['salary'];
                    $player_position[2]['players'][] = $value['id'];
                }
                if($value['position'] == 3){
                    $player_position[3]['salary'][] = $value['salary'];
                    $player_position[3]['players'][] = $value['id'];
                }
                if($value['position'] == 4){
                    $player_position[4]['salary'][] = $value['salary'];
                    $player_position[4]['players'][] = $value['id'];
                }
                if($value['position'] == 5){
                    $player_position[5]['salary'][] = $value['salary'];
                    $player_position[5]['players'][] = $value['id'];
                }
            }

            // print_r($player_position);die;

            $lineup_array = array(); //存储所选阵容的
            //计算排列组合数目 , 效率很低 , 加缓存
            foreach ($player_position[1]['salary'] as $key1 => $value1) {
                foreach ($player_position[2]['salary'] as $key2 => $value2) {
                    foreach ($player_position[3]['salary'] as $key3 => $value3) {
                        foreach ($player_position[4]['salary'] as $key4 => $value4) {
                            foreach ($player_position[5]['salary'] as $key5 => $value5) {
                                $salary_sum = $value1 + $value2 + $value3 + $value4 + $value5;

                                if($salary_sum == 125){
                                    // echo $salary_sum,'<br />';
                                    $lineup_array[] = array($player_position[1]['players'][$key1],$player_position[2]['players'][$key2],$player_position[3]['players'][$key3],$player_position[4]['players'][$key4],$player_position[5]['players'][$key5]);
                                }
                            }
                        }
                    }
                }
            }
            // echo 123456;
            $this->cache('set',$cache_name,$lineup_array);
        }

        $lineup_id = rand(0,count($lineup_array) - 1);
        // echo ;
        $lineup_data_a = $lineup_array[$lineup_id];
        $a = array();
        foreach ($lineup_data_a as $key => $value) {
            $a[$key+1] = $value;
        }
        // print_r($a);
        return $a;
    }
    //投注,竞猜,后台使用,去吃时间判断
    public function guess_a($team_info,$uid){
        $id = $this->_data['id'] ? $this->_data['id'] : self::$champion_id; // 冠军猜的id
        $team_info = $team_info; //所选阵容,数组
        $is_share_in = $this->_data['is_share_in']; //是否是分享投注

        $Map['status'] = 1; //发布中
        $Map['settlement_status'] = 1; //未结算
        $data = M('Champion')->where(array('id' => $id))->find();
        if(!$data){ //房间是否存在
            $this->returnMsg(2,'champion');
        }
        // if($data['bet_end_time'] < time()){ //竞猜是否截止
        //     $this->returnMsg(3,'room');
        // }

        $UserChampion = M('UserChampion');

        //检测用户是否投注过该房间
        // $uid = $uid;
        $user_guess = $UserChampion->where(array('uid' => $uid,'champion_id' =>$id))->find();
        if($user_guess){
            $this->returnMsg(6,'champion'); //已经投注过了
        }

        if($data['project_id'] == 4){

            $lineup_data = $this->conf[1];

        }elseif($data['project_id'] == 5){

            $lineup_data = $this->conf[2];

        }elseif($data['project_id'] == 6){

            $lineup_data = $this->conf[2];

        }else{
            $this->returnMsg(1); //项目id不正确,弹出参数错误
        }

        if(count(array_unique($team_info)) != $lineup_data['num']){ //检测人数是否选择正确
            $this->returnMsg(7,'champion');
        }


        $teams = $this->get_match_team($data['match_id']);//赛事的id,用来获取所有的比赛和球员信息
        if(!$teams){
            $this->returnMsg(3,'champion');
        }
        $players = $this->get_all_player($data['project_id'],$teams);
        if(!$players){
            $this->returnMsg(4,'champion');
        }

        $players_id = array_keys($players); //房间所有球员的一维数组

        $salary = 0; //所选球员的工资和
        $lineup_check = array();
        $lineup_team = array(); //存储所选阵容的队伍id
        foreach ($team_info as $key => $value) { //检测所选阵容是否在房间的选手列表中

            $lineup_team[] = $players[$value]['team_id'];//将所选球员的队伍写到数组

            if(!in_array($value, $players_id)){
                $this->returnMsg(1); //检测所选球员是否在房间球员中
            }
            $salary += $players[$value]['salary']; //计算所选球员的工资

            $lineup_check[] = $players[$value]['position'] == $key ? true : false;

        }

        // $team_len = count(array_unique($lineup_team)); //所选阵容队伍长度
        // if($team_len <= 1){ //所选的选手不能是同一个队伍
        //     $this->returnMsg(8,'champion');
        // }
       
        foreach (array_count_values($lineup_team) as $key => $value) {
            if($value >= 4){
                $this->returnMsg(8,'champion');
            }
        }

        //检测工资是否满足配置要求
        if($salary > $lineup_data['pay']){
            $this->returnMsg(9,'champion',$players,$salary);
        }
        //判断选择的整容位置是否满足配置要求
        foreach ($lineup_check as $key => $value) {
            if($value === false){
                $this->returnMsg(10,'champion');
            }
        }

        //参数验证正确,进行投注操作
        $Lineup = M('ChampionLineup');
        // var_dump($Lineup);die;
        $lineup_token = md5(serialize($team_info)); //阵容的md5值

        $lineup_data = $Lineup->where(array('lineup_token' => $lineup_token,'champion_id' => $data['id']))->find(); //检测该阵容是否已经使用过(获取阵容的信息)

        //添加新纪录
        $_data['lineup'] = serialize($team_info);
        $_data['champion_id'] = $data['id'];
        $_data['lineup_token'] = $lineup_token;
        $_data['add_time'] = time();
        $_data['guess_num'] = 1;

        if ($lineup_data) { //用户选取的新阵容存在
            $Lineup->where(array('id' => $lineup_data['id']))->setInc('guess_num',1);
            $result = $lineup_data['id'];
        }else{
            $result = $Lineup->add($_data);
        }
        if(!$result){
            $this->returnMsg(7,'room');
        }
        unset($_data['lineup_token']);
        unset($_data['lineup']);
        unset($_data['guess_num']);
        $UserChampion = M('UserChampion');
        $_data['is_share_in'] = $is_share_in ? 1 : 0;
        $_data['lineup_id'] = $result;
        $_data['uid'] = $uid;
        $res = $UserChampion->add($_data); //添加一条记录

        if(!$res){
            $this->returnMsg(11,'champion'); //参数错误
        }
        M('Champion')->where(array('id' => $data['id']))->setInc('guess_num',1);
        // M('UserMoreInfo')->where(array('uid' => $this->_user['id']))->setField('is_guess',1); //投注成功,更新状态
        //中奖信息
        $try_res = $this->try_turnplate();
        if($try_res === false){
            $try_res = $this->try_turnplate();
        }
        if($try_res['type'] == 1){
            $field = 'entrance_ticket';
        }
        if($try_res['type'] == 2){
            $field = 'diamond';
        }
        if($try_res['type'] == 3){
            $field = 'gold';
        }
        $list = M('UserPriceList');
        // 必得的10到100门票
            // 入记录表
        $datas['uid']  = $uid;
        $datas['type'] =  1;
        $datas['status'] =  1;
        $datas['num']  = mt_rand(10,100);
        $datas['add_time']  = time();
        $res = $list->add($datas);
        M('UserUser')->where(array('id' => $uid))->setInc($field,$datas['num']);
        //中奖信息
        // $this->returnMsg(0,'champion'); //投注成功
        M('Champion')->where(array('id' => $data['id']))->setDec('virtual_guess_num',1);
        return true;
    }
}