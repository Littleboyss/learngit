<?php
/**
 * 回调
 * @author Wub 2017.3.27
 */
class PayAction extends CommonAction{

	private static $act = true; //是否开启赠送
	private static $act_num = 0.3; //赠送的比例(百分比)

	//微信返回的地址
	public function wxcallback(){
		$scale = C('scale');//兑换比例
		$nonce_str = I('nonce_str'); //商户的签名
		$rmb = $cash_fee = intval(I('cash_fee'))/100;//用户支付的金额.单位:元
		$out_trade_no = I('out_trade_no');//商户的订单
		$transaction_id = I('transaction_id');//微信的订单
		$trade_type = I('trade_type');//支付的方式
		if($this->checkOrderTrue($transaction_id,$trade_type) != $out_trade_no){//
			echo '11';die;
		}
		$UserCharge = M('UserCharge');
		$data = $UserCharge->where(array('order_no' => $out_trade_no,'status' => 0))->find(); //查询订单的真实性
		if(!$data || md5($data['sign_num']) != $nonce_str || $cash_fee*$scale != $data['amount']){ //验证
			echo '2';die;
		}
		if ($rmb <= 4) {
			$add = 0;
		}
		if ($rmb <= 9 && $rmb >= 5) {
			$add = 0;
		}
		if ($rmb <= 29 && $rmb >= 10) {
			$add = 0;
		}
		if ($rmb <= 99 && $rmb >= 30) {
			$add = 0;
		}
		if ($rmb >= 100) {
			$add = 0;
		}
		$gold = $rmb * $scale + $add;

		if(self::$act){
			$gold += floor($gold*self::$act_num); //加送
		}


		$_data['modify_time'] = time();
		$_data['status'] = 1;
		$_data['gold'] = $gold;
		$_data['pay_order'] = $transaction_id;//微信的订单号

		$result = $UserCharge->where(array('order_no' => $out_trade_no))->save($_data);
		// echo $UserCharge->getLastSql();
		if($result){
			$this->data_count(false,false,true,intval(I('cash_fee')));//新增用户统计
			$UMap['id'] = $data['uid'];
			$User = M('UserUser'); 
			$User->where($UMap)->setInc('diamond',$gold);
			$this->insert_account(10,2,$data['uid'],$gold,true);
			echo '<xml>';
			echo '<return_code><![CDATA[SUCCESS]]></return_code>';
			echo '<return_msg><![CDATA[OK]]></return_msg>';
			echo '</xml>';
		}else{
			echo '3';die;
		}
	}

	//微信支付时检查订单的真实性，防止地址暴露不安全
	public function checkOrderTrue($transaction_id,$trade_type){
		$appid = 'wxe43bb7bc7cb3367d';
		$mch_id = '1302281301';
		$key = 'WyO9IBhhJnHxzSMMPnBpX22zccvFFWZi';

		if($trade_type == 'JSAPI'){
			$appid = 'wxcb820196a31b4bf5';
			$mch_id = '1374587902';
			$key = '4598532bBs5510368sg54123rmnj18FA';
		}

		//查询订单
		$url_get = 'https://api.mch.weixin.qq.com/pay/orderquery';
		$data['appid'] = $appid;//微信公众号
		$data['mch_id'] = $mch_id;
		$data['transaction_id'] = $transaction_id;
		$data['nonce_str'] = 'b5hGRYl7pha'+ rand(10000,9999);//随机字符串
        ksort($data);//排序
        $buff = "";
        foreach ($data as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $string = trim($buff, "&");
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);//生成的签名
		$xml = "<xml><appid>".$appid."</appid><mch_id>".$mch_id."</mch_id><nonce_str>".$data['nonce_str']."</nonce_str><transaction_id>".$transaction_id."</transaction_id><sign>".$result."</sign></xml>";//拼接要发送的xml

		$backxml = $this->c_url($url_get,$xml);//发送查询
		$prin_data = json_decode(json_encode(simplexml_load_string($backxml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		if(array_key_exists("return_code", $prin_data)
			&& array_key_exists("result_code", $prin_data)
			&& $prin_data["return_code"] == "SUCCESS"
			&& $prin_data["result_code"] == "SUCCESS"){
			//订单查询成功
			return $prin_data['out_trade_no']; //商户的订单号
		}
		return false;
	}


	//支付宝返回的地址
	public function alipaycallback(){
		$scale = C('scale');//兑换比例
		$out_trade_no = I('out_trade_no');//商户的支付订单
		$trade_on = I('trade_no'); //支付宝的支付订单
		$rmb = I('total_fee');//用户支付的金额.单位:元
		$UserCharge = M('UserCharge');
		$data = $UserCharge->where(array('order_no' => $out_trade_no,'status' => 0))->find(); //查询订单的真实性
		if(!$data || $rmb*$scale != $data['amount']){ //验证
			echo "fail";die;
		}
		if ($rmb <= 4) {
			$add = 0;
		}
		if ($rmb <= 9 && $rmb >= 5) {
			$add = 0;
		}
		if ($rmb <= 29 && $rmb >= 10) {
			$add = 0;
		}
		if ($rmb <= 99 && $rmb >= 30) {
			$add = 0;
		}
		if ($rmb >= 100) {
			$add = 0;
		}
		$gold = $rmb * $scale + $add;
		
		if(self::$act){
			$gold += floor($gold*self::$act_num); //加送
		}

		$_data['modify_time'] = time();
		$_data['status'] = 1;
		$_data['gold'] = $gold;
		$_data['pay_order'] = $trade_on;//支付宝的订单号
		$result = $UserCharge->where(array('order_no' => $out_trade_no))->save($_data);
		// echo $UserCharge->getLastSql();
		if($result){
			$UMap['id'] = $data['uid'];
			$User = M('UserUser'); 
			$User->where($UMap)->setInc('diamond',$gold);
			$this->insert_account(10,2,$data['uid'],$gold,true);
			$this->first_charage($data['id'],$data['uid'],$rmb);
			echo "success";
		}else{
			echo "fail";die;
		}
	}
    //发送请求
    protected function c_url($url,$tree,$request='POST'){
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        //设置请求的方式,GET或POST
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, $request );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        //请求的头信息
        if(!empty($header)){
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );   
        }
       
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $tree );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $res = curl_exec ( $ch );
        curl_close ( $ch );
        return $res;
    }

    //首冲
    private function first_charage($charage_id,$uid,$rmb){
    	$UserCharge = M('UserCharge');
    	//检测用户是否首冲过
    	$res = $UserCharge->where(array('status' => 1,'first_charage' => 1,'uid' => $uid))->find();
    	if($res){
    		return false;
    	}else{
    		$add = $this->charage_reward($rmb,'android');
    		if($add > 0){

    			$this->insert_account(10,2,$uid,$add,true); //添加记录

    			M('UserUser')->where(array('id' => $uid))->setInc('diamond',$add);

    			$UserCharge->where(array('id' => $charage_id))->setInc('gold',$add);

    		}

    		$UserCharge->where(array('id' => $charage_id))->setField('first_charage',1); //更改首冲状态
    	}
    }

    //$type ios | android
    private function charage_reward($rmb,$type){
    	if($type == 'android'){
			if ($rmb <= 29) {
				$add = 0;
			}
			if ($rmb <= 97 && $rmb >= 30) {
				$add = 18;
			}
			if ($rmb <= 197 && $rmb >= 98) {
				$add = 78;
			}
			if ($rmb <= 327 && $rmb >= 198) {
				$add = 168;
			}
			if ($rmb <= 647 && $rmb >= 328) {
				$add = 298;
			}
			if ($rmb >= 648) {
				$add = 688;
			}
    	}
    	if($type == 'ios'){
			if ($rmb <= 29) {
				$add = 0;
			}
			if ($rmb <= 97 && $rmb >= 30) {
				$add = 18;
			}
			if ($rmb <= 197 && $rmb >= 98) {
				$add = 58;
			}
			if ($rmb <= 327 && $rmb >= 198) {
				$add = 118;
			}
			if ($rmb <= 647 && $rmb >= 328) {
				$add = 208;
			}
			if ($rmb >= 648) {
				$add = 478;
			}
    	}
    }
    //支付宝html5支付
    // public function alihtmlpay(){
    // 	$money = I('money'); //金额
    // 	$apiToken = I('apiToken');//公共的
    // 	$token = I('token');
    // 	echo '<form id="alipay" name="alipay" method="post" action="http://betapi.sgamer.com/index.php?g=app&m=user&a=recharge">';
    // 	echo '<input type="hidden" value="alipay_wap" name="type">'; //支付的类型
    // 	echo '<input type="hidden" value="'.$token.'" name="token">'; //支付的类型
    // 	echo '<input type="hidden" value="' . $apiToken . '" name="apiToken">'; //支付的类型
    // 	echo '<input type="hidden" value="' . $money . '" name="money">'; //支付的类型
    // 	echo '<input type="submit" value="submit" style="display:none;">'; //支付的类型
    // 	echo '</form>';
    // 	echo '<script>document.forms["alipay"].submit();</script>';
    // }

}
?>