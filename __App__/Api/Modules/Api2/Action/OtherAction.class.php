<?php
/**
 * 公共常用接口
 * @author wangh 2017.2.17
 */
class OtherAction extends CommonAction{
	// 获取一天前最高积分的阵容
	public function get_top_score(){
		$map['project_id'] = $this->_data['project_id'];
		//$lineups = $this->cache('get', 'lineups'. $map['project_id']);
		//if (!$lineups) {
			if ($map['project_id'] <= 0) {
				$this->returnMsg(2,'customer');// 数据异常
			}
			if ($map['project_id'] == 4) {
	        	$PlayerMatchData = M('PlayerMatchData');
	        }elseif($map['project_id'] == 5){
	        	$PlayerMatchData = M('PlayerMatchDataLol');
	        }elseif($map['project_id'] == 6){
	        	$PlayerMatchData = M('PlayerMatchDataDota2');
	        }
			$map['end_time'] = strtotime(date('Y-m-d'.'00:00:00',time()-3600*24));
			$room_id = M('MatchRoom')->field('id')->where($map)->select();
			if ($room_id) {
				$where = '(1 != 1 ';
				foreach ($room_id as $keys => $values) {
					$where .= ' or room_id =' . "'" . $values['id'] . "'";
				}
				$where .= ' ) and ( match_status = 3 )';
				$data = M('UserGuessRecord')->where($where)->order('lineup_score desc')->find();// 获取对应房间
				if (!$data) {
					$this->returnMsg(1,'lineup');
				}
				$linup_data = M('lineup')->where('id = '.$data['lineup_id'])->find();
				$linup = unserialize($linup_data['lineup']);// 还原阵容
				$match_id = M('MatchRoomInfo')->where('room_id ='.$data['room_id'])->find();// 获取赛事id 
				$team = explode(',',$match_id['match_team']);
				$where2 = '1 != 1 ';
				foreach ($team as $keys => $values) {
					$where2 .= ' or match_id =' . "'" . $values . "'";
				}
				$player_data = $PlayerMatchData->where($where2)->select();// 获取场次id 
				//var_dump($where2);exit;
				//var_dump($where2);exit;
				foreach ($player_data as $key => $value) {
					foreach ($linup as $k => $v) {
						if ($v == $value['player_id']) {
							$lineups['match_data'][$k] =  $value;
							if ($map['project_id'] == 5) {
								$lineups['match_data'][$k]['last_score'] = $this->scorerule_lol($k,$value);
							}elseif($map['project_id'] == 6){
								$lineups['match_data'][$k]['last_score'] = $this->scorerule_dota2($k,$value);
							}
							if($value['death']==0){
								$value['death']==1;
							}
							$lineups['match_data'][$k]['KDA'] = number_format(($value['kill']+$value['assists'])/$value['death'],1);
							$lineups['match_data'][$k]['player_data'] = M('MatchPlayerWcg')->field('name,img,salary,position,team_id')->where('id ='.$v)->find();
							$team_data = M('MatchTeam')->field('name,img')->where('id = '.$lineups['match_data'][$k]['player_data']['team_id'])->find();
							$lineups['match_data'][$k]['player_data']['team_name'] = $team_data['name'];
							$lineups['match_data'][$k]['player_data']['team_img'] = $team_data['img'];
						}
					}
				}
				$uid = $data['uid'];
				$lineups['campion'] = M('MatchRoom')->field('prize_type')->where('id = '.$data['room_id'])->find();
				$lineups['campion']['name'] = M('UserUser')->where('id ='.$uid)->getfield('username');
				$lineups['campion']['avatar_img'] = 'http://api.aifamu.com/avator/icon.php?id='.$uid;
				$lineups['campion']['nums'] = $data['is_reward'];
				$this->cache('set','lineups'. $map['project_id'],$lineups,3600*24);
			}else{
				$this->returnMsg(1,'lineup');
			}
		//}
			$this->returnMsg(0,'room',$lineups);

	}
	protected function scorerule_dota2($position,$player_match_data){
        if ($position == 7) {
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
        }else{
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
        }
        foreach ($player_match_data as $key => $value) {
            $score_sum += $socre_rule[$key] * $value;
        }

        return number_format($score_sum,1);
    }
    protected function scorerule_lol($position,$player_match_data){
        if ($position == 7) {
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
        }else{
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
        }
        foreach ($player_match_data as $key => $value) {
            if ($key == 'remain') {
                if ($player_match_data['is_win'] == 0) {
                    continue;
                }
            }else{
                $score_sum += $socre_rule[$key] * $value;
            }
        }

        return number_format($score_sum,1);
    }
}