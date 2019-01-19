<?php
header('Content-type:text/html;charset=utf-8');
//允许跨域请求 , 是HTML5提供的方法，对跨域访问提供了很好的支持
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
// header('Content-Type: application/json');

// 开启调试模式,上线后改为false
define('APP_DEBUG', true);

// APP常量定义
define('THANK_PATH', '../../ThinkPHP/');
define('APP_PATH', '../__App__/Api/');
define('APP_NAME', '影魔');
ini_set('session.cookie_lifetime',86400);

//定义app当前模块的版本
$version = @$_GET['version'];

$apptype = @$_GET['apptype'];

//定义版本
if($apptype == 'app'){
	if($version == 1){
		define('GROUP_NAME','App1');
	}elseif($version == 2){
		define('GROUP_NAME','App2');
	}else{
		define('GROUP_NAME','App1');
	}
}else{
	if($version == 1){
		define('GROUP_NAME','Api');
	}elseif($version == 2){
		define('GROUP_NAME','Api2');
	}else{
		define('GROUP_NAME','Api');
	}
}




// 加载框架入口文件
require(THANK_PATH.'ThinkPHP.php');