<?php

//虚拟币分析控制器
class CurrencyAction extends AdminAction{
	//来源
	public function from(){
		if(IS_POST){
			$conf = array(4 => '游戏奖励',10 => '付费购买');
			$get_day = I('day') ? I('day') : 7;

			$now_time = time();

			$start_time = I('start_time') ? strtotime(I('start_time')) : $now_time-($get_day*24*3600); //统计的开始时间
			$end_time = I('end_time') ? strtotime(I('end_time')) : $now_time; //统计的结束时间

			$UserAccount = M('UserAccount');
			$Map['addtime'] = array(array('gt',$start_time),array('lt',$end_time),'and'); //时间条件
			$Map['class_id'] = array(array('eq',4),array('eq',10),'or');
			$data = $UserAccount->where($Map)->select();
			// print_r($data);
			$_data = $this->c_data($data);

			// print_r($_data);die;

			if($_data === false){
				$this->returnMsg(1,'没有数据');
			}
			$all_data = array();
			foreach ($_data as $key => $value) {

				$all_data[4][] = array_sum($value[4]) ? array_sum($value[4]) : 0;
				$all_data[10][] = array_sum($value[10]) ? array_sum($value[10]) : 0;
		
				$all_data['time'][] = $key;
			}
			$this->returnMsg(0,'success',$all_data);

		}
		$this->display();
	}


	//格式化数据,按照日期分割
	//$type 1 获取 2支出
	private function c_data($data){
		if(!$data){
			return false;
		}
		$_data = array();
		foreach ($data as $key => $value) {
			$s_date = date('m-d',$value['addtime']);
			if($value['go_nums'] == 0){
				$_data[$s_date][$value['class_id']][] = $value['back_nums'];
			}else{
				$_data[$s_date][$value['class_id']][] = $value['go_nums'];
			}
		}

		// print_r($_data);die;
		return $_data;
	}


	public function consume(){
		if(IS_POST){

			$get_day = I('day') ? I('day') : 7;

			$now_time = time();

			$start_time = I('start_time') ? strtotime(I('start_time')) : $now_time-($get_day*24*3600); //统计的开始时间
			$end_time = I('end_time') ? strtotime(I('end_time')) : $now_time; //统计的结束时间

			$UserAccount = M('UserAccount');
			$Map['addtime'] = array(array('gt',$start_time),array('lt',$end_time),'and'); //时间条件

			$Map['class_id'] = array('eq',3); //竞猜投注

			$data = $UserAccount->where($Map)->select();

			if(!$data){
				$this->returnMsg(1,$UserAccount->getLastSql());
			}

			// echo $UserAccount->getLastSql();

			$_data = $this->c_data_c($data,$start_time,$end_time);

			// print_r($_data);
			$this->returnMsg(0,'success',$_data);

		}else{
			$this->display();
		}
		
	}

	private function c_data_c($data,$start_time,$end_time){

		if($start_time > $end_time){
			return false;
		}

		$dayarr = array(); //所有的日期
		$dayarr[] = date('Y-m-d',$start_time); // 当前日;  

		while( ($start_time = strtotime('+1 day', $start_time)) <= $end_time){  
		      $dayarr[] = date('Y-m-d',$start_time); // 取得递增月;   
		}

		// print_r($dayarr);

		$_data = array();

		foreach ($dayarr as $key => $value) {
			foreach ($data as $k => $v) {
				$data_time = date('Y-m-d',$v['addtime']);
				if($value == $data_time){
					$_data['pay_money'][$key] += $v['go_nums'];
					$_data['pay_uid'][$key] += 1;
				}else{
					$_data['pay_money'][$key] += 0;
					$_data['pay_uid'][$key] += 0;
				}

			}
		}
		$_data['time'] = $dayarr;
		return $_data;
		// print_r($_data);
	}

}