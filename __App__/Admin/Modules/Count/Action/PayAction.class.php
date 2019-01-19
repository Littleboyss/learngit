<?php

//付费分析
class PayAction extends AdminAction{
	private static $scale = 10;

	//付费排行榜
	public function top(){

		if(IS_POST){
			$UserCharge = M('UserCharge');

			$get_day = I('day') ? I('day') : 7; //查询天数,默认7天
			$now_time = time();
			$start_time = I('start_time') ? strtotime(I('start_time')) : $now_time-($get_day*24*3600); //统计的开始时间
			$end_time = I('end_time') ? strtotime(I('end_time')) : $now_time; //统计的结束时间

			$Map['add_time'] = array(array('gt',$start_time),array('lt',$end_time),'and'); //时间条件
			$Map['status'] = 1;
			
			$data = $UserCharge->where($Map)->select();

			foreach ($data as $key => $value) {
				$data[$key]['add_time'] =  date('Y-m-d H:i',$value['add_time']);
				$data[$key]['pay_money'] = $value['amount'] / self::$scale;
			}

			$this->returnMsg(0,'success',$data);

		}else{
			$this->display();
		}

	}

	//付费趋势
	public function trend(){

		if(IS_POST){
			$UserCharge = M('UserCharge');

			$get_day = I('day') ? I('day') : 7; //查询天数,默认7天
			$now_time = time();
			$start_time = I('start_time') ? strtotime(I('start_time')) : $now_time-($get_day*24*3600); //统计的开始时间
			$end_time = I('end_time') ? strtotime(I('end_time')) : $now_time; //统计的结束时间

			$Map['add_time'] = array(array('gt',$start_time),array('lt',$end_time),'and'); //时间条件
			$Map['status'] = 1;

			$data = $UserCharge->where($Map)->select();
			$_data = $this->c_data($data,$start_time,$end_time);
			if($_data){
				$this->returnMsg(0,'success',$_data);
			}else{
				$this->returnMsg(1,'数据错误');
			}
		}else{
			$this->display();
		}

	}

	// $data 数据时间
	// $start_time $end_time 时间戳
	private function c_data($data,$start_time,$end_time){

		if($start_time > $end_time){
			return false;
		}

		$dayarr = array();  
		$dayarr[] = date('Y-m-d',$start_time); // 当前日;  

		while( ($start_time = strtotime('+1 day', $start_time)) <= $end_time){  
		      $dayarr[] = date('Y-m-d',$start_time); // 取得递增月;   
		}
		$_data = array();

		foreach ($dayarr as $key => $value) {
			foreach ($data as $k => $v) {
				$data_time = date('Y-m-d',$v['add_time']);
				if($value == $data_time){
					$_data['pay_count'][$key] += 1;
					$_data['pay_money'][$key] += $v['amount']/self::$scale;

					$_data['pay_uid'][$key][] = $v['uid'];

				}else{
					$_data['pay_count'][$key] += 0;
					$_data['pay_money'][$key] += 0;
					$_data['pay_uid'][$key][] = 0;

				}

			}
		}

		foreach ($_data['pay_uid'] as $key => $value) {
			
			foreach ($value as $k => $v) {
				if($v == 0){
					unset($value[$k]);
				}
			}

			$value = array_values($value);

			if($value[0] == 0 || is_null($value)){
				$_data['pay_uid'][$key] = 0;
			}else{
				$_data['pay_uid'][$key] = count(array_unique($value));
			}
			
		}

		$_data['time'] = $dayarr;
		return $_data;
		// print_r($_data);
	}


}