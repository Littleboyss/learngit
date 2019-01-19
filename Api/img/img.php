<?php
//允许跨域请求 , 是HTML5提供的方法，对跨域访问提供了很好的支持
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
header('content-type:image/jpg;');
@$id = $_GET['id']; //球员的id
$physical_path = './playerimg/'.$id.'.png';
if (file_exists($physical_path)) {
	$content = file_get_contents($physical_path);
}else{
	$content = ''; //默认为空
}
echo $content;
?>