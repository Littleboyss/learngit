<?php
/**
 * 
 * @author wangh 2017.2.17
 */
class ExtraAction extends LoginAction{

	public function slide(){
		$type = $this->_data['type'];
		if($type && is_numeric($type)){
			$Map['type'] = $type;
		}

		$cache_name = 'slides'.$type;
		$data = $this->cache('get',$cache_name);
		if(!$data){
			$AdminSlide = M('AdminSlide');
			$data = $AdminSlide->order('sort asc')->where($Map)->select();
			$this->cache('set',$cache_name,$data,3600*5);
		}
		$this->returnMsg(0,'extra',$data);
	}
	//获取球员的比赛的记录
	public function playermatchdata(){
		$player_id = $this->_data['player_id'];
		if(!is_numeric($player_id)){
			$this->returnMsg(1);
		}
		//根据时间查询赛季的数据
		$now_time = time();
		$season_year = date('Y',$now_time); //年份
		$season_month = date('m',$now_time); //月份
		if($season_month < 11){
			$season_year = $season_year - 1;
		}
		$Map['season'] = $season_year; //赛季
		$Map['player_id'] = $player_id; // 球员的id
		$Map['is_join'] = 1;//上场有过得分的情况
		$PlayerMatchData = M('PlayerMatchData');
		$match_data = $PlayerMatchData->where($Map)->order('add_time desc')->select(); //获取该球员当前赛季的全部比赛数据

		// echo $PlayerMatchData->getLastSql();die;
		//赛季总等
		$get_score = 0; //得分
		$three_point = 0;//三方
		$backboard = 0; //篮板分
		$help_score = 0;//助攻分
		$hinder_score = 0;//抢断分
		$cover_score = 0;//封盖分
		$mistake_score = 0;//失误分
		$play_time = 0; //时间
		//近10场得分
		$get_score_10 = 0; //得分
		$three_point_10 = 0;//三方
		$backboard_10 = 0; //篮板分
		$help_score_10 = 0;//助攻分
		$hinder_score_10 = 0;//抢断分
		$cover_score_10 = 0;//封盖分
		$mistake_score_10 = 0;//失误分
		$play_time_10 = 0; //时间
		$MatchList = M('MatchList');

		$last_ten_score = array();

		foreach ($match_data as $key => $value) {
			if($key <= 9){
				$get_score_10 += $value['get_score'];//得分
				$three_point_10 += $value['three_point'];//三方
				$backboard_10 += $value['backboard']; //篮板分
				$help_score_10 += $value['help_score'];//助攻分
				$cover_score_10 += $value['cover_score'];//封盖分
				$mistake_score_10 += $value['mistake_score'];//失误分
				$last_ten_score[] = $value['get_score'];
				$play_time_10 += $value['play_time']; //时间
			}
			$get_score += $value['get_score']; //得分
			$three_point += $value['three_point'];//三方
			$backboard += $value['backboard']; //篮板分
			$help_score += $value['help_score']; //助攻分
			$cover_score += $value['cover_score'];//封盖分
			$mistake_score += $value['mistake_score'];//失误分
			$play_time += $value['play_time']; //时间
			$match_data[$key]['match_info'] = M('MatchList')->where(array('id' => $value['match_id']))->find();
		}
		$match_num = count($match_data);//赛季比赛的场数
		//计算赛季平均分
		$season_avg = array();
		$season_avg['get_score'] = $get_score/$match_num;
		$season_avg['three_point'] = $three_point/$match_num;
		$season_avg['backboard'] = $backboard/$match_num;
		$season_avg['help_score'] = $help_score/$match_num;
		$season_avg['hinder_score'] = $hinder_score/$match_num;
		$season_avg['cover_score'] = $cover_score/$match_num;
		$season_avg['mistake_score'] = $mistake_score/$match_num;
		$season_avg['play_time'] = $play_time/$match_num;
		$season_avg['score'] = $this->scorerule(1,$season_avg);
		$_data['season_avg'] = $season_avg;
		$_data['match_info'] = $match_data;

		//获取删除这段代码
		for ($i=0; $i < 10; $i++) { 
			$last_ten_score[$i] = rand(20,50);
		}
		//
		$_data['last_ten_score'] = $last_ten_score;

		//积分近10场平均分
		$season_avg_10 = array();
		$season_avg_10['get_score'] = $get_score_10/10;
		$season_avg_10['three_point'] = $three_point_10/10;
		$season_avg_10['backboard'] = $backboard_10/10;
		$season_avg_10['help_score'] = $help_score_10/10;
		$season_avg_10['hinder_score'] = $hinder_score_10/10;
		$season_avg_10['cover_score'] = $cover_score_10/10;
		$season_avg_10['mistake_score'] = $mistake_score_10/10;
		$season_avg_10['score'] = $this->scorerule(1,$season_avg_10);
		$season_avg_10['play_time'] = $play_time_10/$match_num;
		$_data['season_avg_10'] = $season_avg_10;
		$this->returnMsg(0,'extra',$_data);
	}

	// 获取lol选手数据
	public function lolplayermatchdata(){
		$player_id = $this->_data['player_id'];
		if(!is_numeric($player_id)){
			$this->returnMsg(1);
		}
		//根据时间查询赛季的数据
		$now_time = time();
		$season_year = date('Y',$now_time); //年份
		$season_year = $season_year - 0;
		//$Map['season'] = $season_year; //赛季
		$Map['player_id'] = $player_id; // 球员的id
		$PlayerMatchData = M('PlayerMatchDataLol');
		// 获取球员数据
		$play_data = M('MatchPlayerWcg')->where(array('id'=>$player_id))->getField('team_id,position,average,KDA');
		if (empty($play_data)) {
			$this->returnMsg(1,'room');
		}
		$team_id = $play_data['team_id'];
		$map_team = M('MatchTeam')->getField('id,name');
		// 以下为获取下一场信息
		$MatchList = M('MatchList');
		$where ='(match_time > '.time().') and (team_a = '.$team_id.' or team_b = '.$team_id.'  )';
		$data_match = $MatchList->where($where)->order('match_time asc')->find();
		if($data_match){
			if ($team_id != $data_match['team_a']) {
				$opp_id = $data_match['team_a'];
			}else{
				$opp_id = $data_match['team_b'];
			}
			$_data['next_match']['opponents'] = $map_team[$opp_id];// 下一场对手名称
			$_data['next_match']['match_time'] = date('Y-m-d H:i:s',$data_match['match_time']);// 下一场比赛时间
		}else{
			$_data['next_match']['opponents'] = '';// 下一场对手名称
			$_data['next_match']['match_time'] = '';
		}
		$position = $play_data['position'];
		if ($position != 6) {
			$Map['is_join'] = 1;//上场有过得分的情况
		}
		$Map['score'] =array(array('NEQ','0:0'),array('NEQ','0：0'));
		$match_data = $PlayerMatchData->where($Map)->order('addtime desc')->select(); //获取该球员当前赛季的全部比赛数据
		// 获取所有的球员
		foreach ($match_data as $key => $value) {
			$match_data[$key]['scores'] = $value['scores']/10;
			$match_data[$key]['KDA'] = number_format(($value['kill']+$value['assists'])/$value['death'],1);
			//$match_info = M('match_list')->where(array('id'=>$value['match_id']))->find();
			//$match_data[$key]['time'] = date('m/d',$match_info['match_time']);// 比赛时间
			//if ($team != $match_info['team_a']) {
				//$team_id = $match_info['team_a'];// 比赛对手id
				//$match_data[$key]['score'] = strrev($value['score']);// 翻转比分，保证数据正确
			//}else{
				//$team_id = $match_info['team_b'];// 比赛对手id
			//}
			//$opponents = M('MatchTeam')->where('id = '.$team_id)->getField('e_name');
			$match_list = $MatchList->where('id = '.$value['match_id'])->find();
			if ($team_id != $match_list['team_a']) {
				$opp_id = $match_list['team_a'];
			}else{
				$opp_id = $match_list['team_b'];
			}

			$match_data[$key]['opponents'] = $map_team[$opp_id];// 比赛对手队伍名称
			unset($match_data[$key]['opp']);
			$match_data[$key]['time'] = $match_data[$key]['date'];// 比赛时间
			unset($match_data[$key]['date']);
			// 去除添加时间
			unset($match_data[$key]['addtime']);
			unset($match_data[$key]['id']);
		}
		$_data['all'] =$match_data;// 所有已经完赛数据
		$all_avg = $this->array_avg($match_data);// 所有的数据平均值
		$_data['all_avg'] = $all_avg; 
		$_data['ten'] =array_slice($match_data, 0,10);// 最近十条数据
		$_data['ten_avg'] = $this->array_avg($_data['ten']);// 所有的数据平均值
		// 获取赛季数据
		foreach ($match_data as $i => $item) {
			if ($item['season'] == $season_year) {
				$season_data[] =$item;
			}
		}
		$_data['season'] =$season_data;// 本赛季数据
		$season_avg = $this->array_avg($_data['season']);// 所有的数据平均值
		$_data['season_avg'] = $season_avg;
		unset($_data['season_avg']['time']);
		unset($_data['season_avg']['season']);
		unset($_data['all_avg']['time']);
		unset($_data['all_avg']['season']);
		unset($_data['ten_avg']['time']);
		unset($_data['ten_avg']['season']);
		foreach ($_data['all_avg'] as $key => $value) {
			$_data['all_avg'][$key] = number_format($value,1);
		}
		foreach ($_data['season_avg'] as $key => $value) {
			$_data['season_avg'][$key] = number_format($value,1);
		}
		foreach ($_data['ten_avg'] as $key => $value) {
			$_data['ten_avg'][$key] = number_format($value,1);
		}
		if (count($_data['all'])>=10) {
			$_data['last_ten_score'] = array_column($match_data,'scores');
		}else{
			//获取删除这段代码
			for ($i=0; $i < 10; $i++) { 
				$last_ten_score[$i] = rand(20,31);
			}
			//
			$_data['last_ten_score'] = $last_ten_score;
		}
		$this->returnMsg(0,'extra',$_data);
	}
	// 获取dota2选手数据
	public function dota2playermatchdata(){
		$player_id = $this->_data['player_id'];
		if(!is_numeric($player_id)){
			$this->returnMsg(1);
		}
		//根据时间查询赛季的数据
		$now_time = time();
		$season_year = date('Y',$now_time); //年份
		$season_year = $season_year - 0;
		//$Map['season'] = $season_year; //赛季
		$Map['player_id'] = $player_id; // 球员的id
		$PlayerMatchData = M('PlayerMatchDataDota2');
		// 获取球员数据
		$play_data = M('MatchPlayerWcg')->field('team_id,position,average,KDA')->where(array('id'=>$player_id))->find();
		if (empty($play_data)) {
			$this->returnMsg(1,'room');
		}
		$team_id = $play_data['team_id'];
		$map_team = M('MatchTeam')->getField('id,name');
		// 以下为获取下一场信息
		$MatchList = M('MatchList');
		$where ='(match_time > '.time().') and (team_a = '.$team_id.' or team_b = '.$team_id.'  )';
		$data_match = $MatchList->where($where)->order('match_time asc')->find();
		if($data_match){
			if ($team_id != $data_match['team_a']) {
				$opp_id = $data_match['team_a'];
			}else{
				$opp_id = $data_match['team_b'];
			}
			$_data['next_match']['opponents'] = $map_team[$opp_id];// 下一场对手名称
			$_data['next_match']['match_time'] = date('Y-m-d H:i:s',$data_match['match_time']);// 下一场比赛时间
		}else{
			$_data['next_match']['opponents'] = '';// 下一场对手名称
			$_data['next_match']['match_time'] = '';
		}
		$position = $play_data['position'];
		if ($position != 6) {
			$Map['is_join'] = 1;//上场有过得分的情况
		}
		$Map['score'] =array(array('NEQ','0:0'),array('NEQ','0：0'));
		//获取该球员当前赛季的全部比赛数据
		$match_data = $PlayerMatchData->where($Map)->order('addtime desc')->select(); 
		// 获取所有的球员
		foreach ($match_data as $key => $value) {
			$match_data[$key]['scores'] = $value['scores']/10;
			$match_data[$key]['KDA'] = number_format(($value['kill']+$value['assists'])/$value['death'],1);
			//$match_info = M('match_list')->where(array('id'=>$value['match_id']))->find();
			//$match_data[$key]['time'] = date('m/d',$match_info['match_time']);// 比赛时间
			//if ($team != $match_info['team_a']) {
				//$team_id = $match_info['team_a'];// 比赛对手id
				//$match_data[$key]['score'] = strrev($value['score']);// 翻转比分，保证数据正确
			//}else{
				//$team_id = $match_info['team_b'];// 比赛对手id
			//}
			//$opponents = M('MatchTeam')->where('id = '.$team_id)->getField('e_name');
			$match_list = $MatchList->where('id = '.$value['match_id'])->find();
			if ($team_id != $match_list['team_a']) {
				$opp_id = $match_list['team_a'];
			}else{
				$opp_id = $match_list['team_b'];
			}

			$match_data[$key]['opponents'] = $map_team[$opp_id];// 比赛对手队伍名称
			unset($match_data[$key]['opp']);
			unset($match_data[$key]['dragons']);
			$match_data[$key]['time'] = $match_data[$key]['date'];// 比赛时间
			unset($match_data[$key]['date']);
			// 去除添加时间
			unset($match_data[$key]['addtime']);
			unset($match_data[$key]['id']);
		}
		$_data['all'] =$match_data;// 所有已经完赛数据
		$all_avg = $this->array_avg($match_data);// 所有的数据平均值
		$_data['all_avg'] = $all_avg; 
		$_data['ten'] =array_slice($match_data, 0,10);// 最近十条数据
		$_data['ten_avg'] = $this->array_avg($_data['ten']);// 所有的数据平均值
		// 获取赛季数据
		foreach ($match_data as $i => $item) {
			if ($item['season'] == $season_year) {
				$season_data[] =$item;
			}
		}
		$_data['season'] =$season_data;// 本赛季数据
		$season_avg = $this->array_avg($_data['season']);// 所有的数据平均值
		$_data['season_avg'] = $season_avg;		
		unset($_data['season_avg']['time']);
		unset($_data['season_avg']['season']);
		unset($_data['all_avg']['time']);
		unset($_data['all_avg']['season']);
		unset($_data['ten_avg']['time']);
		unset($_data['ten_avg']['season']);
		foreach ($_data['all_avg'] as $key => $value) {
			$_data['all_avg'][$key] = number_format($value,1);
		}
		foreach ($_data['season_avg'] as $key => $value) {
			$_data['season_avg'][$key] = number_format($value,1);
		}
		foreach ($_data['ten_avg'] as $key => $value) {
			$_data['ten_avg'][$key] = number_format($value,1);
		}
		if (count($_data['all'])>=10) {
			$_data['last_ten_score'] = array_column($match_data,'scores');
		}else{
			//获取删除这段代码
			for ($i=0; $i < 10; $i++) { 
				$last_ten_score[$i] = rand(20,31);
			}
			//
			$_data['last_ten_score'] = $last_ten_score;
		}
		$this->returnMsg(0,'extra',$_data);
	}
	// 获取球员相关比赛信息
	public function get_player_news(){
		$PlayerNews = M ('PlayerNews');
		$where['project_id'] = $this->_data['project_id'];
		$where['player_id'] = $this->_data['player_id'];
		$data = $PlayerNews->field('title,detail,addtime')->where($where)->select();
		foreach ($data as $key => $value) {
			$data[$key]['time'] = $this->format_date($value['addtime']);
			unset($data[$key]['addtime']);
		}
		if ($data) {
			$this->returnMsg(0,'turnplate',$data);
		}else{
			$this->returnMsg(12,'user');
		}
	}
	private function format_date($time){
	    $t=time()-$time;
	    $f=array(
	        '31536000'=>'年',
	        '2592000'=>'个月',
	        '604800'=>'星期',
	        '86400'=>'天',
	        '3600'=>'小时',
	        '60'=>'分钟',
	        '1'=>'秒'
	    );
	    foreach ($f as $k=>$v)    {
	        if (0 !=$c=floor($t/(int)$k)) {
	            return $c.$v.'前';
	        }
	    }
	}
}