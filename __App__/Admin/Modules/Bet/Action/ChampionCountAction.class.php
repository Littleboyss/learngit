<?php
/**
 * @author wangh 2017.7.22
 */

class ChampionCountAction extends AdminAction{
	public function pvcount(){
		if(IS_POST){
			$ChampionCount = M('ChampionCount');

			$get_day = I('day') ? I('day') : 7; //查询天数,默认7天
			$now_time = time();
			$start_time = I('start_time') ? strtotime(I('start_time')) : $now_time-($get_day*24*3600); //统计的开始时间
			$end_time = I('end_time') ? strtotime(I('end_time')) : $now_time; //统计的结束时间

			$Map['add_time'] = array(array('gt',$start_time),array('lt',$end_time),'and'); //时间条件
			// $Map['status'] = 1;
			
			$date_arr = $this->c_data_a($start_time,$end_time);
			// print_r($date_arr);die;
			// $data = $ChampionCount->where($Map)->select();
			$data = array();
			// $i = 0;
			$IpCount = M('IpCount');

			foreach ($date_arr as $key => $value) {
				$data['date'][] = $value;
				$f = $ChampionCount->where(array('date' => $value))->find();
				$data['pv'][] = $f['pv'] ? intval($f['pv']) : 0;
				$data['share_pv'][] = $f['share_pv'] ? intval($f['share_pv']) : 0;
				$data['oher_pv'][] = $f['oher_pv'] ? intval($f['oher_pv']) : 0;
				// $i++;

				$ip_num = $IpCount->where(array('date' => $value))->count(); //当天的ip数
				// $IpCount->where(array('date' => $value))->sum('ip_request'); //当天ip请求次数
				$data['ip_num'][] = intval($ip_num);


			}

			// foreach ($data as $key => $value) {
			// 	$data[$key]['add_time'] =  date('Y-m-d H:i',$value['add_time']);
			// 	$data[$key]['pay_money'] = $value['amount'] / self::$scale;
			// }

			$this->returnMsg(0,'success',$data);

		}else{
			$this->display();
		}
	}

	public function c_data_a($start_time,$end_time,$form = 'Ymd'){
		$dayarr = array();  
		$dayarr[] = date($form,$start_time); // 当前日;  
		while( ($start_time = strtotime('+1 day', $start_time)) <= $end_time){  
		      $dayarr[] = date($form,$start_time); // 取得递增月;   
		}
		return $dayarr;
	}


	public function sharecount(){
		$UserChampion = M('UserChampion');
		$count = $UserChampion->where('champion_id=1 and is_share_in=1')->count();
		$data = M('Champion')->where(array('id' => 1))->find();
		echo '<h1>总投注人数:'.$data['guess_num'].'人</h1>';
		echo '<h1>分享总投注:'.$count.'人</h1>';
	}

}