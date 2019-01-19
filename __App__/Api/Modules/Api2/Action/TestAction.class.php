<?php

class TestAction extends CommonAction{
	private $match_id = 7;//定义赛事类型id
	//获取阵容的比赛数据
	public function get_lineup_score(){
		$id = I('id');
		$data = M('ChampionLineup')->where(array('id' => $id))->find();
		if(!$id){
			exit('没有查询到数据');
		}

		$lineup_info = unserialize($data['lineup']);
		$all_players = $this->project_players(6);
		$all_teams = $this->all_teams();
		$MatchList = M('MatchList');
		$PlayerMatchDataDota2 = M('PlayerMatchDataDota2');
		$_data = array();
		foreach ($lineup_info as $key => $value) {
			//获取这个球员所有的比赛
			$team_id =  $all_players[$value]['team_id'];

			$match_list_info = $MatchList->where('match_name_id='.$this->match_id.' and (team_a='.$team_id.' or team_b='.$team_id.')')->select();
			foreach ($match_list_info as $k => $v) {
				$_data[] = $PlayerMatchDataDota2->where(array('match_id' => $v['id'],'player_id' => $value))->find();
			}
		}
		echo '<table border="1" cellpadding="2" cellspacing="0" style="margin:0 auto;width:100%;text-align:center;border:1px solid #666;">';
			echo '<td>选手名称</td>';
			echo '<td>击杀</td>';
			echo '<td>死亡次数</td>';
			echo '<td>助攻</td>';
			echo '<td>打野数</td>';
			echo '<td>十次以上击杀或助攻</td>';
			echo '<td>比分</td>';
			echo '<td>上场场数</td>';
			echo '<td>对手战队</td>';
			echo '<td>获得积分值</td>';
			echo '<td>推塔数</td>';
			echo '<td>小龙击杀次数</td>';
			echo '<td>大龙击杀次数</td>';
			echo '<td>是否是一血</td>';
			echo '<td>是否赢得比赛</td>';
			echo '<td>是否上场</td>';
			echo '<td>是否30分钟胜利</td>';
			echo '<td>剩余场次</td>';
			echo '<td>时间</td>';


		foreach ($_data as $ky => $ve) {
			echo '<tr>';

			echo '<td>'.$all_players[$ve['player_id']]['name'].'</td>';
			echo '<td>'.$ve['kill'].'</td>';
			echo '<td>'.$ve['death'].'</td>';
			echo '<td>'.$ve['assists'].'</td>';
			echo '<td>'.$ve['jungle'].'</td>';
			echo '<td>'.$ve['ten_kill'].'</td>';
			echo '<td>'.$ve['score'].'</td>';
			echo '<td>'.$ve['times'].'</td>';
			echo '<td>'.$ve['opp'].'</td>';
			echo '<td>'.$ve['scores'].'</td>';
			echo '<td>'.$ve['tower'].'</td>';
			echo '<td>'.$ve['dragons'].'</td>';
			echo '<td>'.$ve['barons'].'</td>';
			echo '<td>'.$ve['first_blood'].'</td>';
			echo '<td>'.$ve['is_win'].'</td>';
			echo '<td>'.$ve['is_join'].'</td>';
			echo '<td>'.$ve['is_fast'].'</td>';
			echo '<td>'.$ve['remain'].'</td>';
			echo '<td>'.$ve['date'].'</td>';


			echo '</tr>';
		}
		echo '</table>';
	}

}