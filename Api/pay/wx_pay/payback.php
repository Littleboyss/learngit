<?php
//此处用来接收收数据
$res = file_get_contents('php://input');
$msg = (array) simplexml_load_string($res,'SimpleXMLElement',LIBXML_NOCDATA); //接收的数据转换成数组
// file_put_contents('./pauurl11.txt', json_encode($msg));
if($msg['return_code'] == 'SUCCESS'){ //成功

	$data['nonce_str'] = $msg['nonce_str']; //签名验证的md5值
	$data['cash_fee'] = $msg['cash_fee'];//用户支付的金额
	$data['out_trade_no'] = $msg['out_trade_no']; //商户的订单号
	$data['transaction_id'] = $msg['transaction_id']; //微信的订单号
	$data['trade_type'] = $msg['trade_type']; //支付的方式
	$verfiy_url = 'http://api.aifamu.com/index.php?g=api&m=pay&a=wxcallback'; //微信支付的处理url

	$request_url = $verfiy_url .'&'. http_build_query($data);
	// file_put_contents('./pauurl1.txt', $request_url);
	echo file_get_contents($request_url);
	
}