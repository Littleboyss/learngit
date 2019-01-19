<?php
/**
 * conf文件用户控制前端
 * @author wangh 2017.8.7
 */
class ConfAction extends Action{

	public function conf(){
		$data = C('is_maintenance_info');
		exit(json_encode($data));
	}

}