<?php
header('Content-type:text/html;charset=utf-8');
//数据更新类
class UpdateAction extends Action{

	public function update_match(){

		$match_id = @$_GET['match_id']; //比赛的id
		if(!$match_id) exit('参数错误,请填写比赛id');
		$match_data = M('MatchList')->where(array('id' => $match_id))->find();
		if(!$match_data){
			exit('数据库没有添加该场比赛');
		}
		// print_r($match_data);die;
		$match_code = $match_data['only_id']; //china.nba 比赛的唯一编码0041600314
		// $match_code = '0041600314'; //china.nba 比赛的唯一编码
		if(!$match_code){
			exit('数据源id不存在');
		}

		$match_info_url = 'http://china.nba.com/static/data/game/snapshot_%s.json'; //采集数据的json
		$match_josn = file_get_contents(sprintf($match_info_url,$match_code));
		if(!$match_josn){
			exit('无法获取比赛的数据信息,请检查match_code是否填写正确');
		}

		$_data = json_decode($match_josn,true);
		//更新比分和状态
		$match_info['score_a'] = $_data['payload']['boxscore']['homeScore']; //主队得分
		$match_info['score_b'] = $_data['payload']['boxscore']['awayScore']; //客队得分
		if($_data['payload']['boxscore']['status'] == 3){
			$match_info['match_status'] = 3;
		}
		M('MatchList')->where('id='.$match_data['id'])->save($match_info);
		// print_r($_data['payload']['homeTeam']['gamePlayers']);die; //主场team
		// print_r($_data['payload']['awayTeam']['gamePlayers']);die; //客场team
		$PlayerMatchData = M('PlayerMatchData');
		$players = $this->getallplayer(); //获取所有的选手



		foreach ($_data['payload']['homeTeam']['gamePlayers'] as $key => $value) {
			$mins = $value['statTotal']['mins']; //出场时间,没有时间的表示没有上场
			// if($mins == 0){
			// 	continue; 
			// }
			$player_id = $value['profile']['playerId']; //球员的id

			$score = $value['statTotal']['points']; //得分
			$rebs = $value['statTotal']['rebs']; //篮板
			$assists = $value['statTotal']['assists']; //助攻
			$steals = $value['statTotal']['steals']; //抢断
			$blocks = $value['statTotal']['blocks']; //盖帽
			$turnovers = $value['statTotal']['turnovers']; //失误
			$tpm = $value['statTotal']['tpm']; //三分

			//查询比赛是否存在,决定insert or update
			$Map = array('match_id' => $match_id,'player_id' => $player_id);
			$match_data_p = $PlayerMatchData->where($Map)->find();
			if($match_data_p){
				$data_['team_id'] = $players[$player_id]['team_id']; //球员的队伍id
				$data_['player_id'] = $player_id; //球员的id
				$data_['match_id'] = $match_id;//比赛的id
				$data_['season'] = 2016; //赛季
				$data_['match_time'] = $match_data['match_time']; //比赛时间
				$data_['is_join'] = $mins == 0 ? 2 : 1; //是否上场过
				$data_['play_time'] = $mins; //出场时间
				$data_['get_score'] = $score; //得分
				$data_['three_point'] = $tpm; //三分
				$data_['backboard'] = $rebs; //篮板
				$data_['help_score'] = $assists; //助攻
				$data_['hinder_score'] = $steals; //抢断
				$data_['mistake_score'] = $turnovers; //失误
				$data_['cover_score'] = $blocks; //封盖
				$data_['score'] = $this->scorerule($data_)*10; //积分


				$data_['team_a_id'] = $match_data['team_a'];
				$data_['team_b_id'] = $match_data['team_b'];
				$data_['team_a_score'] = $match_data['score_a'];
				$data_['team_b_score'] = $match_data['score_b'];
	

				$PlayerMatchData->where($Map)->save($data_);
			}else{
				$data_['team_id'] = $players[$player_id]['team_id']; //球员的队伍id
				$data_['player_id'] = $player_id; //球员的id
				$data_['match_id'] = $match_id;//比赛的id
				$data_['season'] = 2016; //赛季
				$data_['match_time'] = $match_data['match_time']; //比赛时间
				$data_['is_join'] = $mins == 0 ? 2 : 1; //是否上场过
				$data_['play_time'] = $mins; //出场时间
				$data_['get_score'] = $score; //得分
				$data_['three_point'] = $tpm; //三分
				$data_['backboard'] = $rebs; //篮板
				$data_['help_score'] = $assists; //助攻
				$data_['hinder_score'] = $steals; //抢断
				$data_['mistake_score'] = $turnovers; //失误
				$data_['cover_score'] = $blocks; //封盖
				$data_['score'] = $this->scorerule($data_)*10; //积分

				$data_['team_a_id'] = $match_data['team_a'];
				$data_['team_b_id'] = $match_data['team_b'];
				$data_['team_a_score'] = $match_data['score_a'];
				$data_['team_b_score'] = $match_data['score_b'];

				$PlayerMatchData->add($data_); //添加数据
			}
		}

		unset($data_);
		foreach ($_data['payload']['awayTeam']['gamePlayers'] as $key => $value) {
			$mins = $value['statTotal']['mins']; //出场时间,没有时间的表示没有上场
			// if($mins == 0){
			// 	continue;
			// }
			$player_id = $value['profile']['playerId']; //球员的id

			$score = $value['statTotal']['points']; //得分
			$rebs = $value['statTotal']['rebs']; //篮板
			$assists = $value['statTotal']['assists']; //助攻
			$steals = $value['statTotal']['steals']; //抢断
			$blocks = $value['statTotal']['blocks']; //盖帽
			$turnovers = $value['statTotal']['turnovers']; //失误
			$tpm = $value['statTotal']['tpm']; //三分

			//查询比赛是否存在,决定insert or update
			$Map = array('match_id' => $match_id,'player_id' => $player_id);
			$match_data_p = $PlayerMatchData->where($Map)->find();
			if($match_data_p){
				$data_['team_id'] = $players[$player_id]['team_id']; //球员的队伍id
				$data_['player_id'] = $player_id; //球员的id
				$data_['match_id'] = $match_id;//比赛的id
				$data_['season'] = 2016; //赛季
				$data_['match_time'] = $match_data['match_time']; //比赛时间
				$data_['is_join'] = $mins == 0 ? 2 : 1; //是否上场过
				$data_['play_time'] = $mins; //出场时间
				$data_['get_score'] = $score; //得分
				$data_['three_point'] = $tpm; //三分
				$data_['backboard'] = $rebs; //篮板
				$data_['help_score'] = $assists; //助攻
				$data_['hinder_score'] = $steals; //抢断
				$data_['mistake_score'] = $turnovers; //失误
				$data_['cover_score'] = $blocks; //封盖
				$data_['score'] = $this->scorerule($data_)*10; //积分

				$data_['team_a_id'] = $match_data['team_a'];
				$data_['team_b_id'] = $match_data['team_b'];
				$data_['team_a_score'] = $match_data['score_a'];
				$data_['team_b_score'] = $match_data['score_b'];
				

				$PlayerMatchData->where($Map)->save($data_);
			}else{
				$data_['team_id'] = $players[$player_id]['team_id']; //球员的队伍id
				$data_['player_id'] = $player_id; //球员的id
				$data_['match_id'] = $match_id;//比赛的id
				$data_['season'] = 2016; //赛季
				$data_['match_time'] = $match_data['match_time']; //比赛时间
				$data_['is_join'] = $mins == 0 ? 2 : 1; //是否上场过
				$data_['play_time'] = $mins; //出场时间
				$data_['get_score'] = $score; //得分
				$data_['three_point'] = $tpm; //三分
				$data_['backboard'] = $rebs; //篮板
				$data_['help_score'] = $assists; //助攻
				$data_['hinder_score'] = $steals; //抢断
				$data_['mistake_score'] = $turnovers; //失误
				$data_['cover_score'] = $blocks; //封盖
				$data_['score'] = $this->scorerule($data_)*10; //积分

				$data_['team_a_id'] = $match_data['team_a'];
				$data_['team_b_id'] = $match_data['team_b'];
				$data_['team_a_score'] = $match_data['score_a'];
				$data_['team_b_score'] = $match_data['score_b'];

				$PlayerMatchData->add($data_); //添加数据
			}
		}
		echo '更新完成';
	}
	//统计积分
    protected function scorerule($player_match_data){
        $score_sum = 0;
        $socre_rule = array('get_score' => 1,'backboard' => 1.2,'help_score' => 1.5,'hinder_score' => 2,'cover_score' => 2,'mistake_score' => -1,'three_point' => 0.5); //积分规则配置

        foreach ($player_match_data as $key => $value) {
            $score_sum += $socre_rule[$key] * $value;
        }

        return $score_sum;
    }

    //获取所有的选手
    public function getallplayer(){
    	$data = S('allplayer_data');
    	if(!$data){
			$data = M('MatchPlayer')->getField('id,name,team_id');
			S('allplayer_data',$data,3600);
    	}
    	return $data;
    	
    }

}