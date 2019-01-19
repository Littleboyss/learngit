<?php


function is_mobile(){
        $user_agentq = $_SERVER['HTTP_USER_AGENT'];
        $user_agent=strtolower($user_agentq);
        $mobile_agents = array("240x320","iphone","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (strpos($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }        
   
    return $is_mobile;
}

if(is_mobile()){
	header("Location: http://www.aifamu.com/client/wapapp/");
}else{
	header("Location: http://www.aifamu.com/client/app/");
}



// 开启调试模式
define('APP_DEBUG', true);

// APP常量定义
define('THANK_PATH', '../../ThinkPHP/');
define('APP_PATH', '../__App__/Web/');
define('APP_NAME', '爱伐木');

// 加载框架入口文件
require(THANK_PATH.'ThinkPHP.php');