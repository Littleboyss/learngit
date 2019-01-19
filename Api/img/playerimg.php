<?php
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