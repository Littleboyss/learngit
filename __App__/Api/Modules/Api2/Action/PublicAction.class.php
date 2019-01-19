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
    	$map['token'] = $this->_data['user_token'];  
    	$user=M('UserUser'); // 用户表
    	$list = $user->where($map)->find();
    	if ($list) {
    		$list['avatar_img'] = C('AVATAR_IMG').$list['id'];
			$list['token'] = $save['token'];
			$rank = $this->cache('get','rank_name');
			if (!$rank) {
				$rank = M('UserRank')->field('id,name,avatar_img')->select();
				$this->cache('set','rank_name',$rank);
			}
			// 称号不存在,默认为空
			$list['rank_name'] = $rank[$list['rank']-1]['name'] ? $rank[$list['rank']-1]['name'] : '';
			$list['rank_img'] = $rank[$list['rank']-1]['avatar_img'] ? $rank[$list['rank']-1]['avatar_img'] : '';
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
					$rink_id = $rank_data['rank_id'].',2';
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
    	$user_error=M('UserErrorTry');
    	$user_error->where('user_id',$this->_user['id'])->setField(array('black_hand_1'=>0,'black_hand_2'=>0));
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



    //微信网页授权登录
    public function wxlogin(){

    	$redirect_url = 'http://act.aifamu.com/index.html?code=';

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
				redirect($redirect_url.$this->en_de_crypt('en',$user_data['token']));
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
					redirect($redirect_url.$this->en_de_crypt('en',$token));
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
}