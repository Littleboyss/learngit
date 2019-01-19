<?php

//用户分享控制器
class UserAction extends AdminAction{
	//启动次数
	public function startcount(){
		if(IS_POST){
			$Count = M('Count');

			$get_day = I('day') ? I('day') : 7; //查询天数,默认7天
			$now_time = time();
			$start_time = I('start_time') ? strtotime(I('start_time')) : $now_time-($get_day*24*3600); //统计的开始时间
			$end_time = I('end_time') ? strtotime(I('end_time')) : $now_time; //统计的结束时间

			$Map['add_time'] = array(array('gt',$start_time),array('lt',$end_time),'and'); //时间条件
			// $Map['status'] = 1;
			
			$date_arr = $this->c_data_a($start_time,$end_time);

			$data = array();
			foreach ($date_arr as $key => $value) {
				$data['date'][] = $value;
				$f = $Count->where(array('date' => $value))->find();
				$data['start'][] = $f['start'] ? intval($f['start']) : 0;
				$data['new_user'][] = $f['new_user'] ? intval($f['new_user']) : 0;
				$data['pay_user'][] = $f['pay_user'] ? intval($f['pay_user']) : 0;
				$data['pay_money'][] = $f['pay_money'] ? intval($f['pay_money'])/100 : 0;
			}
			$this->returnMsg(0,'success',$data);
		}else{
			$this->display();
		}
	}
	//新增用户
	public function newuser(){

	}
	//付费用户数
	public function payuser(){

	}
	//付费金额
	public function paymoney(){

	}

	//格式化数据
	public function c_data_a($start_time,$end_time,$form = 'Ymd'){
		$dayarr = array();  
		$dayarr[] = date($form,$start_time); // 当前日;  
		while( ($start_time = strtotime('+1 day', $start_time)) <= $end_time){  
		      $dayarr[] = date($form,$start_time); // 取得递增月;   
		}
		return $dayarr;
	}
}