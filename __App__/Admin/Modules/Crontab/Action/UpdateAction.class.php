<?php
header('Content-type:text/html;charset=utf-8');
ini_set('max_execution_time', '0');
set_time_limit(0);
/**
 * @author chengy 2017.3.22
 */

class UpdateAction extends Action{
	//更新房间发布状态
	public function update_room(){
		$player_wcg = M('MatchPlayerWcg');
		$player = $player_wcg->select();
		$match_dota2 = M('PlayerMatchDataDota2');
		$match_lol = M('PlayerMatchDataLol');
		foreach ($player as $value) {
			if ($value['project_id'] == 5) {
				$datas = $match_lol->where('scores > 0 and player_id = '.$value['id'])->select();
			}elseif($value['project_id'] == 6) {
				$datas = $match_dota2->where('scores > 0 and player_id = '.$value['id'])->select();
			}
			$kill = 0;
			$assists = 0;
			$death = 0;
			foreach ($datas as $keys => $values) {
				$kill += $values['kill'];
				$assists += $values['assists'];
				$death += $values['death'];
			}
			$KDA = number_format(($kill + $assists )/$death,1);
			if ($KDA != 0) {
				$player_wcg->where('id ='.$value['id'])->setField('KDA',$KDA*10);
			}
		}

		//查询今天的房间开启
		$now_time = time();
		$Map['show_date'] = array(array('egt',strtotime(date('Y-m-d 00:00:00',$now_time))),array('elt',strtotime(date('Y-m-d 23:59:00',$now_time))), 'and'); //塞选比赛时间,只查询今天开赛的房间

		$data = M('MatchRoomInfo')->where($Map)->select();
		// echo M('MatchRoomInfo')->getLastSql();
		// print_r($data);
		$MatchRoom = M('MatchRoom');

		if($data){
			foreach ($data as $key => $value) {
				$MatchRoom->where(array('id' => $value['room_id']))->setField('status',1);
			}
			
		}

		//更新用户今天的投注的情况
		M('UserUser')->where(1)->setField('today_guess_num',0);


		//清空用户的阵容
		$Model = new Model();
		//进行原生的SQL查询
		$Model->query('truncate fa_user_lineup');

		//查询昨天的房间,关闭状态
		exit('更新完成');
	}

}