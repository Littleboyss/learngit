<?php
header('content-type:image/jpg;');
@$id = $_GET['id'];
if (is_null($id)) {
	$content=file_get_contents('./use.jpg');
}else{
	$dir = floor($id/500);
	$physical_path = './'.$dir.'/'.$id.'.jpg';
	// echo $physical_path;die;
	if (file_exists($physical_path)) {
		$content=file_get_contents($physical_path);
	}else{
		$content=file_get_contents('./use.jpg');
	}
}
echo $content;
?>