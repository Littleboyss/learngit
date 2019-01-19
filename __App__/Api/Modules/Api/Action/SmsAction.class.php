<?php
/**
 * 短信接口
 * @author chengy 2017.04.10
 */
class SmsAction extends CommonAction{

	private $user_apiKey = 'f4ce4c4e4180dfff9d3c1b2887d72f31';// String  用户账号
	private $user_seckey = '8AE25C91AAA63209';	//String		//用户的密码
	//private $user_templateId = '1064';   //String 用户测试用的 ----短信模板ID
	private $user_templateId = '1233';   //String 用户测试用的 ----短信模板ID
	private $flag = true;  //测试标示符，上线时改为true
	private $msg = '【爱伐木】尊敬的玩家，您的验证码是%s，10分钟内有效，快来开启你的竞猜之旅！';

	//统计请求,添加记录 和 返回最后一次请求1小时内的，请求次数
	public function countRequest($phone){
		if($phone){
			$AdminSmscount = M('AdminSmscount');
			$time = time();
			$data['phone'] = $phone;
			$data['request_time'] = $time;
			$result = $AdminSmscount->add($data);
			//最近1小时的时间戳
			$onehourstime = $time - 3600;

			$RMap['request_time'] = array('gt',$onehourstime);
			$count = $AdminSmscount->where($RMap)->count();//查询最近1小时的请求次数

			//删除前一天 该号码的所有记录
			$lasttime = strtotime('yesterday');
			//var_dump($lasttime);
			$AdminSmscount->where('request_time <'.$lasttime)->delete();
			return $count ? $count : 0;
		}else{
			return 0;
		}
	}


	/*
	发送验证短信
	param apiToken phoneNum token(可选)
	error code 
		0 顺利返回
		1 token验证错误
	*/
	function send(){
		$code = rand(100001,999999); // 验证码
		$phone = $this->_data['phoneNum']; // 手机号
		$todo = $this->_data['todo'];
		$user_token = $this->_data['user_token'];
		//验证手机号是否已经注册
		$PMap['phone'] = $phone;
		if(!preg_match("/^1[34578][0-9]{9}$/", $phone)){
			$this->returnMsg(7,'user');// 请正确输入手机号
		}
		$check = M('UserUser')->field('id')->where($PMap)->find();
		if($todo == 1){//注册
			if($check){
				$this->returnMsg(1,'sms');
			}
		}elseif($todo == 2){//重置密码
			if(!$check){
				$this->returnMsg(2,'sms');
			}
		}elseif($todo == 3){//绑定手机
			// 判断用户是否已绑定手机
			if (!$user_token) {
				$this->returnMsg(8,'user'); // 参数错误
			}
			$user = M('UserUser')->field('phone')->where(array('token'=>$user_token))->find();
			if ($user['phone']) {
				$this->returnMsg(3,'bind'); // 手机已经绑定
			}
			if($check){
				$this->returnMsg(5,'sms');
			}
		}elseif($todo == 4){//验证手机
			// if(!$check){
			// 	$this->returnMsg(7,'sms');
			// }
		}
		$ip = get_client_ip(1);// 返回ipv4地址，int
		if (empty($phone) || empty($todo)) {
			$this->returnMsg(5,'buy');// 输入数据不合法
		}else{
			$time = time();
			$Sms = M('AdminSms');
			$IPMap['ip'] = $ip;
			$check_ip = $Sms->where($IPMap)->find();//查询该ip最后一次的请求时间
			$limittime = $time - $check_ip['sms_time'];
			$todytime = strtotime(date('Y-m-d'));
			$IPMap['sms_time'] = array('gt',$todytime);
			
			$sendCount = $Sms->where($IPMap)->count();// 查询当日的请求次数的
			if($sendCount >= 20){
				$this->returnMsg(6, 'sms'); // 您请求的次数超过限制
			}

			$Map['phone'] = $phone;
			$result = $Sms->where($Map)->order('id desc')->find();
			//判断是否发送过
			if (!$result) {
				//未发送过，直接发送
				$data['phone'] = $phone;
				$data['sms_code'] = $code;
				$data['todo'] = $todo;
				$data['sms_time'] = time();
				$data['source'] = 2;
				$data['ip'] = $ip;	
				$res = $Sms->add($data);
				if ($this->flag) {
					$code = sprintf($this->msg,$code);
					$this->sendPostRequest($phone,$code);
					$this->returnMsg(0,'sms');	
				}else{
					$returnData['error'] = 0;
			    	$returnData['captcha'] = $code;
			    	echo json_encode($returnData);
				}
			}else{
				//判断发送间隔	
	        	$limit = 60;
	        	//该手机为黑名单
	        	if($result['warn'] >= 2){
	        		$this->returnMsg(11,'sms');// 系统错误
	        	}

	        	$val = $time - $result['sms_time'];
	        	if ($val > $limit) {
        		// if (true) {
	        		//超过60s允许重发并且记录
					$data['sms_code'] = $code;
					$data['sms_time'] = mktime();
					$data['status']	= 0;//重置验证状态
					$data['ip'] = $ip;
					$data['todo'] = $todo;
					$data['source'] = 2;
					//统计单个号码的次数用户
					$lastsend = date('Ymd',$result['sms_time']);
					$nowtime = date('Ymd');
					if($lastsend == $nowtime){
						if($result['get_count'] >= 10){
							$this->returnMsg(7,'sms');
						}else{
							$Sms->where($Map)->setInc('get_count',1);
						}
					}else{
						$Sms->where($Map)->setField('get_count',1);
					}
					
					$Sms->where($Map)->save($data);

					//统计部分,计算1小时内的请求次数
					$recount = $this->countRequest($phone);
					if($recount >= 5){
						$Sms->where($Map)->setInc('warn',1);
					}

	        		if ($this->flag) {
	        			$code = sprintf($this->msg,$code);
	        			$this->sendPostRequest($phone,$code);
						$Sms->where($Map)->setInc('total',1);
						$this->returnMsg(0,'sms');	
					}else{
						$returnData['error'] = 0;
				    	$returnData['captcha'] = $code;
						$Sms->where($Map)->setInc('total',1);
				    	echo json_encode($returnData);
					}
	        	}else{
	        		//未超过60s
	        		$this->returnMsg(3,'sms');
	        	}
			}
				 
		}
	}

	/*
	验证短信
	param apiToken phoneNum smsCode token
	error code 
		0 无错误
		1 token验证错误
		3 验证码错误
		4 验证码超时
	*/
	function verify(){
		if (!empty($token)) {
			$user = $this->userRequest();
			if ($user['phone']  != 0) {
				$this->returnMsg(10,'sms');	
			}else{
				$User = M('UserUser');
			}
		}
		$phone = $this->_data['phoneNum'];
		if(!preg_match("/^1[34578][0-9]{9}$/", $phone)){
			$this->returnMsg(7,'user');// 请正确输入手机号
		}
		$code = $this->_data['smsCode'];
		if (is_null($phone)) {
			$this->returnMsg(2,'customer');
		}
		if (is_null($code)) {
			$this->returnMsg(2,'customer');
		}
		$Sms = M('AdminSms');
		$Map['phone'] = $phone;
		$Map['sms_code'] = $code;
		$Map['status'] = 0;
		$result = $Sms->where($Map)->order('id desc')->find();
		if (is_null($result)) {
			$this->returnMsg(9,'sms');	
		}else{
			$limit = 60 * 10;
			$time = time();
        	$val = $time - $result['sms_time'];// 十分钟内验证
			if ($val > $limit) {
				$this->returnMsg(4,'sms');	
			}else{
				$data['status'] = 1;
				$Sms->where($Map)->save($data);// 更改验证状态
				$this->returnMsg(0,'system');	
			}
		}
	}
	private function setSendPostJsonStr($phone,$code)
	{
	    $data['apiKey'] =  $this->user_apiKey;
	    $data['content'] = $code;
	    $data['extNum'] = "";
	    $data['op'] = "Sms.send";
	    $data['phone'] = $phone;//群发号码间用英文逗号隔开，最多200个号码，例如：13911112222,13022221111,13311110000
	    $data['taskId'] = floor((microtime(true)*1000)); ////不超过64位长度的唯一字符串，通过和手机状态接口获取的结果里的teskid关联，确定发送的信息是否收到。
	    $data['templateId'] = $this->user_templateId;
	    $data['ts'] = floor((microtime(true)*1000));


	    //array_multisort($data,SORT_ASC);
	    $str = '';
	    foreach ($data as $k => $v)
	    {
	        $str .= $k.'='.$v;
	    }$str .=$this->user_seckey;
	    //var_dump($str);
	    $data['sig'] = md5($str);
	    //var_dump( md5($str));
	    return json_encode($data);
	}
	// #接收手机回复，要轮询该接口，
	private function setMoPostJsonStr()
	{
	    $data['apiKey'] =  $this->user_apiKey;
	    $data['op'] = "Sms.mo";
	    $data['ts'] = floor((microtime(true)*1000));


	    //array_multisort($data,SORT_ASC);
	    $str = '';
	    foreach ($data as $k => $v)
	    {
	        $str .= $k.'='.$v;
	    }$str .=$this->user_seckey;
	    //var_dump($str);
	    $data['sig'] = md5($str);
	    //var_dump( md5($str));
	    return json_encode($data);
	}

	//接收手机是否收到的状态，要轮询该接口，
	private function setRptPostJsonStr()
	{
	    $data['apiKey'] =  $this->user_apiKey;
	    $data['op'] = "Sms.status";
	    $data['ts'] = floor((microtime(true)*1000));


	    //array_multisort($data,SORT_ASC);
	    $str = '';
	    foreach ($data as $k => $v)
	    {
	        $str .= $k.'='.$v;
	    }$str .=$this->user_seckey;
	    //var_dump($str);
	    $data['sig'] = md5($str);
	    //var_dump( md5($str));
	    return json_encode($data);
	}

	//查询余额
	private function setBalPostJsonStr()
	{
	    $data['apiKey'] =  $this->user_apiKey;
	    $data['op'] = "Sms.account";
	    $data['ts'] = floor((microtime(true)*1000));


	    //array_multisort($data,SORT_ASC);
	    $str = '';
	    foreach ($data as $k => $v)
	    {
	        $str .= $k.'='.$v;
	    }$str .=$this->user_seckey;
	    //var_dump($str);
	    $data['sig'] = md5($str);
	    //var_dump( md5($str));
	    return json_encode($data);
	}

	//发送单条短信
	private function sendPostRequest($phone,$code){	
		$post_data = array();
		$post_data['msg'] = $code;
		$post_data['account'] = 'jingjiyou';
		$post_data['pswd'] =  'Password@1';
		$post_data['mobile'] = $phone;
		$post_data['needstatus'] = true;
		$post_data['product'] = 101822;
		$post_data['extno'] = 758;
		$url = 'http://118.178.189.133/msg/HttpSendSM?';

		$post_data = http_build_query($post_data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		$status = explode($result, ',');
		if($status[1] == 0){
			return true;
		}else{
			return false;
		}
	}

	
}
?>