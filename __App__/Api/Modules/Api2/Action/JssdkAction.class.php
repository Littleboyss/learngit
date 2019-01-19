<?php

class JssdkAction extends CommonAction{
    protected $appid;
    protected $appsecret;
    protected $access_token;

    public function __construct(){
        $this->appid = 'wxcb820196a31b4bf5';
        $this->appsecret = 'b779850be348a07e3e9816e1f1ca6e44';
        $this->access_token = $this->get_access_token();
    }

    /**
     * 获取access_token
     * 两小时过期后自动获取最新的access_token
     * @return access_token|空字符串
     */
    public function get_access_token(){
        $cache_name = 'wx_access_token_aaa';
        $access_token = S($cache_name);
        if(!$access_token){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
            $data = json_decode($this->request($url),true);
            if($data['errcode'] == 0){
                $access_token = $data['access_token'];
                S($cache_name,$access_token,7000);//过期时间2小时
            }else{
                $access_token = '';
            }
        }
        return $access_token;
    }

    /**
     * 签名生成规则的其一参数
     * @return jsapi_ticket
     */
    public function get_ticket(){
        $cache_name = 'wx_ticket_sssa';
        $ticket = S($cache_name);
        if(!$ticket){
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->access_token.'&type=jsapi';
            $data = $data = json_decode($this->request($url),true);
            if($data['errcode'] == 0){
                $ticket = $data['ticket'];
                S($cache_name,$ticket,7000);//过期时间2小时
            }else{
                $ticket = '';
            }
        }
        return $ticket;
    }

    /**
     * @param int $length 随机字符串的长度
     * @return string 返回随机字符串
     */
    protected function get_noncestr($length = 8){
        $chars = "abcdefghijklmnopqrstuvwxyzABC";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function getjssdk(){
        $url = I('url');
        $url = urldecode($url);
        $ticket = $this->get_ticket();

        if($ticket == ''){
            exit('share is error');
        }
        $nonceStr = $this->get_noncestr();
        $timestamp = time();
        $string = 'jsapi_ticket='.$ticket.'&noncestr='.$nonceStr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($string);
        $arr = array(
            'url' => $url,
            'appid' =>  $this->appid, // 必填，公众号的唯一标识
            'noncestr' =>   $nonceStr, // 必填，生成签名的随机串
            'timestamp' =>  $timestamp, // 必填，生成签名的时间戳
            'signature'=>   $signature
        );
        exit(json_encode($arr));
    }

    //微信上传图片到服务器上
    public function wxupload(){
        $media_id = $this->_data['media_id'];
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$media_id;
        $data = file_get_contents($url);

        file_put_contents('./url.txt', $url);

        echo $data;
    }

    /**
     * @param $url 需要发送请求的URL地址
     * @param $https 是http请求还是https请求
     * @param string $method 是get还是post请求方式
     * @param null $data post请求要带上的数据
     * @return mixed 请求返回结果字符串
     *
     */
    protected function request($url,$https=true,$method='get',$data=null){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不输出在浏览器上，保存在变量中
        if($https === true){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 不从证书中检查SSL加密算法是否存在
        }
        if($method === 'post'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $content = curl_exec($ch);//发送请求
        curl_close($ch);//关闭资源
        return $content;
    }

}