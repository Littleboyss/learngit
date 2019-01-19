<?php
ini_set('max_execution_time', '0');
set_time_limit(0);
/**
 * 公共常用接口
 * @author wangh 2017.2.17
 */
class PublicAction extends CommonAction{

	private static $wxappid = 'wxcb820196a31b4bf5';
	private static $wxappsecret = 'b779850be348a07e3e9816e1f1ca6e44';

	/**
	检查登录的状态
	*/
	public function checklogin(){
		$this->data_count(true); //启动统计
		$user_token = $this->_data['user_token'];
		$result = M('UserUser')->where(array('token' => $user_token))->find();
		if($result){
			$this->returnMsg(0,'login');
		}else{
			$this->returnMsg(1,'login');
		}

	}

	/**
	* 登录
	* @param
	*/
	public function login(){
		$UserUser = M('UserUser');
		/*测试情况下,用id进行不同的登录 start*/
		$uid = $this->_data['uid'];
		if (empty($uid)) {
			$this->returnMsg(5,'user'); // 用户名为空
		}
		//$username = $this->_data['username'];//获取昵称
		$username = $uid;
		$user_data = $UserUser->where(array('username' => $username))->find();

		if(!$user_data){
			$user_data['username'] = $username;
			$user_data['entrance_ticket'] = rand(100,200);
			$user_data['diamond'] = rand(100,300);
			$user_data['gold'] = rand(10000,50000);
			$user_data['rank'] = 1;
			$user_data['phone'] = rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9);
			$user_data['token'] = md5(time().rand(1,99999));
			$res = $UserUser->add($user_data);
			$user_data['id'] = $res;
			if(!$res){
				$this->returnMsg(1); // 参数错误
			}
		}

		//如果查询到用户的数据则更新token
		$token = md5($user_data['id'].time().rand(1,999999));
		$res_token = $UserUser->where(array('id' => $user_data['id']))->setField('token',$token);
		if(!$res_token){
			$this->returnMsg(1); // 参数错误
		}
		$user_data['token'] = $token;



		//获取到用户的信息,进行登录操作,登录采用cookie加密的方式进登录
		$en_string = json_encode(array('id' => $user_data['id'],'username' => $user_data['username'],'phone' => $user_data['phone'])); 
		$string = $this->en_de_crypt('en',$en_string);// 生成加密后的字符串
		if($string === false){
			$this->returnMsg(1,'system');
		}
		cookie(C('LOGIN_STR'),$string); //保存在cookie中
		$user_data['rank_name'] = '出入蓝蓝';
		$user_data['avatar_img'] =  C('AVATAR_IMG').$user_data['id'];
		/*
		 * 签到部分开始
		 * @author chengy 2017.03.10
		 * */
		session('error_try',0); // 错误尝试次数初始化
		// $string = $this->en_de_crypt('en','0');// 生成加密后的字符串
		// cookie(C('HAND_1'),$string); //保存在cookie中
		// cookie(C('HAND_2'),$string); //保存在cookie中
		//session('black_hand_1',0);
		//session('black_hand_2',0);
		$attendance = M('AwardAttendance');
		$uid = $user_data['id'];

		$attend_data = $attendance->field('id,attend_day,user_id,add_time')->where(array('user_id' => $uid))->find();
		//var_dump($attend_data);
		if(!$attend_data){// 第一次签到,插入操作,并给用户表entrance_ticket字段加10操作
			$data['attend_day']=1; // 签到天数
			$data['user_id']=$uid; // 用户ID
			$data['add_time']=time(); // 最后更新时间
			$result = $attendance->add($data);
		}else{
			// 判断是否是今天的第一次登陆，是就更新签到天数与门票数
			$add_day = strtotime(date('Ymd',$attend_data['add_time']));
			// 当天开始天数时间的时间戳
			$now_day = strtotime(date("Ymd"));
			// 判断签到时间与签到天数
			if ($add_day<$now_day && $attend_data['attend_day'] <= 29 ) {
				$data['attend_day'] = $attend_data['attend_day']+1;
				$data['add_time'] = time();
				$res = $attendance->where(array('user_id' => $uid))->save($data);// 签到天数加1,更新签到时间
			}
		}
		$this->activity($uid); // 活动
		$user_data = $this->login_action($user_data);// 登录后的数据处理

		/*签到部分结束*/
		$this->returnMsg(0,'user',$user_data);

	}
	// 登陆
	public function login_do(){  
        // 账号  
        $map['phone']=$this->_data['phone'];  
        // 密码  
        $password = md5($this->_data['pwd'].c('LOGIN_KEY'));// 密码加盐加密
        $user=M('UserUser'); // 用户表
        $UserErrorTry=M('UserErrorTry'); // 用户表
        // 判断手机号是否存在
        $list = $user->where($map)->find();
        //var_dump($list);exit;
        if (!$list) {
        	$this->returnMsg(7,'user');
        }else{
        	// 获取错误尝试的次数，最后尝试的时间
        	$error = $UserErrorTry->field('user_error,user_time')->where(array('user_id'=>$list['id']))->find();
        	if ($error['user_error'] >= 9) {
        		$time = time()-$error['user_time'];
	            if($time<1800){
	                $times = 1800 - $time; //还需等待的秒数
	                $this->returnMsg(5,'redcode',array('times'=>$times)); // 错误尝试次数过多
	            }else{
	                $error['user_error']=0;// 错误尝试次数清零
	            }
        	}
        	if ($password != $list['password']) {
        		$data['user_error']=$error['user_error']+1;
        		$data['user_time']=time();
        		// 更新错误尝试时间
        		$UserErrorTry->where(array('user_id'=>$list['id']))->save($data);
        		$array['error_try'] = 10-$data['user_error'];
        		$this->returnMsg(9,'user',$array);// 登陆失败,密码错误
        	}else{
        		// 清空错误尝试次数
        		$data['user_error']=0;
        		$UserErrorTry->where(array('user_id'=>$list['id']))->save($data);
				$user_data = $this->login_action($list);// 登录后的数据处理
				// 删除敏感信息
				unset($user_data['password']);
				unset($user_data['ip']);
				unset($user_data['hide_notice']);
				unset($user_data['notice_state']);
				$this->attendance($list['id']); // 每日签到
				$this->activity($list['id']); // 活动
        		$this->returnMsg(0,'user',$user_data);// 登陆成功
        	}

        }
    }  
    // 验证是否已经登录
    public function check_token(){
    	$map['token']=$this->_data['token'];  
    	$user=M('UserUser'); // 用户表
    	$list = $user->where($map)->find();
    	if ($list) {
    		$list['avatar_img'] = C('AVATAR_IMG').$list['id'];
			$list['token'] = $save['token'];
			$rank = $this->cache('get','rank_name');
			if (!$rank) {
				$rank = M('UserRank')->field('id,name')->select();
				$this->cache('set','rank_name',$rank);
			}
			// 称号不存在,默认为空
			$list['rank_name'] = $rank[$list['rank']-1]['name'] ? $rank[$list['rank']-1]['name'] : '';
			$list['token'] = $map['token'];
			// 删除敏感信息
			unset($list['password']);
			unset($list['ip']);
			unset($list['hide_notice']);
			unset($list['notice_state']);
			$this->attendance($list['id']);
    		$this->returnMsg(0,'user',$list);// 登陆成功
    	}else{
    		$this->returnMsg(1,'user');// 请先登录
    	}
    	
    }
	/*
	 * 签到部分
	 * @author chengy 2017.03.10
	 * */ 
    protected function attendance($uid){
		$attendance = M('AwardAttendance');
		$attend_data = $attendance->field('id,attend_day,user_id,add_time')->where(array('user_id' => $uid))->find();
		if(!$attend_data){// 第一次签到,插入操作
			$data['attend_day']=1; // 签到天数
			$data['user_id']=$uid; // 用户ID
			$data['add_time']=time(); // 最后更新时间
			$result = $attendance->add($data);
		}else{
			// 判断是否是今天的第一次登陆，是就更新签到天数与门票数
			$add_day = strtotime(date('Ymd',$attend_data['add_time']));
			// 当天开始天数时间的时间戳
			$now_day = strtotime(date("Ymd"));
			// 判断签到时间与签到天数
			if ($add_day<$now_day && $attend_data['attend_day'] <= 29 ) {
				$data['attend_day'] = $attend_data['attend_day']+1;
				$data['add_time'] = time();
				$res = $attendance->where(array('user_id' => $uid))->save($data);// 签到天数加1,更新签到时间
				if ($data['attend_day'] == 30) {
					// 大于30天获得称号
					$rank = M('UserRankInfo');
					$rank_data = $rank->where(array('uid' => $uid))->find();
					if (!$rank_data) {
						$rank_data['uid'] = $id;
			        	$rank_data['rank_id'] = 1;
			        	M('UserRankInfo')->add($rank_data);
			        	$rink_id['rank_id'] = '1,2';
					}else{
						$rink_id['rank_id'] = $rank_data['rank_id'].',2';
					}
					$rank->where(array('uid' => $uid))->save($rink_id);
				}
			}
		}
    }
    /*
	 * 连续签到三天活动
	 * @author chengy 2017.06.13
	 * */
    protected function activity($uid){
     	// 判断是否已过签到活动时间
     	if(time() > 1498752000){
     		return false;
     	}
		$attend = M('AwardActivity');
		$attend_data = $attend->field('id,attend_day,user_id,add_time')->where(array('user_id' => $uid))->find();
		if(!$attend_data){// 第一次签到,插入操作
			$data['attend_day']=1; // 签到天数
			$data['user_id']=$uid; // 用户ID
			$data['add_time']=time(); // 最后更新时间
			$result = $attend->add($data);
		}else{
			// 判断是否是今天的第一次登陆，是就更新签到天数与门票数
			$add_day = strtotime(date('Ymd',$attend_data['add_time']));
			// 当天开始天数时间的时间戳
			$now_day = strtotime(date("Ymd"));
			// 判断签到时间与签到天数
			if ($add_day<$now_day && $attend_data['attend_day'] <= 3 ) {
				$data['attend_day'] = $attend_data['attend_day']+1;
				$data['add_time'] = time();
				$res = $attend->where(array('user_id' => $uid))->save($data);// 签到天数加1,更新签到时间
				return true;
			}else{
				return false;
			}
		}
    }
	/*
	 * 登录的相关操作
	 * @author chengy 2017.06.06
	 * */ 
    protected function login_action($list){
    	$user=M('UserUser'); // 用户表
	    // 返回ipv4地址，int
		$save['ip'] = get_client_ip(1);
		// 最后登陆时间
		$save['last_time'] = time();
		$save['token'] = md5(time().$id.mt_rand(1000,9999));// 登陆的token
		// 更新最后登陆时间以及ip
		$user->where(array('id'=>$list['id']))->save($save);
		//获取到用户的信息,进行登录操作,登录采用cookie加密的方式进登录
		$en_string = json_encode(array('id' => $list['id'],'username' => $list['username'],'phone' => $list['phone'])); 
		$string = $this->en_de_crypt('en',$en_string);// 生成加密后的字符串
		if($string === false){
			$this->returnMsg(1,'system');
		}
		cookie(C('LOGIN_STR'),$string,3600); //保存在cookie中
		// 头像
		$list['avatar_img'] = C('AVATAR_IMG').$list['id'];
		$list['token'] = $save['token'];
		$rank = $this->cache('get','rank_name');
		if(!$rank) {
			$rank = M('UserRank')->field('id,name,avatar_img')->select();
			$this->cache('set','rank_name',$rank);
		}
		// 称号不存在,默认为空
		$list['rank_name'] = $rank[$list['rank']-1]['name'] ? $rank[$list['rank']-1]['name'] : '';
		$list['rank_img'] = $rank[$list['rank']-1]['avatar_img'] ? $rank[$list['rank']-1]['avatar_img'] : '';
		return $list;
    }

    // 所有第三方登录
    public function alllogin(){
		$type_con = $this->_post('type_con','trim');
		$arr = array('qq','wx','wb');
		if(!$type_con || !in_array($type_con, $arr)){//检测第三方来源参数
			$this->returnMsg(1);
		}
		//获取openid 和access_token
		$openid = $this->_post('openid','trim');
		$access_token = $this->_post('access_token','trim');

		// file_put_contents('./open.txt',$type_con.'/'.$openid.'/'.$access_token."\r\n",FILE_APPEND);
		if(!$access_token || !$openid){ //检测参数是否正确
			$this->returnMsg(1);
		}

		if($type_con == 'qq'){
			$appid = '101330904';
			$appkey = '41ebce53a80c6713b420b1f17093a942';
	    	$url = 'https://graph.qq.com/user/get_user_info?access_token='.$access_token.'&oauth_consumer_key='.$appid.'&openid='.$openid;
	    	$opentype = 'qq_openid';
	    	if (!$openid) {
	    		$this->returnMsg(8,'user'); // 数据异常
	    	}
	    	$type = 2;
		}elseif($type_con == 'wx'){
			$appid = 'wxe43bb7bc7cb3367d';
			$appkey = 'c7e44ed43c2394173d072e2056f333bd';
	 		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid;
	 		$opentype = 'wx_openid';
	 		$type = 3;

		}elseif($type_con == 'wb'){
			$appid = '1037790589';
			$appkey = '820a6717fe84830a6f0e97696947355e';
			$url = 'https://api.weibo.com/2/users/show.json?access_token='.$accessToken.'&source='.$appid.'&uid='.$openid;
			$opentype = 'wb_openid';
			$type = 4;
		}else{
			$this->returnMsg(8,'user'); // 数据异常
		}
		// 检验openid的真实有效性
		$open_data = file_get_contents($url);
		$Users = json_decode($open_data,true); // 获取用户的资料
		// file_put_contents('./wxjson.txt', $open_data . "\r\n", FILE_APPEND);
		if ($type_con == 'qq') {
			if ($Users['ret'] != 0 ) {
				$this->returnMsg(8,'user'); // 数据异常
			}
			$connect_avatar = $Users['figureurl_qq_2'];
			$connect_username = $Users['nickname'];

		}elseif ($type_con == 'wb') {
			if ($Users != 0 ) {
				$this->returnMsg(8,'user',$Users); // 数据异常
			}
			$connect_avatar = '';
			$connect_username = '';
		}elseif ($type_con == 'wx') {
			if ($Users['unionid'] == '') {
				$this->returnMsg(8,'user',$Users); // 数据异常
			}
			$openid = $Users['unionid'];
			$connect_avatar = $Users['headimgurl'];
			$connect_username = $Users['nickname'];

		}else{
			$connect_avatar = '';
			$connect_username = '';	
		}
		
			
		//根据openid 去查数据库
		$Map[$opentype] = $openid;
		$ConnectLogin = M('UserOpenInfo');
		$connect_q = $ConnectLogin->where($Map)->find();//检测是否存在
		$User=M('UserUser'); // 用户表
		//添加已经登录用户的绑定逻辑 , 用户的token
		$token = $this->_post('token');
		if($token){
			$userInfo = $User->where(array('token'=>$token))->find(); // 获取用户登录的信息
			if($userInfo){
				//检测该第三方号是否被使用过
				if($connect_q){
					if($connect_q['user_id'] != 0){
						$this->returnMsg(3,'bind'); // 该第三方账号已经被使用过了
					}
				}

				//检测用户是否已经绑定过其他第三方平台
				$bindData = $ConnectLogin->where(array('user_id'=>$userInfo['id']))->find();
				if($bindData){
					$bindcheck = $ConnectLogin->where(array('id' => $bindData['id']))->save(array($opentype => $openid));
				}else{
					$bindcheck = $ConnectLogin->add(array('user_id'=>$userInfo['id'],$opentype=>$openid,'add_time'=>time()));
				}

				if($bindcheck){
					$this->returnMsg(0,'bind'); // 绑定成功
				}else{
					$this->returnMsg(2,'bind'); // 绑定失败
				}
			}else{
				$this->returnMsg(2,'bind');
			}
		}
		if($connect_q && $connect_q['user_id'] != 0){//做登录操作
			$userData = $User->where(array('id'=>$connect_q['user_id']))->find();
			if($userData){
				// 签到
				$this->attendance($connect_q['uid']);
				// 登录后的数据处理
				$user_data = $this->login_action($userData);
				// 删除敏感信息
				unset($user_data['password']);
				unset($user_data['ip']);
				unset($user_data['hide_notice']);
				unset($user_data['notice_state']);
				$this->activity($user_data['id']); // 活动
				$this->returnMsg(0,'user',$user_data);// 登陆成功
			}else{
				$this->returnMsg(3,'login'); // 登录失败
			}
		}else{//做绑定或者注册操作
			/* 暂时取消，获取用户的信息没有用
			$data = $this->c_url($url, '', 'GET');
			$qqUser = json_decode($data,true); // 获取用户的资料
			if($qqUser['ret'] != 0){
				$this->returnMsg(4,'login',$qqUser['msg']); //qq 服务器方面的错误
			}
			*/
			if(!$connect_q){//防止 重复添加openid到数据库
				// 注册用户
				$data['type'] = $type;
				$data['add_time'] = time();
		        $data['ip'] = get_client_ip(1);// 返回ipv4地址，int
		        //获取第三方的用户名称
	        	// $data['username'] = $connect_username == '' ? $this->get_rand_username($data['type']) : $connect_username;
	        	$data['username'] = $this->get_rand_username($data['type']);
	        	// file_put_contents('./wxjson2.txt', $data['username'] . "\r\n", FILE_APPEND);
		        $data['entrance_ticket'] = C('USER_MONEY')['entrance_ticket'];// 门票
				$data['diamond'] = C('USER_MONEY')['diamond'];// 砖石
				$data['gold'] = C('USER_MONEY')['gold'];// 木头
		        $id = $User->add($data);
		        // file_put_contents('./sql.txt', $User->getLastSql());
		        if ($id) {
		        	$this->data_count(false,true);
		        	//上传第三方头像
		        	if($connect_avatar != ''){
		        		$this->url_upload($connect_avatar,$id);
		        	}

		        	$token = md5(time().$id.mt_rand(1000,9999));// 登陆的token
		        	$User->where('id = '.$id)->setField('token',$token);
		        	$_data['user_id'] = $id;
		        	$error['user_id'] = $id ;
		        	M('UserErrorTry')->add($error);
		        	$rank_data['uid'] = $id;
		        	$rank_data['rank_id'] = 1;
		        	M('UserRankInfo')->add($rank_data);
					// 将获取的openid 写入到数据库
					$_data[$opentype] = $openid;
					$_data['addtime'] = time();
					$result = $ConnectLogin->add($_data);
						// 签到
					$this->attendance($id);
					// 删除敏感信息
					$user_data = $this->login_action($data);// 登录后的数据处理
					unset($user_data['password']);
					unset($user_data['ip']);
					$this->returnMsg(0,'login',$user_data);// 登陆成功
	        	}
			}
		}
	}

	/*注册*/
	public function register(){
		// 用户注册类型
        $data['type'] = 1;
        $UserUser = M('UserUser');
        $data['add_time'] = time();
        $data['ip'] = get_client_ip(1);// 返回ipv4地址，int
        $data['username'] = $this->get_rand_username( $data['type']);
    	$data['phone'] = $this->_data['phone'] ? $this->_data['phone'] : ''; //注册手机号
    	// 判断有没有发送过短信，且通过短信验证
    	if(!preg_match("/^1[34578]\d{9}$/", $data['phone'])){
			$this->returnMsg(7,'user');// 请正确输入手机号
		}
    	$ten_time = time()-600;
    	$map['status'] = 1;
    	$map['todo'] = 1;
    	$map['phone'] = $data['phone'];
    	$map['sms_time'] =array('gt',$ten_time);
    	$check = M('AdminSms')->where($map)->find();
    	if (!$check) {
    		// 未通过短信验证
    		$this->returnMsg(1,'reg');
    	}
		$res = $UserUser->where(array('phone'=>$data['phone']))->find();
		if ($res) {
			$this->returnMsg(1,'sms');// 你已注册，请登陆
		}
        $pass1 = $this->_data['pass1']; //密码1
		if (!preg_match("/^(?![A-Z]+$)(?![a-z]+$)(?!\d+$)(?![\W_]+$)\S{6,16}$/", $pass1)) {
			$this->returnMsg(9,'user');// 请正确输入密码 
		}
        $pass2 = $this->_data['pass2']; //密码2
        if ( $pass1 != $pass2) {
        	$this->returnMsg(6,'user');// 两次输入的密码不一致 
        }
        $data['password'] = md5($pass1.c('LOGIN_KEY'));// 密码加盐加密
       	// 初始化用户金额
       	$data['entrance_ticket'] = c('USER_MONEY')['entrance_ticket'];// 门票
		$data['diamond'] = c('USER_MONEY')['diamond'];// 砖石
		$data['gold'] = c('USER_MONEY')['gold'];// 木头
        $id = $UserUser->add($data);
        if ($id) {
        	$this->data_count(false,true);//新增用户统计
        	$token = md5(time().$id.mt_rand(1000,9999));// 登陆的token
        	$UserUser->where('id = '.$id)->setField('token',$token);
        	$user=M('UserUser'); // 用户表
	        $UserErrorTry=M('UserErrorTry'); // 用户表
	        // 判断手机号是否存在
	        $list = $user->where('id = '.$id)->find();
        	$error['user_id'] = $id ;
        	M('UserErrorTry')->add($error);
        	$rank_data['uid'] = $id;
        	$rank_data['rank_id'] = 1;
        	M('UserRankInfo')->add($rank_data);
        	$UserErrorTry->where(array('user_id'=>$list['id']))->save($data);
			$user_data = $this->login_action($list);// 登录后的数据处理
			// 删除敏感信息
			unset($user_data['password']);
			unset($user_data['ip']);
			unset($user_data['hide_notice']);
			unset($user_data['notice_state']);
			$this->attendance($list['id']); // 每日签到
			$this->activity($list['id']); // 活动
        	$this->returnMsg(0,'reg',$user_data);// 注册成功 
        }else{
        	$this->returnMsg(10,'user');// 注册失败，请重试 
        }
        
	}
	/**
	 * @param  int  $type      注册类型
	 * @param  integer $length 随机字符串长度
	 * @return string          返回的用户名
	 */
	public function get_rand_username( $type,$length = 8 ) { 
		// 字符集，可任意添加你需要的字符 
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
		$char = ''; 
		for ( $i = 0; $i < $length; $i++ ) { 
			$char .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
		} 
		if ($type == 1) {
			// 手机注册
			$types = 'phone_';
		}elseif($type == 2){
			// QQ注册
			$types = 'QQ_';
		}elseif($type == 3){
			// 微信注册
			$types = 'WChat_';
		}elseif($type == 4){
			// 微博注册
			$types = 'WeiBo';
		}
		$user = $types.$char; // 昵称(唯一的)3-13位
		return $user; 
	} 
	/*忘记密码*/
	public function forget(){
		$UserUser = M('UserUser');
    	$data['phone'] = $this->_data['phone'] ? $this->_data['phone'] : ''; //手机号
    	if(!preg_match("/^1[34578]\d{9}$/", $data['phone'])){
			$this->returnMsg(7,'user');// 请正确输入手机号
		}
		$ten_time = time()-600;
    	$map['status'] = 1;
    	$map['todo'] = 2;
    	$map['phone'] = $data['phone'];
    	$map['sms_time'] =array('gt',$ten_time);
        $check = M('AdminSms')->where($map)->find();
    	if (!$check) {
    		// 未通过短信验证
    		$this->returnMsg(1,'reg');
    	}
		$res = $UserUser->where(array('phone'=>$data['phone']))->find();
        if(!$res){
        	$this->returnMsg(10,'user');// 该手机号不存在 
        }
        $pass1 = $this->_data['pass1']; //密码1
		if (!preg_match("/^(?![A-Z]+$)(?![a-z]+$)(?!\d+$)(?![\W_]+$)\S{6,16}$/", $pass1)) {
			$this->returnMsg(9,'user');// 请正确输入密码 
		}
        $pass2 = $this->_data['pass2']; //密码2
        if ( $pass1 != $pass2) {
        	$this->returnMsg(6,'user');// 两次输入的密码不一致 
        }
        $data['password'] = md5($pass1.c('LOGIN_KEY'));// 密码加盐加密
    	$data['token'] = md5(time().$res['id'].mt_rand(1000,9999));// 登陆的token
    	$data['ip'] = get_client_ip(1);// 返回ipv4地址，int
    	$result = $UserUser->where('id = '.$res['id'])->save($data);
    	if($result){
    		$this->returnMsg(0,'forget');// 更改密码成功 
    	}else{
    		$this->returnMsg(1,'forget');// 更改密码失败
    	}
	}
	/**
	* 清楚前台缓存,后台更新数据后需要更新前台缓存,统一调用此方法
	*/
	public function clearcache(){
		// $cache_names = C('CACHE_DATA');
		// foreach ($cache_names as $key => $value) {
		// 	$this->cache('clear',$value);
		// }

        $path = APP_PATH . 'Runtime/Temp/';
       
        $dh = opendir($path); 
        while ($file = readdir($dh)) { 
            if($file != "." && $file != "..") { 
                $fullpath = $path."/".$file;
                echo $fullpath . '<br />';
                unlink($fullpath); 
            } 
        } 
        closedir($dh); 

	}

	//添加随机工资到球员数据库,测试用
	public function add_rand_salary(){
		exit('已停用');
		$type = $this->_get('type');
		$this->show('1添加球员的随机工资' . '<br />' . '2修改球员的位子' . '<br />' . '3修改所有房间的比赛时间和竞猜时间' .'<br />'. '4添加数据平均分,出场时间,出场次数' . '<br />' . '5修改所有的比赛的比赛是我为一小时后' . '<br />' .'6修改所有的球员的比赛的是为一小时后'. '<br />');
		if($type == 1){
			$players = $this->getdata('player_data_all');
			$MatchPlayer = M('MatchPlayer');
			foreach ($players as $key => $value) {
				$salary = rand(10,50);
				$MatchPlayer->where(array('id' => $key))->setField('salary',$salary);
			}
			echo '添加球员的随机工资';
		}

		if($type == 2){
			$data = $this->getdata('player_data_all',3600*24);
			// print_r($data);
			
			$MatchPlayer = M('MatchPlayer');
			foreach ($data as $key => $value) {
				$MatchPlayer->where(array('id' => $key))->setField('position',rand(1,5));
			}
			echo '修改球员的位子';
		}

		if($type == 3){
			$_data['match_start_time'] = time() + 3600*24;
			$_data['match_end_time'] = time() + 3600*24 - 60;
			if(I('time')){
				$_data['match_start_time'] = time();
				$_data['match_end_time'] = time();
			}

			M('MatchRoom')->where('1')->save($_data);
			echo '修改所有房间的比赛时间和竞猜时间';
		}

		if($type == 4){
			$data = $this->getdata('player_data_all',3600*24);
			// print_r($data);
			
			$MatchPlayer = M('MatchPlayer');
			foreach ($data as $key => $value) {
				$_data['average'] =rand(50,300);
				$_data['play_num'] = rand(20,80);
				$_data['play_time'] = rand(110,350);
				$MatchPlayer->where(array('id' => $key))->save($_data);
			}
			echo '添加数据平均分,出场时间,出场次数';
		}

		if($type == 5){
			$MatchList = M('MatchList');
			$MatchList->where(1)->setField('match_time',time()+3600);
			echo '修改match_list表所有的比赛的比赛是我为一小时后';
		}

		if($type == 6){
			M('PlayerMatchData')->where(1)->setField('match_time',time()+3600);
			echo '修改player_match_data表所有的球员的比赛的是为一小时后';
		}

	}
	//更新比赛
	public function get_start_match(){
		$now_time = time();
		$Map['match_status'] = array(array('eq',1),array('eq',2),'or');
		$Map['match_time'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',$now_time), 'and');
		$MatchList = M('MatchList');
		$data = $MatchList->where($Map)->select();
		// echo $MatchList->getLastSql();
		if(!$data){
			echo '没有查询到比赛数据';die;
		}
		foreach ($data as $key => $value) {
			if($value['match_time'] <= $now_time){ //当现在时间已经过了比赛时间时,开始更新数据
				if($value['match_status'] == 1){
					$MatchList->where('id='.$value['id'])->setField('match_status',2); //更新比赛的状态
				}
				if ($value['project_id'] == 4) {
					echo file_get_contents('http://work.aifamu.com/index.php?g=Bet&m=Update&a=update_match&match_id='.$value['id']).'___分割线___'; //更新比赛数据
				}
				// file_put_contents(, data)
			}
		}
	}
	//后台更新比赛
	public function get_start_match_admin(){
		$start = $this->_data['start'];
		$end = $this->_data['end'];
		$now_time = time();
		$Map['match_status'] = array(array('eq',1),array('eq',2),'or');
		$Map['match_time'] = array(array('egt',strtotime($start)),array('elt',strtotime($end)), 'and');
		$MatchList = M('MatchList');
		$data = $MatchList->where($Map)->select();
		// echo $MatchList->getLastSql();
		if(!$data){
			echo '没有查询到比赛数据';die;
		}
		foreach ($data as $key => $value) {
			if($value['match_time'] <= $now_time){ //当现在时间已经过了比赛时间时,开始更新数据
				if($value['match_status'] == 1){
					$res = $MatchList->where('id='.$value['id'])->setField('match_status',2); //更新比赛的状态
				}
				if ($value['project_id'] == 4) {
					echo file_get_contents('http://work.aifamu.com/index.php?g=Bet&m=Update&a=update_match&match_id='.$value['id']).'___分割线___'; //更新比赛数据
				}
				// file_put_contents(, data)
			}
		}
		echo '更新完成';die;
	}

	/**
	* 数据实时更新计划
	* 1 更新所有房间的所选阵容的所得积分
	*/
	public function updateroomlineup(){
		//条件.塞选今天的比赛,只更新今天的比赛
		$Map['status'] = 1;//发布中
		$Map['settlement_status'] = 1; //未结算
		//查询已经开始了的
		$now_time = time();
		$MatchRoom = M('MatchRoom');
		$Map['project_id'] = 4;//nba
		$Map['match_start_time'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',$now_time), 'and'); //塞选比赛时间,只查询今天开赛的房间
		$data = $MatchRoom->join('as t1 left join '.C('DB_PREFIX').'match_room_info as t2 on t1.id=t2.room_id')->field('id,room_id,match_team')->where($Map)->select();
		// print_r($data);die;
		$matchs = array();
		foreach ($data as $key => $value) {
			if ($now_time < $value['match_start_time'] ) {
				continue;
			}
			$matchs[] = $value['match_team'];
		}
		$matchs = array_unique($matchs);

		$where = $this->or_sql($matchs);
		$Lineup = M('Lineup');
		$lineups = $Lineup->where($where)->select();
		$today_match_data = $this->todaymatchnba($matchs); // 今天的比赛数据
		if(!$today_match_data){
			echo '没有查到比赛数据,请先更新数据';die;
		}
		$UserGuessRecord = M('UserGuessRecord');
		//循环阵容,更新阵容的积分
		foreach ($lineups as $key => $value) {
			$s = unserialize($value['lineup']);
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和
			foreach ($s as $keys => $values) {
				$lineup_score_sum += $this->scorerule($values,$today_match_data[$values]);
				$lineup_total_play_time += $today_match_data[$values]['play_time'];
			}
			$_data['lineup_score'] = $lineup_score_sum*10;
			$_data['total_play_time'] = $lineup_total_play_time;
			$Lineup->where(array('id' => $value['id']))->save($_data); //更新阵容的积分
			$UserGuessRecord->where(array('lineup_id' => $value['id']))->setField('lineup_score',$lineup_score_sum*10); //更新用户所选阵容的积分

			//防止数据累计
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和

		}
		echo '更新完成,时间:' . date('Y-m-d H:i:s');

	}
	/**
	* 数据实时更新计划
	* 1 更新所有房间的所选阵容的所得积分
	*/
	public function updateroomlineuplol(){
		//条件.塞选今天的比赛,只更新今天的比赛
		$Map['status'] = 1;//发布中
		$Map['settlement_status'] = 1; //未结算
		//查询已经开始了的
		$now_time = time();
		$MatchRoom = M('MatchRoom');
		$Map['project_id'] = $this->_data['project_id'];//5为lol，6为dota2
		$Map['match_start_time'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',$now_time), 'and'); //塞选比赛时间,只查询今天开赛的房间
		$data = $MatchRoom->join('as t1 left join '.C('DB_PREFIX').'match_room_info as t2 on t1.id=t2.room_id')->field('id,room_id,match_team')->where($Map)->select();
		$matchs = array();
		foreach ($data as $key => $value) {
			if ($now_time < $value['match_start_time'] ) {
				continue;
			}
			$matchs[] = $value['match_team'];
		}
		$matchs = array_unique($matchs);
		$match_list = implode(',', $matchs);
		$where = $this->or_sql($matchs);
		$Lineup = M('Lineup');
		$lineups = $Lineup->where($where)->select();
		$today_match_data = $this->todaymatch($match_list,5)[5]; // 今天的比赛数据
		// var_dump($where);die;
		
		if(!$today_match_data){
			echo '没有查到比赛数据,请先更新数据';die;
		}
		$UserGuessRecord = M('UserGuessRecord');
		//循环阵容,更新阵容的积分
		foreach ($lineups as $key => $value) {
			$s = unserialize($value['lineup']);
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和
			foreach ($s as $keys => $values) {
				$lineup_score_sum += $today_match_data[$values]['scores'];
			}
			$_data['lineup_score'] = $lineup_score_sum;
			$Lineup->where(array('id' => $value['id']))->save($_data); //更新阵容的积分
			$UserGuessRecord->where(array('lineup_id' => $value['id']))->setField('lineup_score',$lineup_score_sum); //更新用户所选阵容的积分

			//防止数据累计
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和

		}
		echo '更新完成,时间:' . date('Y-m-d H:i:s');
	}
	//拼接or SQL语句
	protected function or_sql($arr){
		if(!is_array($arr) || empty($arr)){
			return '';
		}
		$sql = '';
		foreach ($arr as $key => $value) {
			if($value == '') continue;
			if($key >= 1){
				$sql .= ' or match_id_token=' . "'" . $value . "'";
			}else{
				$sql .= 'match_id_token=' . "'" . $value . "'";
			}
			
		}
		return $sql;
	}

	//更新房间的信息,公共方法,,更新房间的信息,用计划任务执行此方法
	public function updateroom(){
		
		$MatchRoom = M('MatchRoom');
		$Map['status'] = 1;//发布中
		$Map['settlement_status'] = 1; //未结算
		//查询已经开始了的
		$now_time = time();
		$Map['match_start_time'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',$now_time), 'and'); //塞选比赛时间,只查询今天开赛的房间
		$data = $MatchRoom->field('id,reward_id,reward_num,prize_num,open_id,open_num,now_guess_num,is_special,special_uid')->where($Map)->select();
		// print_r($data);
		if(!$data){
			exit('没有查询到比赛的数据');
		}

		$UserGuessRecord = M('UserGuessRecord');
		foreach ($data as $key => $value) {
			//更新比赛的状态为进行中
			$UserGuessRecord->where(array('room_id' => $value['id']))->setField('match_status',2);//data
			//更新房间的用户的排名,和用户获得的奖励
			//更新后再次查询所有用户,根据积分规则进行排序,和设置排名
			sleep(1);// 防止数据没有更新过来导致错误
			$lineup_data_user = $UserGuessRecord->where(array('room_id' => $value['id']))->order('lineup_score desc,add_time asc')->select(); //该房间所有的阵容,按照积分和时间投注进行排序
			
			if(!$lineup_data_user){ //没有投注的用户直接跳过循环
				continue;
			}

			//将用户投注多注的塞选出来
			$s = array();
			foreach ($lineup_data_user as $ku => $vu) {
				
				if($vu['guess_num'] > 1){
					for($i = 1; $i <= $vu['guess_num'];$i++){
						$s[] = $vu;
					}
				}else{
					$s[] = $vu;
				}
			}
			$lineup_data_user = $s;

			//获取房间的中奖配置信息
			if($value['open_id'] == 2){ //满开,检测是否满足开奖条件
				if($value['now_guess_num'] < $value['open_num']){ //没有满足开奖条件,获得的奖励都为0
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
					$is_must_open = false; //是否必开
				}else{
					$is_must_open = true;
				}
			}else{
				$is_must_open = true;
			}

			if($is_must_open == true){

				if($value['reward_id'] == 1){ //每人各的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = $value['reward_num']; //每人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 2){ //均分奖池的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 3){ //获胜均分,主播房
					$special_data = $UserGuessRecord->where(array('uid' => $value['special_uid'],'room_id' =>$value['id']))->find();//获取主播的阵容的积分数据
					$reward_user_num = $UserGuessRecord->where('lineup_score>' . $special_data['lineup_score'].' and room_id='.$value['id'])->count(); //大于主播的玩家数 -> 中奖的人数
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}
				if($value['reward_id'] == 7){
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
				}

				if($value['reward_id'] == 12){ //实物奖品
					$reward = 1; //实物奖励的时候,默认is_reward值为1
					$reward_user_num = $value['prize_num'];
				}

			}

			$j = 1;
			$lineup_data_user_l = $lineup_data_user;
			for ($i=0; $i < count($lineup_data_user); $i++) {

				$lineup_data_user[$i]['ranking'] = $j;
				//用户实际排名获得的奖励 - start
				if(in_array($value['reward_id'], array(4,5,6,8,9,10,11))){ //固定配置奖励
					$lineup_data_user[$i]['reward'] = $this->user_get_reward($value['reward_id'],$j);
				}else{
					if($reward == 0 || $reward_user_num == 0){ //中奖人数/奖品都为0是,获得奖励为0
						$lineup_data_user[$i]['reward'] = 0;
					}else{
						if($j <= $reward_user_num){
							$lineup_data_user[$i]['reward'] = $reward; //获取的奖品数量
						}else{
							$lineup_data_user[$i]['reward'] = 0;
						}
					}
				}
				$j++;
			}

			sleep(1);


			$user_s = array();

			foreach ($lineup_data_user as $kk => $vk) {
				$UserGuessRecord->where(array('id' => $vu['id']))->setField('ranking',$ku + 1);
				$user_s[$vk['uid']][$vk['lineup_id']][] = $vk['reward'];
			}

			$yu = 1;
			for ($i=0; $i < count($lineup_data_user_l); $i++) {

				$lineup_data_user_l[$i]['ranking'] = $yu;

				//用户实际排名获得的奖励 - end
				// if($i == 0){continue;}
				if($lineup_data_user_l[$i]['lineup_score'] == $lineup_data_user_l[$i-1]['lineup_score'] && $lineup_data_user_l[$i]['add_time'] == $lineup_data_user_l[$i-1]['add_time']){
					$lineup_data_user_l[$i]['ranking'] = $lineup_data_user_l[$i-1]['ranking'];
				}
				$yu++;
				$UserGuessRecord->where(array('lineup_id' => $lineup_data_user_l[$i]['lineup_id'],'room_id' => $lineup_data_user_l[$i]['room_id']))->setField('ranking',$lineup_data_user_l[$i]['ranking']); //更新用户的排名


			}


			foreach ($user_s as $k_ => $v_) {
				foreach ($v_ as $_k_ => $_v_) {
					// if(array_sum($_v_) == 0){
					// 	continue;
					// }
					$UserGuessRecord->where(array('room_id' => $value['id'],'lineup_id' => $_k_,'uid' => $k_))->setField('is_reward',array_sum($_v_));
				}
			}
			echo '更新完成,时间:' . date('Y-m-d H:i:s') . '<br />';
		}
	}

	//更新房间的排名,得分,按照积分排名,相同积分的用户均分奖励
	public function updateroom_bak(){
		
		$MatchRoom = M('MatchRoom');
		$Map['status'] = 1;//发布中
		$Map['settlement_status'] = 1; //未结算
		//查询已经开始了的
		$now_time = time();
		$Map['match_start_time'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',$now_time), 'and'); //塞选比赛时间,只查询今天开赛的房间
		$data = $MatchRoom->field('id,reward_id,reward_num,prize_num,open_id,open_num,now_guess_num,is_special,special_uid')->where($Map)->select();
		// print_r($data);
		if(!$data){
			exit('没有查询到比赛的数据');
		}

		$UserGuessRecord = M('UserGuessRecord');
		foreach ($data as $key => $value) {
			//更新比赛的状态为进行中
			$UserGuessRecord->where(array('room_id' => $value['id']))->setField('match_status',2);//data
			//更新房间的用户的排名,和用户获得的奖励
			//更新后再次查询所有用户,根据积分规则进行排序,和设置排名
			sleep(1);// 防止数据没有更新过来导致错误
			$lineup_data_user = $UserGuessRecord->where(array('room_id' => $value['id']))->order('lineup_score desc')->select(); //该房间所有的阵容
			
			if(!$lineup_data_user){ //没有投注的用户直接跳过循环
				continue;
			}

			//将用户投注多注的塞选出来
			$s = array();
			foreach ($lineup_data_user as $ku => $vu) {
				if($vu['guess_num'] > 1){
					for($i = 1; $i <= $vu['guess_num'];$i++){
						$s[] = $vu;
					}
				}else{
					$s[] = $vu;
				}
			}
			$lineup_data_user = $s;

			//获取房间的中奖配置信息
			if($value['open_id'] == 2){ //满开,检测是否满足开奖条件
				if($value['now_guess_num'] < $value['open_num']){ //没有满足开奖条件,获得的奖励都为0
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
					$is_must_open = false; //是否必开
				}else{
					$is_must_open = true;
				}
			}else{
				$is_must_open = true;
			}

			if($is_must_open == true){

				if($value['reward_id'] == 1){ //每人各的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = $value['reward_num']; //每人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 2){ //均分奖池的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 3){ //获胜均分,主播房
					$special_data = $UserGuessRecord->where(array('uid' => $value['special_uid'],'room_id' =>$value['id']))->find();//获取主播的阵容的积分数据
					$reward_user_num = $UserGuessRecord->where('lineup_score>' . $special_data['lineup_score'].' and room_id='.$value['id'])->count(); //大于主播的玩家数 -> 中奖的人数
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}
				if($value['reward_id'] == 7){ //新手练习-
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
				}

				if($value['reward_id'] == 12){ //实物奖品
					$reward = 1; //实物奖励的时候,默认is_reward值为1
					$reward_user_num = $value['prize_num'];
				}

			}

			$j = 1;
			for ($i=0; $i < count($lineup_data_user); $i++) {

				$lineup_data_user[$i]['ranking'] = $j;
				//用户实际排名获得的奖励 - start
				if(in_array($value['reward_id'], array(4,5,6,8,9,10,11))){ //固定配置奖励
					$lineup_data_user[$i]['reward'] = $this->user_get_reward($value['reward_id'],$j);
				}else{
					if($reward == 0 || $reward_user_num == 0){ //中奖人数/奖品都为0是,获得奖励为0
						$lineup_data_user[$i]['reward'] = 0;
					}else{
						if($j <= $reward_user_num){
							$lineup_data_user[$i]['reward'] = $reward; //获取的奖品数量
						}else{
							$lineup_data_user[$i]['reward'] = 0;
						}
					}
				}
				//用户实际排名获得的奖励 - end
				// if($i == 0){continue;}
				if($lineup_data_user[$i]['lineup_score'] == $lineup_data_user[$i-1]['lineup_score']){
					$lineup_data_user[$i]['ranking'] = $lineup_data_user[$i-1]['ranking'];
				}
				$j++;
				$UserGuessRecord->where(array('lineup_id' => $lineup_data_user[$i]['lineup_id'],'room_id' => $lineup_data_user[$i]['room_id']))->setField('ranking',$lineup_data_user[$i]['ranking']); //更新用户的排名
			}

			//die
			sleep(1);

			if($value['reward_id'] == 1 || $value['reward_id'] == 12){//如果奖励规则是每人各的和固定人数奖励,则不进行排名均分
				foreach ($lineup_data_user as $kk => $vk) {
					$UserGuessRecord->where(array('uid' => $vk['uid'],'room_id' => $vk['room_id'],'id'=>$vk['id'],'lineup_id'=>$vk['lineup_id']))->setField('is_reward',$vk['reward']);
					// echo $UserGuessRecord->getLastSql()."\r\n";
				}
				continue;
			}

			
			//更新用户的奖励
			//再次做循环统计相同名次的,取中奖平均值
			$s = array(); //存储用户的奖励
			$k = array(); //存储用户阵容id
			for ($i = 0; $i < count($lineup_data_user); $i++) {
				// if($lineup_data_user[$i]['ranking'] == $lineup_data_user[$i-1]['ranking']){
				// 	$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i-1]['uid']] = $lineup_data_user[$i-1]['reward'];
				// 	$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']] = $lineup_data_user[$i]['reward'];
				// }else{
				// 	$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']] = $lineup_data_user[$i]['reward'];
				// }
				$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']][] = $lineup_data_user[$i]['reward'];
				$k[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']][] = $lineup_data_user[$i]['lineup_id'];
			}

			// //添加用户的奖励到数据库
			foreach ($s as $kq => $vq) {
				$user_guess_sum = 0;
				$user_guess_reward_sum = 0;
				foreach ($vq as $ky => $vy) {
					$user_guess_sum += count($vy); //中奖总次数
					$user_guess_reward_sum += array_sum($vy); //中奖的木头总和
				}
				$reward = floor($user_guess_reward_sum/$user_guess_sum);
				foreach ($vq as $ky => $vy) {
					$UserGuessRecord->where(array('uid' => $ky,'room_id' => $value['id'],'lineup_id'=>$k[$kq][$ky][0]))->setField('is_reward',$reward*count($vy));
				}
			}
			echo '更新完成,时间:' . date('Y-m-d H:i:s') . '<br />';
		}
	}

	/**
	* @param $reward_id 奖励规则的配置信息
	* @param $ranking 名次
	* @return 获取的奖励
	*/
	public function user_get_reward($reward_id,$ranking){
		if($reward_id == 4){
			if($ranking == 1){
				$reward_num = 20000;
			}elseif($ranking == 2){
				$reward_num = 8000;
			}elseif($ranking == 3){
				$reward_num = 4000;
			}elseif($ranking >= 4 && $ranking <= 5){
				$reward_num = 1600;
			}elseif($ranking >= 6 && $ranking <= 10){
				$reward_num = 1160;
			}elseif($ranking >= 11 && $ranking <= 20){
				$reward_num = 750;
			}elseif($ranking >= 21 && $ranking <= 50){
				$reward_num = 550;
			}elseif($ranking >= 51 && $ranking <= 100){
				$reward_num = 300;
			}elseif($ranking >= 101 && $ranking <= 200){
				$reward_num = 200;
			}else{
				$reward_num = 0;
			}
			return $reward_num;
		}elseif($reward_id == 5){
			if($ranking == 1){
				$reward_num = 10000;
			}elseif($ranking == 2){
				$reward_num = 4000;
			}elseif($ranking == 3){
				$reward_num = 2000;
			}elseif($ranking >= 4 && $ranking <= 5){
				$reward_num = 1000;
			}elseif($ranking >= 6 && $ranking <= 10){
				$reward_num = 800;
			}elseif($ranking >= 11 && $ranking <= 20){
				$reward_num = 600;
			}elseif($ranking >= 21 && $ranking <= 50){
				$reward_num = 400;
			}elseif($ranking >= 51 && $ranking <= 100){
				$reward_num = 200;
			}elseif($ranking >= 101 && $ranking <= 200){
				$reward_num = 0;
			}else{
				$reward_num = 0;
			}
			return $reward_num;
		}elseif($reward_id == 6){
			if($ranking == 1){
				$reward_num = 20000;
			}elseif($ranking == 2){
				$reward_num = 8000;
			}elseif($ranking == 3){
				$reward_num = 4000;
			}elseif($ranking >= 4 && $ranking <= 5){
				$reward_num = 2000;
			}elseif($ranking >= 6 && $ranking <= 10){
				$reward_num = 1600;
			}elseif($ranking >= 11 && $ranking <= 20){
				$reward_num = 1200;
			}elseif($ranking >= 21 && $ranking <= 50){
				$reward_num = 800;
			}elseif($ranking >= 51 && $ranking <= 100){
				$reward_num = 400;
			}elseif($ranking >= 101 && $ranking <= 200){
				$reward_num = 0;
			}else{
				$reward_num = 0;
			}
			return $reward_num;
		}elseif($reward_id == 8){
			if($ranking == 1){
				$reward_num = 2000;
			}elseif($ranking == 2){
				$reward_num = 1000;
			}elseif($ranking == 3){
				$reward_num = 800;
			}elseif($ranking >= 4 && $ranking <= 5){
				$reward_num = 600;
			}elseif($ranking >= 6 && $ranking <= 10){
				$reward_num = 400;
			}elseif($ranking >= 11 && $ranking <= 20){
				$reward_num = 200;
			}elseif($ranking >= 21 && $ranking <= 50){
				$reward_num = 100;
			}else{
				$reward_num = 0;
			}
			return $reward_num;
		}elseif($reward_id == 9){
			if($ranking == 1){
				$reward_num = 4000;
			}elseif($ranking == 2){
				$reward_num = 2000;
			}elseif($ranking == 3){
				$reward_num = 1600;
			}elseif($ranking >= 4 && $ranking <= 5){
				$reward_num = 1200;
			}elseif($ranking >= 6 && $ranking <= 10){
				$reward_num = 800;
			}elseif($ranking >= 11 && $ranking <= 20){
				$reward_num = 400;
			}elseif($ranking >= 21 && $ranking <= 50){
				$reward_num = 200;
			}else{
				$reward_num = 0;
			}
			return $reward_num;
		}elseif($reward_id == 10){
			if($ranking == 1){
				$reward_num = 10000;
			}elseif($ranking == 2){
				$reward_num = 4000;
			}elseif($ranking == 3){
				$reward_num = 2000;
			}elseif($ranking >= 4 && $ranking <= 5){
				$reward_num = 1000;
			}elseif($ranking >= 6 && $ranking <= 10){
				$reward_num = 800;
			}elseif($ranking >= 11 && $ranking <= 20){
				$reward_num = 600;
			}elseif($ranking >= 21 && $ranking <= 50){
				$reward_num = 400;
			}elseif($ranking >= 51 && $ranking <= 100){
				$reward_num = 200;
			}else{
				$reward_num = 0;
			}
			return $reward_num;
		}elseif($reward_id == 11){
			if($ranking == 1){
				$reward_num = 20000;
			}elseif($ranking == 2){
				$reward_num = 8000;
			}elseif($ranking == 3){
				$reward_num = 4000;
			}elseif($ranking >= 4 && $ranking <= 5){
				$reward_num = 2000;
			}elseif($ranking >= 6 && $ranking <= 10){
				$reward_num = 1600;
			}elseif($ranking >= 11 && $ranking <= 20){
				$reward_num = 1200;
			}elseif($ranking >= 21 && $ranking <= 50){
				$reward_num = 800;
			}elseif($ranking >= 51 && $ranking <= 100){
				$reward_num = 400;
			}elseif($ranking >= 101 && $ranking <= 200){
				$reward_num = 200;
			}else{
				$reward_num = 0;
			}
			return $reward_num;
		}else{
			return 0;
		}
	}

	//获取今天所有比赛球员的数据
	//$time 时间搓,获取指定天的所有的比赛的数据
	public function todaymatchnba($match_list){
		if ($match_list) {
			$match_list = implode(',', $match_list);
			$match_lists = explode(',',$match_list);
			$match_lists = array_unique($match_lists);
			// $sql = $this->or_sql($match_lists);
			$sql = '';
			foreach ($match_lists as $key => $value) {
				if($value == '') continue;
				if($key >= 1){
					$sql .= ' or match_id=' . "'" . $value . "'";
				}else{
					$sql .= 'match_id=' . "'" . $value . "'";
				}
				
			}
			// 通过项目id判断使用哪个表获取数据
			$PlayerMatchData = M('PlayerMatchData');
			$data = $PlayerMatchData->where($sql)->getField('player_id,get_score,backboard,help_score,hinder_score,cover_score,mistake_score,three_point,is_join,score');
			return $data;
		}else{
			return false;
		}

	}
	public function todaymatch($match_list,$project_id){
		if (!empty($match_list)) {
			$match_lists = explode(',',$match_list);
			foreach ($match_lists as $key => $value) {
				// 通过项目id判断使用哪个表获取数据
				if ($project_id == 4) {
					$PlayerMatchData = M('PlayerMatchData');
				}elseif($project_id == 5){
					$PlayerMatchData = M('PlayerMatchDataLol');
				}elseif($project_id == 6){
					$PlayerMatchData = M('PlayerMatchDataDota2');
				}
				$sql = 'match_id=' . $value;
				$data = $PlayerMatchData->where($sql)->select();
				// echo $PlayerMatchData->getLastSql();die;
				//获取今天所有的比赛选手信息
				foreach ($data as $k => $v) {
					$array[$project_id][$v['player_id']] = $v;
				}
			}
			// print_r($data);
			// print_r($array);
			return $array;
		}
	}

	//添加球员随机比赛数据
	public function player_match_data_add(){
		$id = $this->_get('match_id');
		if(!is_numeric($id)){
			echo '请输入正确的比赛的数据的id';
		}
		$MatchList = M('MatchList');
		$_data = $MatchList->where(array('id' => $id))->find();
		if(!$_data){
			echo '比赛不存在';
		}
		$players = $this->getdata('player_data_all');
		// print_r($players);
		$PlayerMatchData = M('PlayerMatchData');
		foreach ($players as $key => $value) {
			if( in_array($value['team_id'], array($_data['team_a'],$_data['team_b']))){
				//添加随机的比赛数据
				$data['player_id'] = $value['id'];//球员id
				$data['match_id'] = $_data['id'];//比赛id
				$data['play_time'] = rand(5,45);
				$data['get_score'] = rand(5,30);
				$data['three_point'] = rand(1,10);
				$data['backboard'] = rand(1,15);
				$data['help_score'] = rand(0,5);
				$data['hinder_score'] = rand(1,15);
				$data['cover_score'] = rand(0,5);
				$data['mistake_score'] = rand(1,7);
				$data['add_time'] = time();
				$PlayerMatchData->add($data);
			}
		}

	}
	//添加球员随机比赛数据
	public function player_match_data_lol_add(){
		$id = $this->_data['match_id'];

		if(!is_numeric($id)){
			echo '请输入正确的比赛的数据的id';
		}
		$MatchList = M('MatchList');
		$_data = $MatchList->where(array('id' => $id))->find();
		if(!$_data){
			echo '比赛不存在';
		}
		$players = $this->getdata('player_data_lol');
		// print_r($players);
		$PlayerMatchData = M('PlayerMatchDataLol');
		foreach ($players as $key => $value) {
			if( in_array($value['team_id'], array($_data['team_a'],$_data['team_b']))){
				//添加随机的比赛数据
				$data['player_id'] = $value['id'];//球员id
				$data['match_id'] = $_data['id'];//比赛id
				$position = M('MatchPlayerWcg')->where(array('id'=>$value['id']))->getField('position');
				if ($position != 6) {
					$data['kill'] = mt_rand(5,30);
					$data['death'] = mt_rand(0,30);
					$data['assists'] = mt_rand(0,30);
					$data['jungle'] = mt_rand(20,400);
					$data['ten_kill'] = mt_rand(0,2);
				}else{
					$data['tower'] = mt_rand(0,11);
					$data['dragons'] = mt_rand(0,10);
					$data['barons'] = mt_rand(0,5);
					$data['first_blood'] = mt_rand(0,1);
					$data['is_win'] = mt_rand(0,1);
					$data['is_join'] = mt_rand(0,1);
					$data['is_fast'] = mt_rand(0,1);
				}
				$data['remain'] = mt_rand(0,2);
				$data['addtime'] = time();
				$PlayerMatchData->add($data);
				unset($data);
			}
		}

	}

	//更新今天所有球员的比赛积分
	public function updateplayerscore(){
		//获取所有的球员
		$gttime = strtotime(date('Y-m-d 00:00:00',time()));
		$lttime = strtotime(date('Y-m-d 23:59:59',time()));
		$Map['match_time'] = array(array('egt',$gttime),array('elt',$lttime),'and');
		$Map['is_join'] = 1; //已经上场了的球员(比分不0的)
		$PlayerMatchData = M('PlayerMatchData');
		$data = $PlayerMatchData->where($Map)->select();
		// print_r($data);die;
		foreach ($data as $key => $value) {
			$_score = $this->scorerule($value['player_id'],$value);
			$PlayerMatchData->where(array('id' => $value['id']))->setField('score',$_score*10);
		}

	}
	// 积分规则
    // $player_id 选手的id
    // $player_match_data选手比赛的得分数据
    // @return 返回积分
    protected function scorerule_dota2($player_id,$player_match_data){
        $score_sum = 0;
        $position = M('MatchPlayerWcg')->where(array('id'=>$player_id))->getField('position');
        //var_dump($position);exit;
        if ($position == 6) {
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
        }else{
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
        }
        foreach ($player_match_data as $key => $value) {
            $score_sum += $socre_rule[$key] * $value;
        }

        return number_format($score_sum,1);
    }
    // 根据id获取比赛中的数据
    public function get_match_live(){
    	// 先查询出今天的比赛
    	$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0; // 比赛id
    	$PlayerMatchData = M('PlayerMatchDataDota2');
		$match_data = M('MatchList')->where('id = '.$id)->find();
		$all_player = M('MatchPlayerWcg')->field('id,only_id,team_id,name')->where('project_id = 6')->select();
		$all_team = M('MatchTeam')->field('id,code')->where('project_id = 6')->select();
		foreach ($all_player as $k2 => $v2) {
		    $only_ids[] = $v2['only_id'];
		    $map[$v2['only_id']] = $v2['id'];
		    $map_team[$v2['id']] = $v2['team_id'];
		    $map_name[$v2['id']] = $v2['name'];
		}
		foreach ($all_team as $ks => $vs) {
		    $map_code[$vs['id']] = $vs['code'];
		}
    	$only_id = $match_data['only_id'];
	    $match_id = explode(',',$only_id);
	    foreach ($match_id as $k => $v) {
	        $datas = $htmls = curl_request('http://www.trackdota.com/data/game/'.$v.'/live.json',array(),'GET',true);
	        $data = json_decode($datas,true);
	        if (!$data['tower_state']) {
	        	$this->returnMsg (1,'room');die; // 暂时无法获取数据
	        }
	        // if ($data['finished']) {
	        //	continue;
	        // 比赛已经结束
	        // }
	        if ($k == 0) {
	            $match_time = $data['updated']; // 添加比赛开始时间
	        }
	        for($i=0;$i<5;$i++){
	        	$team_a = $map[$map_code[$map_team[$map[$data['dire']['players'][$i]['account_id']]]]];
	        	if ($team_a) {
	        		break;
	        	}
	        }
	        if ($team_a == $map[$map_code[$match_data['team_a']]]) {
	        	$team_b = $map[$map_code[$match_data['team_b']]];
	        }else if($team_a == $map[$map_code[$match_data['team_b']]]){
	        	$team_b = $map[$map_code[$match_data['team_a']]];
	        }
            if ($data['duration']<1800) {
            	if ($data['winer']== 1) {
	                $match[$k]['team'][$team_b]['is_fast'] = 1;
	                $match[$k]['team'][$team_a]['is_fast'] = 0;
            	}else{
            		$match[$k]['team'][$team_b]['is_fast'] = 0;
	                $match[$k]['team'][$team_a]['is_fast'] = 1;
            	}
            }
	        $match[$k]['team'][$team_a]['first_kill'] = 0;
			$length = strlen(decbin($data['tower_state']));
	        if ($data['tower_state'] == 0) {
				$match[$k]['team'][$team_a]['tower'] = 11;
				$match[$k]['team'][$team_b]['tower'] = 11;
	        }elseif($length < 11){
				$match[$k]['team'][$team_b]['tower'] = 11;
				$match[$k]['team'][$team_a]['tower'] = substr_count(substr(decbin($data['tower_state']),0,11),'0');
	        }else{
		        $match[$k]['team'][$team_b]['tower'] = substr_count(substr(decbin($data['tower_state']),0,11),'0'); // 先转成二进制，数零的个数
		        $match[$k]['team'][$team_a]['tower'] = substr_count(substr(decbin($data['tower_state']),11,11),'0');
	        }
	        $match[$k]['team'][$team_b]['first_kill'] = 0;
	        $match[$k]['team'][$team_a]['opp'] = $map_name[$team_b];
	        $match[$k]['team'][$team_b]['opp'] = $map_name[$team_a];
	        foreach ($data['dire']['players'] as $k1 => $v1) {
	            @$player_id = $map[$v1['account_id']]?$map[$v1['account_id']] : false;
	            if (!@$map[$v1['account_id']]) {
	                continue;
	            }
                $match[$k]['player'][$player_id]['team_id'] = $team_a;// 队伍id
                $match[$k]['player'][$player_id]['opp'] = $match[$k]['team'][$team_a]['opp'];// 对手
	            $match[$k]['player'][$player_id]['kills'] = $v1['kills'];    	  // 击杀
	            $match[$k]['player'][$player_id]['deaths'] = $v1['death'];  	  // 死亡
	            $match[$k]['player'][$player_id]['assists'] = $v1['assists'];	  // 助攻
	            $match[$k]['player'][$player_id]['last_hits'] = $v1['last_hits']; // 补刀
	            $match[$k]['player'][$player_id]['ten_kill'] = 0;
	            if ($v1['kills'] >= 10) {
	                $match[$k]['player'][$player_id]['ten_kill']++;
	            }
	            if ($v1['assists'] >= 10) {
	                $match[$k]['player'][$player_id]['ten_kill']++;
	            }
	        }
	        foreach ($data['radiant']['players'] as  $radiant) {
	            @$player_id = $map[$radiant['account_id']]?$map[$radiant['account_id']] : false;
	            if (!@$map[$radiant['account_id']]) {
	                continue;
	            }
                $match[$k]['player'][$player_id]['team_id'] = $team_b;// 队伍id
                $match[$k]['player'][$player_id]['opp'] = $match[$k]['team'][$team_b]['opp'];// 对手
	            $match[$k]['player'][$player_id]['kills'] = $radiant['kills'];    	  // 击杀
	            $match[$k]['player'][$player_id]['deaths'] = $radiant['death'];  	  // 死亡
	            $match[$k]['player'][$player_id]['assists'] = $radiant['assists'];	  // 助攻
	            $match[$k]['player'][$player_id]['last_hits'] = $radiant['last_hits']; // 补刀
	            $match[$k]['player'][$player_id]['ten_kill'] = 0;
	            if ($radiant['kills'] >= 10) {
	                $match[$k]['player'][$player_id]['ten_kill']++;
	            }
	            if ($radiant['assists'] >= 10) {
	                $match[$k]['player'][$player_id]['ten_kill']++;
	            }
	        }
	    }
	        	        
	    $teams = array();   
	    foreach ($match as $k3 => $v3) {
	        foreach ($v3['team'] as $k4 => $v4) {
	            if ($map_team[$k4] == $match_data['team_a']) {
	                $score = $match_data['score_a'].':'.$match_data['score_b'];
	            }else{
	                $score = $match_data['score_b'].':'.$match_data['score_a'];
	            }
	            if ( ($score == '2:0' && $match_data['home_id'] != 2 )|| $score == '3:1' ) {
	                $remain = 1;
	            }elseif($score == '3:0'){
	                $remain = 2;
	            }else{
	                $remain =0;
	            }
	            $win = array('1:0','2:0','2:1','3:0','3:1','3:2');
	            if (in_array($score,$win)) {
	                $is_win = 1;
	            }else{
	                $is_win = 0;
	            }
	            $teams[$k4]['id'] = $k4;
	            $teams[$k4]['tower'] += $v4['tower'];
	            $teams[$k4]['barons'] += $v4['roshan'];
	            $teams[$k4]['is_fast'] += $v4['is_fast'];
	            $teams[$k4]['remain'] = $remain;
	            $teams[$k4]['is_win'] = $is_win;
	            $scores = $this->scorerule_dota2($k4,$teams[$k4]);
	            $teams[$k4]['score'] = $score;
	            $teams[$k4]['opp'] = $v4['opp'];
	            $teams[$k4]['scores'] = $scores;
	        }
	        foreach ($v3['player'] as $k5 => $v5) {
	            if ($map_team[$v5['team_id']] == $match_data['team_a']) {
	                $score = $match_data['score_a'].':'.$match_data['score_b'];
	            }else{
	                $score = $match_data['score_b'].':'.$match_data['score_a'];
	            }
	            if ( ($score == '2:0' && $match_data['home_id'] != 2 )|| $score == '3:1' ) {
	                $remain = 1;
	            }elseif($score == '3:0'){
	                $remain = 2;
	            }else{
	                $remain =0;
	            }
	            $player[$k5]['id'] = $k5;
	            $player[$k5]['kill'] += $v5['kills'];
	            $player[$k5]['death'] += $v5['deaths'];
	            $player[$k5]['assists'] += $v5['assists'];
	            $player[$k5]['jungle'] += $v5['last_hits'];
	            $player[$k5]['ten_kill'] += $v5['ten_kill'];
	            $player[$k5]['remain'] = $remain;
	            $scores = $this->scorerule_dota2($k5,$player[$k5]);
	            $player[$k5]['scores'] = $scores;
	            $player[$k5]['opp'] = $v5['opp'];
	            $player[$k5]['score'] = $score;
	        }    
	    }
	    $date = date('m/d',$match_time);
	    $season = date('Y',$match_time);
	    $match_id = $match_data['id'];
	    $addtime = time();
	    foreach ($player as $k6 => $v6) {
			$v6['player_id'] = $v6['id'];
			$v6['match_id'] = $match_id;
			$v6['season'] = $season;
			$v6['date'] = $date;
			$v6['addtime'] = $addtime;
	      	unset($v6['id']);
	      	$check = $PlayerMatchData->where("match_id = $match_id and player_id = ".$v6['player_id'])->find();
	      	if ($check) {
	      		$res3 = $PlayerMatchData->where('id ='.$check['id'])->save($v6);
	      	}else{
	        	$res3 = $PlayerMatchData->add($v6);
	      	}
	        if ($res3) {
	            echo '数据写入成功';
	        }else{
	            echo '数据写入失败';
	        }
	    }
	    foreach ($teams as $k7 => $v7) {
	        $v7['player_id'] = $v7['id'];
	        $v7['match_id'] = $match_id;
	        $v7['season'] = $season;
			$v7['date'] = $date;
			$v7['addtime'] = $addtime;
	      	unset($v7['id']);
	        $check = $PlayerMatchData->where("match_id = $match_id and player_id = ".$v7['player_id'])->find();
	      	if ($check) {
	      		$res4 = $PlayerMatchData->where('id ='.$check['id'])->save($v7);
	      	}else{
	        	$res4 = $PlayerMatchData->add($v7);
	      	}
	      	if ($res4) {
	            echo '数据写入成功';
	        }else{
	            echo '数据写入失败';
	        }
	    }
    }
    // 更新冠军猜积分
    public function update_champion_data(){
    	//exit('活动已经结束');
    	$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0; // 冠军猜id
    	$Champion = M('Champion');
    	$ChampionLineup = M('ChampionLineup');
 		$all_team =	M('MatchTeam')->where('project_id = 6')->getfield('id,name');
    	$user_champion = M('UserChampion');
    	$cham_data = $Champion->field('project_id,match_end_time,match_id')->where('id ='.$id)->find();
    	// if (time()<$cham_data['match_end_time'] ) {
    	// 	$this->returnMsg(1);// 参数异常
    	// }
    	$project_id = $cham_data['project_id'];// 项目id
    	$match_id = $cham_data['match_id'];
    	$match_list = M('MatchList');
    	$match_list_data = $match_list->where('match_name_id = '.$match_id)->select();
		$PlayerMatchData = M('PlayerMatchDataDota2');
    	foreach ($match_list_data as $key => $value) {
    		$data[] = $PlayerMatchData->field('player_id,kill,death,assists,jungle,ten_kill,score')->where('match_id ='.$value['id'])->select();
    	}
    	$socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2);
    	foreach ($data as $k1 => $v1) {
    		foreach ($v1 as $k2 => $v2) {
    			$scores = 0;
    			foreach ($v2 as $k3 => $v3) {
		            $scores += $socre_rule[$k3] * $v3;
		        }
				$num = explode(':',$v2['score']); 
				$nums = $num[0]+$num[1];
    			$player[$v2['player_id']][]=round($scores*10/$nums);
    		}
    	}
    	foreach ($player as $k4 => $v4) {
    	 	$player[$k4]= M('MatchPlayerWcg')->field('id,only_id,team_id,name')->where('id = '.$k4)->find();
    	 	$player[$k4]['score']= round(array_sum($v4)/count($v4)) + $this->get_player_score($k4);
    	 	$player[$k4]['team_name'] = $all_team[$player[$k4]['team_id']];
    	}
    	echo json_encode($player);exit;
    	$cham_lineup = $ChampionLineup->field('id,lineup')->where('champion_id ='.$id)->select();
    	foreach ($cham_lineup as $k4 => $v4) {
    	 	$lineups = unserialize($v4['lineup']);
    	 	$lineup_score = 0;
    	 	foreach ($lineups as $k5 => $v5) {
    	 		$lineup_score += $player[$v5];
    	 		$lineup_score += $this->get_player_score($v5);
    	 	}
    	 	$ChampionLineup->where('id = '.$v4['id'])->setField('lineup_score',$lineup_score);
    	 	$user_champion->where('lineup_id = '.$v4['id'])->setField('lineup_score',$lineup_score);
    	}
    	exit('更新完成');
    }
    //获取选手的得分
    public function get_player_score($player_id){
    	$conf[] = array('score' => 1,'teams' => array(100176,100180,100173,100175,100185,100166,100170,100179,100188,100182,100193,100198,100171,100167,100186,100189)); //16强
    	$conf[] = array('score' => 2,'teams' => array(100171,100182,100166,100170,100176,100180,100173,100175)); //8强
    	$conf[] = array('score' => 3,'teams' => array(100173,100175,100171,100170)); //4强
    	$conf[] = array('score' => 4,'teams' => array(100173,100175)); //亚军
    	$conf[] = array('score' => 5,'teams' => array(100175)); //冠军
    	//获取所有的dota2的选手信息
    	$cache_name_t = 'asjdhfjaksdfwe';

    	$palyers = S($cache_name_t);
    	if(!$players){
    		$palyers = M('MatchPlayerWcg')->where(array('project_id' => 6))->getField('id,team_id');
    		S($cache_name_t,$palyers,3600*24);
    	}
    	$score = 0;
    	foreach ($conf as $key => $value) {
    		if( in_array($palyers[$player_id], $value['teams']) ){
    			$score += $value['score'];
    		}
    	}
    	return $score;
    	// echo $score;
    }

    //微信网页授权登录
    public function wxlogin(){
        $code = I('code');
        
        if(!$code){
        	$this->returnMsg(1,'wxlogin');
        }
        //通过code，换取access_token
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.self::$wxappid.'&secret='.self::$wxappsecret.'&code='.$code.'&grant_type=authorization_code';
        $result = $this->c_url($url);
        // echo $result;
        $oauthMessage = json_decode($result,true);
        if($oauthMessage['access_token']){
            $access_token = $oauthMessage['access_token'];
            $openid = $oauthMessage['openid'];
            $unionid = $oauthMessage['unionid'];
        }else{
        	$this->returnMsg(1,'wxlogin');
        }
        $UserOpenInfo = M('UserOpenInfo');
        $connect_data = $UserOpenInfo->where(array('wx_openid' => $unionid))->find();
        $UserUser = M('UserUser');
        if($connect_data && $connect_data['user_id'] != 0){

			$user_data = $UserUser->where(array('id'=>$connect_data['user_id']))->find();
			if($user_data){
				redirect('http://wap.aifamu.com/index.html?code='.$this->en_de_crypt('en',$user_data['token']));
			}else{
				$this->returnMsg(3,'login'); // 登录失败
			}
        }else{
        	//注册操作
			if(!$connect_data){//防止 重复添加openid到数据库

				$wx_user_data = $this->get_wx_userinfo($access_token,$openid);
				//获取到微信的用户信息
					
		        	



				// 注册用户
				$data['type'] = 'wx';
				$data['add_time'] = time();
		        $data['ip'] = get_client_ip(1);// 返回ipv4地址，int
		        // $data['username'] = $this->get_rand_username( $data['type']);
		        $data['username'] = $wx_user_data['nickname'];
		        $data['entrance_ticket'] = C('USER_MONEY')['entrance_ticket'];// 门票
				$data['diamond'] = C('USER_MONEY')['diamond'];// 砖石
				$data['gold'] = C('USER_MONEY')['gold'];// 木头
		        $id = $UserUser->add($data);
		        if ($id) {

		        	$this->url_upload($wx_user_data['headimgurl'],$id); //上传微信头像到我们服务器

		        	$token = md5(time().$id.mt_rand(1000,9999));// 登陆的token

		        	$UserUser->where('id = '.$id)->setField('token',$token);

		        	$error['user_id'] = $id ;

		        	M('UserErrorTry')->add($error);

					$this->set_connect_info($id,$unionid,'wx',$openid);

					$wx_user_data = $this->get_wx_userinfo($access_token,$openid);
					$this->url_upload($wx_user_data['headimgurl'],$id);
					redirect('http://wap.aifamu.com/index.html?code='.$this->en_de_crypt('en',$token));

	        	}
			}else{
				$this->returnMsg(1,'system');
			}
		}

        // return false;
    }

    public function get_user_info(){
    	$code = $this->_data['code'];

    	$token = $this->_data['token'];

    	if(strlen($token) == 32){ //token 登录 

	    	$user_token = $token;

	    	$user_data = M('UserUser')->where(array('token' => $user_token))->find();
	    	if(!$user_data){
	    		$this->returnMsg(1,'login');
	    	}
	    	$this->activity($user_data['id']); // 活动

	    	$this->attendance($user_data['id']); //签到
			// 删除敏感信息
	    	$list = $user_data;
	    	$user=M('UserUser'); // 用户表
		    // 返回ipv4地址，int
			$save['ip'] = get_client_ip(1);
			// 最后登陆时间
			$save['last_time'] = time();
			// 更新最后登陆时间以及ip
			$user->where(array('id'=>$list['id']))->save($save);
			//获取到用户的信息,进行登录操作,登录采用cookie加密的方式进登录
			$en_string = json_encode(array('id' => $list['id'],'username' => $list['username'],'phone' => $list['phone'])); 
			$string = $this->en_de_crypt('en',$en_string);// 生成加密后的字符串
			if($string === false){
				$this->returnMsg(1,'system');
			}
			cookie(C('LOGIN_STR'),$string,3600); //保存在cookie中
			// 头像
			$list['avatar_img'] = C('AVATAR_IMG').$list['id'];
			// $list['token'] = $save['token'];
			$rank = $this->cache('get','rank_name');
			if (!$rank) {
				$rank = M('UserRank')->field('id,name,avatar_img')->select();
				$this->cache('set','rank_name',$rank);
			}
			// 称号不存在,默认为空
			$list['rank_name'] = $rank[$list['rank']-1]['name'] ? $rank[$list['rank']-1]['name'] : ''; 
			$list['rank_img'] = $rank[$list['rank']-1]['avatar_img'] ? $rank[$list['rank']-1]['avatar_img'] : ''; 


			$user_data = $list;



			unset($user_data['password']);

			unset($user_data['ip']);

			$this->returnMsg(0,'login',$user_data);// 登陆成功
    	}


    	if(!$code){
    		$this->returnMsg(1);
    	}
    	$user_token = $this->en_de_crypt('de',$code);
    	if(!$user_token){
    		$this->returnMsg(1);
    	}
    	$user_data = M('UserUser')->where(array('token' => $user_token))->find();
    	if(!$user_data){
    		$this->returnMsg(1,'login');
    	}
    	$this->activity($user_data['id']); // 活动

    	$this->attendance($user_data['id']); //签到
		// 删除敏感信息
		$user_data = $this->login_action($user_data);// 登录后的数据处理
		unset($user_data['password']);

		unset($user_data['ip']);

		$this->returnMsg(0,'login',$user_data);// 登陆成功
    }

    /**
     *  根据openid获取用户的基本信息
     *  unionid	只有将公众号绑定到微信开放平台帐号后，才会出现该字段。
     */
    private function get_wx_userinfo($access_token,$openid){
        
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $result = json_decode($this->c_url($url),true);
        return $result;
    }


    private function set_connect_info($uid,$openid,$type,$pay_openid){
    	if(!in_array($type, array('qq','wx','wb'))){
    		return false;
    	}
    	$UserOpenInfo = M('UserOpenInfo');
    	$data = $UserOpenInfo->where(array('user_id' => $uid))->find();
    	if($data){
    		$_data[$type.'_openid'] = $openid;
			$_data['wx_pay_openid'] = $pay_openid;
    		$res = $UserOpenInfo->where(array('id' => $data['id']))->save($type.'_openid',$_data);
    	}else{
    		$_data['user_id'] = $uid;
			$_data[$type.'_openid'] = $openid;
			$_data['addtime'] = time();
			$_data['wx_pay_openid'] = $pay_openid;
			$res = $UserOpenInfo->add($_data);
    	}
    	if($res){
    		return truen;
    	}
    	return false;
    }
    // 添加选手平均积分
    public function average_dota2_player(){
    	$wcg = M('MatchPlayerWcg');
    	$player = $wcg->where('project_id = 6')->select();
    	$player_match = M('PlayerMatchDataDota2');
    	foreach ($player as $key => $value) {
    		$average = 0;
    		$match_data = $player_match->where('player_id = '.$value['id'])->select();
    		$sum = 0;
    		$count = 0;
    		foreach ($match_data as $k1 => $v1) {
	    		if ($v1['score'] != '0:0' || $v1['score'] != '0：0') {
	    			$sum += $v1['scores'];
	    			$count++;
	    		}
    		}
    		if ($count>0) {
    			$average = $sum/$count;
    		}
    		//var_dump($average);exit;
			$wcg->where('id ='.$value['id'])->save('average',$average);
    	}
    }
    // 更新lol球员工资
     public function update_lol(){
    	$wcg = M('MatchPlayerWcg');
    	$player = $wcg->where('id = '.$this->_data['id'].' and project_id = 5')->select();
    	$player_match = M('PlayerMatchDataLol');
    	$today = strtotime(date("Y-m-d"),time()); //
		$end = $today+60*60*24;  
    	foreach ($player as $key => $value) {
    		$salary = $value['salary'];
    		$result = $salary;
    		$match_data = $player_match->where('is_join = 1 and player_id = '.$value['id'].' and addtime > '.$today.' and addtime <'.$end)->select();
    		if ($match_data) {
	    		foreach ($match_data as $k => $v) {
	    			if ($v['score'] != '0:0' || $v['score'] != '0：0') {
		    			$salary = $v['scores']*0.15 + $salary*0.85;
	    			}
	    		}
    		}
			$wcg->where('id ='.$value['id'])->save('salary',$salary);
    	}
    }
	// 更新dota2球员工资
     public function update_dota2(){
    	$wcg = M('MatchPlayerWcg');
    	$player = $wcg->where('id = '.$this->_data['id'].' and project_id = 6')->select();
    	$player_match = M('PlayerMatchDataDota2');
    	$today = strtotime(date("Y-m-d"),time()); //
		$end = $today+60*60*24;  
    	foreach ($player as $key => $value) {
    		$salary = $value['salary'];
    		$result = $salary;
    		$match_data = $player_match->where('is_join = 1 and player_id = '.$value['id'].' and addtime > '.$today.' and addtime <'.$end)->select();
    		if ($match_data) {
	    		foreach ($match_data as $k => $v) {
	    			if ($v['score'] != '0:0' || $v['score'] != '0：0') {
		    			$salary = $v['scores']*0.15 + $salary*0.85;
	    			}
	    		}
    		}
			$wcg->where('id ='.$value['id'])->save('salary',$salary);
    	}
    }


    //手动更新阵容积分NBA
	public function updateroomlineup_admin(){
		$room_id = I('room_id'); //房间的id
		if(!is_numeric($room_id)){
			exit('房间id不正确');
		}
		$Map['status'] = 1;//发布中
		$Map['settlement_status'] = 1;
		$MatchRoom = M('MatchRoom');
		$Map['project_id'] = 4;//nba
		$Map['id'] = $room_id = I('room_id');
		$data = $MatchRoom->join('as t1 left join '.C('DB_PREFIX').'match_room_info as t2 on t1.id=t2.room_id')->field('id,room_id,match_team')->where($Map)->select();
		// print_r($data);die;
		$matchs = array();
		foreach ($data as $key => $value) {
			if ($now_time < $value['match_start_time'] ) {
				continue;
			}
			$matchs[] = $value['match_team'];
		}
		$matchs = array_unique($matchs);

		$where = $this->or_sql($matchs);
		$Lineup = M('Lineup');
		$lineups = $Lineup->where($where)->select();
		$today_match_data = $this->todaymatchnba($matchs); // 今天的比赛数据
		if(!$today_match_data){
			echo '没有查到比赛数据,请先更新数据';die;
		}
		$UserGuessRecord = M('UserGuessRecord');
		//循环阵容,更新阵容的积分
		foreach ($lineups as $key => $value) {
			$s = unserialize($value['lineup']);
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和
			foreach ($s as $keys => $values) {
				$lineup_score_sum += $this->scorerule($values,$today_match_data[$values]);
				$lineup_total_play_time += $today_match_data[$values]['play_time'];
			}
			$_data['lineup_score'] = $lineup_score_sum*10;
			$_data['total_play_time'] = $lineup_total_play_time;
			$Lineup->where(array('id' => $value['id']))->save($_data); //更新阵容的积分
			$UserGuessRecord->where(array('lineup_id' => $value['id']))->setField('lineup_score',$lineup_score_sum*10); //更新用户所选阵容的积分

			//防止数据累计
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和

		}
		echo '更新完成,时间:' . date('Y-m-d H:i:s');

	}


	/**
	* 数据实时更新计划
	* 1 更新所有房间的所选阵容的所得积分 lol 和dota2
	*/
	public function updateroomlineuplol_admin(){
		$room_id = I('room_id'); //房间的id
		if(!is_numeric($room_id)){
			exit('房间id不正确');
		}
		$Map['status'] = 1;//发布中
		//查询已经开始了的
		$now_time = time();
		$MatchRoom = M('MatchRoom');
		$Map['project_id'] = I('project_id');//5为lol，6为dota2
		$Map['id'] = $room_id;
		$data = $MatchRoom->join('as t1 left join '.C('DB_PREFIX').'match_room_info as t2 on t1.id=t2.room_id')->field('id,room_id,match_team')->where($Map)->select();
		$matchs = array();
		foreach ($data as $key => $value) {
			if ($now_time < $value['match_start_time'] ) {
				continue;
			}
			$matchs[] = $value['match_team'];
		}
		$matchs = array_unique($matchs);
		$match_list = implode(',', $matchs);
		$where = $this->or_sql($matchs);
		$Lineup = M('Lineup');
		$lineups = $Lineup->where($where)->select();
		$today_match_data = $this->todaymatch($match_list,$Map['project_id'])[$Map['project_id']]; // 今天的比赛数据
		// var_dump($where);die;
		
		if(!$today_match_data){
			echo '没有查到比赛数据,请先更新数据';die;
		}
		$UserGuessRecord = M('UserGuessRecord');
		//循环阵容,更新阵容的积分
		foreach ($lineups as $key => $value) {
			$s = unserialize($value['lineup']);
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和
			foreach ($s as $keys => $values) {
				$lineup_score_sum += $today_match_data[$values]['scores'];
			}
			$_data['lineup_score'] = $lineup_score_sum;
			$Lineup->where(array('id' => $value['id']))->save($_data); //更新阵容的积分
			$UserGuessRecord->where(array('lineup_id' => $value['id']))->setField('lineup_score',$lineup_score_sum); //更新用户所选阵容的积分

			//防止数据累计
			$lineup_score_sum = 0; // 阵容的积分和
			$lineup_total_play_time = 0; // 阵容的总出场时间和

		}
		echo '更新完成,时间:' . date('Y-m-d H:i:s');
	}

	//更新房间的排名奖励
	//按照积分进行排名,相同积分的用户均分奖励
	public function updateroom_admin_bak(){
		$room_id = I('room_id'); //房间的id
		if(!is_numeric($room_id)){
			exit('房间id错误');
		}
		// set_time_limit(0);
		$MatchRoom = M('MatchRoom');
		$Map['status'] = 1;//发布中
		$Map['settlement_status'] = 1; //未结算
		$Map['id'] = $room_id;
		//查询已经开始了的
		// $now_time = time();
		// $Map['match_start_time'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',$now_time), 'and'); //塞选比赛时间,只查询今天开赛的房间
		$data = $MatchRoom->field('id,reward_id,reward_num,prize_num,open_id,open_num,now_guess_num,is_special,special_uid')->where($Map)->select();
		// print_r($data);
		if(!$data){
			exit('没有查询到比赛的数据,或已结算');
		}

		$UserGuessRecord = M('UserGuessRecord');
		foreach ($data as $key => $value) {
			//更新比赛的状态为进行中
			$UserGuessRecord->where(array('room_id' => $value['id']))->setField('match_status',2);//data
			//更新房间的用户的排名,和用户获得的奖励
			//更新后再次查询所有用户,根据积分规则进行排序,和设置排名
			sleep(1);// 防止数据没有更新过来导致错误
			$lineup_data_user = $UserGuessRecord->where(array('room_id' => $value['id']))->order('lineup_score desc')->select(); //该房间所有的阵容
			
			if(!$lineup_data_user){ //没有投注的用户直接跳过循环
				continue;
			}

			//将用户投注多注的塞选出来
			$s = array();
			foreach ($lineup_data_user as $ku => $vu) {
				if($vu['guess_num'] > 1){
					for($i = 1; $i <= $vu['guess_num'];$i++){
						$s[] = $vu;
					}
				}else{
					$s[] = $vu;
				}
			}
			$lineup_data_user = $s;

			//获取房间的中奖配置信息
			if($value['open_id'] == 2){ //满开,检测是否满足开奖条件
				if($value['now_guess_num'] < $value['open_num']){ //没有满足开奖条件,获得的奖励都为0
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
					$is_must_open = false; //是否必开
				}else{
					$is_must_open = true;
				}
			}else{
				$is_must_open = true;
			}

			if($is_must_open == true){

				if($value['reward_id'] == 1){ //每人各的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = $value['reward_num']; //每人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 2){ //均分奖池的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 3){ //获胜均分,主播房
					$special_data = $UserGuessRecord->where(array('uid' => $value['special_uid'],'room_id' =>$value['id']))->find();//获取主播的阵容的积分数据
					$reward_user_num = $UserGuessRecord->where('lineup_score>' . $special_data['lineup_score'].' and room_id='.$value['id'])->count(); //大于主播的玩家数 -> 中奖的人数
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}
				if($value['reward_id'] == 7){
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
				}

				if($value['reward_id'] == 12){ //实物奖品
					$reward = 1; //实物奖励的时候,默认is_reward值为1
					$reward_user_num = $value['prize_num'];
				}

			}

			$j = 1;
			for ($i=0; $i < count($lineup_data_user); $i++) {

				$lineup_data_user[$i]['ranking'] = $j;
				//用户实际排名获得的奖励 - start
				if(in_array($value['reward_id'], array(4,5,6,8,9,10,11))){ //固定配置奖励
					$lineup_data_user[$i]['reward'] = $this->user_get_reward($value['reward_id'],$j);
				}else{
					if($reward == 0 || $reward_user_num == 0){ //中奖人数/奖品都为0是,获得奖励为0
						$lineup_data_user[$i]['reward'] = 0;
					}else{
						if($j <= $reward_user_num){
							$lineup_data_user[$i]['reward'] = $reward; //获取的奖品数量
						}else{
							$lineup_data_user[$i]['reward'] = 0;
						}
					}
				}
				//用户实际排名获得的奖励 - end
				// if($i == 0){continue;}
				if($lineup_data_user[$i]['lineup_score'] == $lineup_data_user[$i-1]['lineup_score']){
					$lineup_data_user[$i]['ranking'] = $lineup_data_user[$i-1]['ranking'];
				}
				$j++;
				$UserGuessRecord->where(array('lineup_id' => $lineup_data_user[$i]['lineup_id'],'room_id' => $lineup_data_user[$i]['room_id']))->setField('ranking',$lineup_data_user[$i]['ranking']); //更新用户的排名
			}
			// print_r($lineup_data_user);
			//die
			sleep(1);

			if($value['reward_id'] == 1 || $value['reward_id'] == 12){//如果奖励规则是没人各的,则不进行排名均分

				foreach ($lineup_data_user as $kk => $vk) {
					$UserGuessRecord->where(array('uid' => $vk['uid'],'room_id' => $vk['room_id'],'id'=>$vk['id'],'lineup_id'=>$vk['lineup_id']))->setField('is_reward',$vk['reward']);
					// echo $UserGuessRecord->getLastSql()."\r\n";
				}
				continue;
			}


			//更新用户的奖励
			//再次做循环统计相同名次的,取中奖平均值
			$s = array(); //存储用户的奖励
			$k = array(); //存储用户阵容id
			for ($i = 0; $i < count($lineup_data_user); $i++) {
				// if($lineup_data_user[$i]['ranking'] == $lineup_data_user[$i-1]['ranking']){
				// 	$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i-1]['uid']] = $lineup_data_user[$i-1]['reward'];
				// 	$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']] = $lineup_data_user[$i]['reward'];
				// }else{
				// 	$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']] = $lineup_data_user[$i]['reward'];
				// }
				$s[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']][] = $lineup_data_user[$i]['reward'];
				$k[$lineup_data_user[$i]['ranking']][$lineup_data_user[$i]['uid']][] = $lineup_data_user[$i]['lineup_id'];
			}
			// print_r($s);
			// print_r($k);
			// //添加用户的奖励到数据库
			foreach ($s as $kq => $vq) {
				$user_guess_sum = 0;
				$user_guess_reward_sum = 0;
				foreach ($vq as $ky => $vy) {
					$user_guess_sum += count($vy); //中奖总次数
					$user_guess_reward_sum += array_sum($vy); //中奖的木头总和
				}
				$reward = floor($user_guess_reward_sum/$user_guess_sum);
				foreach ($vq as $ky => $vy) {
					$UserGuessRecord->where(array('uid' => $ky,'room_id' => $value['id'],'lineup_id'=>$k[$kq][$ky][0]))->setField('is_reward',$reward*count($vy));
				}
			}
			echo '更新完成,时间:' . date('Y-m-d H:i:s') . '<br />';
		}
	}
	//更新房间的排名奖励,同样的积分按照时间的投注的先后顺序进行排名,
	public function updateroom_admin(){
		$room_id = I('room_id'); //房间的id
		if(!is_numeric($room_id)){
			exit('房间id错误');
		}
		// set_time_limit(0);
		$MatchRoom = M('MatchRoom');
		$Map['status'] = 1;//发布中
		$Map['settlement_status'] = 1; //未结算
		$Map['id'] = $room_id;
		//查询已经开始了的
		// $now_time = time();
		// $Map['match_start_time'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',$now_time), 'and'); //塞选比赛时间,只查询今天开赛的房间
		$data = $MatchRoom->field('id,reward_id,reward_num,prize_num,open_id,open_num,now_guess_num,is_special,special_uid')->where($Map)->select();
		// print_r($data);
		if(!$data){
			exit('没有查询到比赛的数据,或已结算');
		}

		$UserGuessRecord = M('UserGuessRecord');
		foreach ($data as $key => $value) {
			//更新比赛的状态为进行中
			$UserGuessRecord->where(array('room_id' => $value['id']))->setField('match_status',2);//data
			//更新房间的用户的排名,和用户获得的奖励
			//更新后再次查询所有用户,根据积分规则进行排序,和设置排名
			sleep(1);// 防止数据没有更新过来导致错误
			$lineup_data_user = $UserGuessRecord->where(array('room_id' => $value['id']))->order('lineup_score desc,add_time asc')->select(); //该房间所有的阵容,按照积分和时间投注进行排序
			
			if(!$lineup_data_user){ //没有投注的用户直接跳过循环
				continue;
			}

			//将用户投注多注的塞选出来
			$s = array();
			foreach ($lineup_data_user as $ku => $vu) {
				
				if($vu['guess_num'] > 1){
					for($i = 1; $i <= $vu['guess_num'];$i++){
						$s[] = $vu;
					}
				}else{
					$s[] = $vu;
				}
			}
			$lineup_data_user = $s;

			//获取房间的中奖配置信息
			if($value['open_id'] == 2){ //满开,检测是否满足开奖条件
				if($value['now_guess_num'] < $value['open_num']){ //没有满足开奖条件,获得的奖励都为0
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
					$is_must_open = false; //是否必开
				}else{
					$is_must_open = true;
				}
			}else{
				$is_must_open = true;
			}

			if($is_must_open == true){

				if($value['reward_id'] == 1){ //每人各的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = $value['reward_num']; //每人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 2){ //均分奖池的 -- 奖励配置
					$f1 = floor($value['now_guess_num'] * $value['prize_num'] / 100);
					$reward_user_num = $f1 >= 1 ? $f1 : 1; //中奖人数名次
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}

				if($value['reward_id'] == 3){ //获胜均分,主播房
					$special_data = $UserGuessRecord->where(array('uid' => $value['special_uid'],'room_id' =>$value['id']))->find();//获取主播的阵容的积分数据
					$reward_user_num = $UserGuessRecord->where('lineup_score>' . $special_data['lineup_score'].' and room_id='.$value['id'])->count(); //大于主播的玩家数 -> 中奖的人数
					$reward = floor($value['reward_num'] / $reward_user_num); //没人中奖数量,可获得的奖励
				}
				if($value['reward_id'] == 7){
					$reward = 0; //奖品数量
					$reward_user_num = 0; //中奖的人数
				}

				if($value['reward_id'] == 12){ //实物奖品
					$reward = 1; //实物奖励的时候,默认is_reward值为1
					$reward_user_num = $value['prize_num'];
				}

			}

			$j = 1;
			$lineup_data_user_l = $lineup_data_user;
			for ($i=0; $i < count($lineup_data_user); $i++) {

				$lineup_data_user[$i]['ranking'] = $j;
				//用户实际排名获得的奖励 - start
				if(in_array($value['reward_id'], array(4,5,6,8,9,10,11))){ //固定配置奖励
					$lineup_data_user[$i]['reward'] = $this->user_get_reward($value['reward_id'],$j);
				}else{
					if($reward == 0 || $reward_user_num == 0){ //中奖人数/奖品都为0是,获得奖励为0
						$lineup_data_user[$i]['reward'] = 0;
					}else{
						if($j <= $reward_user_num){
							$lineup_data_user[$i]['reward'] = $reward; //获取的奖品数量
						}else{
							$lineup_data_user[$i]['reward'] = 0;
						}
					}
				}
				$j++;
			}

			sleep(1);


			$user_s = array();

			foreach ($lineup_data_user as $kk => $vk) {
				$UserGuessRecord->where(array('id' => $vu['id']))->setField('ranking',$ku + 1);
				$user_s[$vk['uid']][$vk['lineup_id']][] = $vk['reward'];
			}

			// print_r($lineup_data_user_l);die;
			$yu = 1;
			for ($i=0; $i < count($lineup_data_user_l); $i++) {

				$lineup_data_user_l[$i]['ranking'] = $yu;

				//用户实际排名获得的奖励 - end
				// if($i == 0){continue;}
				if($lineup_data_user_l[$i]['lineup_score'] == $lineup_data_user_l[$i-1]['lineup_score'] && $lineup_data_user_l[$i]['add_time'] == $lineup_data_user_l[$i-1]['add_time']){
					$lineup_data_user_l[$i]['ranking'] = $lineup_data_user_l[$i-1]['ranking'];
				}
				$yu++;
				$UserGuessRecord->where(array('lineup_id' => $lineup_data_user_l[$i]['lineup_id'],'room_id' => $lineup_data_user_l[$i]['room_id']))->setField('ranking',$lineup_data_user_l[$i]['ranking']); //更新用户的排名


			}


			foreach ($user_s as $k_ => $v_) {
				foreach ($v_ as $_k_ => $_v_) {
					// if(array_sum($_v_) == 0){
					// 	continue;
					// }
					$UserGuessRecord->where(array('room_id' => $value['id'],'lineup_id' => $_k_,'uid' => $k_))->setField('is_reward',array_sum($_v_));
				}
			}
			echo '更新完成,时间:' . date('Y-m-d H:i:s') . '<br />';
		}
	}

	/*注册*/
	public function register_test(){ //添加测试账号
		exit('停用');
		// 用户注册类型
        $data['type'] = rand(1,4);
        $UserUser = M('UserUser');
        $data['add_time'] = time();
        $data['ip'] = get_client_ip(1);// 返回ipv4地址，int
        $data['username'] = $this->get_rand_username( $data['type']);
    	$data['phone'] = rand(222,999).rand(222,999).rand(222,999).rand(22,99); //注册手机号
		$res = $UserUser->where(array('phone'=>$data['phone']))->find();
		if ($res) {
			$this->returnMsg(1,'sms');// 你已注册，请登陆
		}

        $data['password'] = '';// 密码加盐加密
       	// 初始化用户金额
       	$data['entrance_ticket'] = c('USER_MONEY')['entrance_ticket'];// 门票
		$data['diamond'] = c('USER_MONEY')['diamond'];// 砖石
		$data['gold'] = c('USER_MONEY')['gold'];// 木头
        $id = $UserUser->add($data);
        if ($id) {
        	$this->data_count(false,true);//新增用户统计
        	$token = md5(time().$id.mt_rand(1000,9999));// 登陆的token
        	$UserUser->where('id = '.$id)->setField('token',$token);
        	$user=M('UserUser'); // 用户表
	        $UserErrorTry=M('UserErrorTry'); // 用户表
	        // 判断手机号是否存在
	        $list = $user->where('id = '.$id)->find();
        	$error['user_id'] = $id ;
        	M('UserErrorTry')->add($error);
        	$rank_data['uid'] = $id;
        	$rank_data['rank_id'] = 1;
        	M('UserRankInfo')->add($rank_data);
        	$UserErrorTry->where(array('user_id'=>$list['id']))->save($data);
			$user_data = $this->login_action($list);// 登录后的数据处理
			// 删除敏感信息
			unset($user_data['password']);
			unset($user_data['ip']);
			unset($user_data['hide_notice']);
			unset($user_data['notice_state']);
			$this->attendance($list['id']); // 每日签到
			$this->activity($list['id']); // 活动
        	$this->returnMsg(0,'reg',$user_data);// 注册成功 
        }else{
        	$this->returnMsg(10,'user');// 注册失败，请重试 
        }
        
	}
	/*更改队伍信息*/
	public function update_team_info(){
		$player_wcg = M('MatchPlayerWcg');
		$team = $player_wcg->field('id,team_id')->where('position = 6 and project_id = 6')->select();
		$match_list = M('MatchList');
		foreach ($team as $value) {
			$win = 0;
			$lost = 0;
			$match_data = $match_list->field('team_a,team_b,score_a,score_b')->where('match_name_id = 7 and (team_a = '.$value['team_id'].' or team_b ='.$value['team_id'].')')->select();
			foreach ($match_data as $keys => $values) {
				if ($values['team_a'] == $value['team_id']) {
					$win += $values['score_a'];
					$lost += $values['score_b'];
				}else{
					$lost += $values['score_a'];
					$win += $values['score_b'];
				}
			}
			$result = $win.'W-'.$lost.'L';
			if ($result != '0W-0L') {
				$player_wcg->where('id ='.$value['id'])->setField('result',$result);
			}
		}
	}
	/*更改球员kda信息*/
	public function update_player_info(){
		$player_wcg = M('MatchPlayerWcg');
		$player = $player_wcg->select();
		$match_dota2 = M('PlayerMatchDataDota2');
		$match_lol = M('PlayerMatchDataLol');
		foreach ($player as $value) {
			if ($value['project_id'] == 5) {
				$datas = $match_lol->where('scores > 0 and player_id = '.$value['id'])->select();
			}elseif($value['project_id'] == 6) {
				$datas = $match_dota2->where('scores > 0 and player_id = '.$value['id'])->select();
			}
			$kill = 0;
			$assists = 0;
			$death = 0;
			foreach ($datas as $keys => $values) {
				$kill += $values['kill'];
				$assists += $values['assists'];
				$death += $values['death'];
			}
			$KDA = number_format(($kill + $assists )/$death,1);
			if ($KDA != 0) {
				$player_wcg->where('id ='.$value['id'])->setField('KDA',$KDA*10);
			}
		}
	}
}