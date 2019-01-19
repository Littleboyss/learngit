<?php
/**
 * @author wangh 2017.2.17
 */
class MatchAction extends CommonAction{
	/**
	* 获取所有的队伍
	* @param $project_id 项目的id ,不传获取所有的,球员
	*/
	public function team(){
		$cache_name = 'team_data';
		$project_id = $this->_data['project_id'];
		if(is_numeric($project_id)){
			$Map['project_id'] = $project_id;
		}
		//获取缓存的信息
		$project_data = $this->getdata('project_name_all',86400);
		if($project_data == false){
			$this->returnMsg(1,'system');
		}
		//添加缓存
		$data = $this->cache('get',$cache_name);
		if(!$data){
			$data = M('MatchTeam')->where($Map)->getField('id,name,e_name,short_name,location,union,img,home_court');
			foreach ($data as $key => $value) {
				$data[$key]['project_name'] = $project_data[$value['project_id']]['name'];
				$data[$key]['location_name'] = C('TEAM_LOCATION')[$value['location']];
				$data[$key]['union_name'] = C('TEAM_UNION')[$value['union']];
			}
			$this->cache('set',$cache_name,$data,3600*24);
		}
		$this->returnMsg(0,'room',$data);
	}
	/**
	* 获取所有的球员和选手列表
	* @param $project_id 项目的id 
	*/
	public function player(){
		$project_id = $this->_data['project_id'];

		$project_data = $this->getdata('project_name_all',86400);
		// print_r($project_data);die;
		if($project_data == false){
			$this->returnMsg(1,'system');
		}
		// 项目id不能为空
		if (empty($project_id)) {

			$cache_name_all = 'all_players';

			$p_a = $this->cache('get',$cache_name_all);
			if(!$p_a){
				//项目id为空的时候获取所有的选手
				$f1 = M('MatchPlayerWcg')->where($Map)->select();
				$f2 = M('MatchPlayer')->where($Map)->select();

				$f3 = array_merge($f1,$f2);

				foreach ($f3 as $key => $value) {
					$f3[$key]['project_name'] = $project_data[$value['project_id']]['name']; //获取项目名称
					//对球员的平均分
					$f3[$key]['average'] = $value['average']/10;
					if($project_id == 4){
						// 平均上场时间
						$f3[$key]['img'] = 'http://api.aifamu.com/img/playerimg/'.$value['id'].'.png';
						$f3[$key]['play_time'] = $value['play_time']/10;
					}elseif($project_id == 5 || $project_id == 6){
						// 10场平均kda
						$f3[$key]['KDA'] = $value['KDA']/10;
					}
					unset($f3[$key]['only_id']);
				}

				$p_a = array();

				foreach ($f3 as $k => $v) {
					$p_a[$v['position']][] = $v;
				}


				$this->cache('set',$cache_name_all,$p_a,3600*24);
			}
			$this->returnMsg(0,'room',$p_a);
		}
		//获取缓存的信息
		

		//添加缓存
		$cache_name = 'player_data'.$project_id;
		$data = $this->cache('get',$cache_name);
		if(!$data){
			if ($project_id == 5 || $project_id == 6) {
				// lol 或 dota2
				$MatchPlayer = M('MatchPlayerWcg') ;
			}elseif($project_id == 4){
				// NBA
				$MatchPlayer = M('MatchPlayer');
			}else{
				$this->returnMsg(9,'room'); // 
			}
			$data = $MatchPlayer->where($Map)->select();
			foreach ($data as $key => $value) {
				$data[$key]['project_name'] = $project_data[$value['project_id']]['name']; //获取项目名称
				//对球员的平均分
				$data[$key]['average'] = $value['average']/10;
				if($project_id == 4){
					// 平均上场时间
					$data[$key]['img'] = 'http://api.aifamu.com/img/playerimg/'.$value['id'].'.png';
					$data[$key]['play_time'] = $value['play_time']/10;
				}elseif($project_id == 5 || $project_id == 6){
					// 10场平均kda
					$data[$key]['KDA'] = $value['KDA']/10;
				}
				unset($data[$key]['only_id']);
			}
			$this->cache('set',$cache_name,$data,3600*24);
		}
		$this->returnMsg(0,'room',$data);
	}
	/**
	* 获取所有的球员和选手列表
	* @param $project_id 项目的id 
	*/
	public function player_all(){
		$project_id = $this->_data['project_id'];

		$project_data = $this->getdata('project_name_all',86400);
		// print_r($project_data);die;
		if($project_data == false){
			$this->returnMsg(1,'system');
		}
		// 项目id不能为空
		if (empty($project_id)) {

			$cache_name_all = 'all_players_a';
			$p_a = $this->cache('get',$cache_name_all);
			//项目id为空的时候获取所有的选手
			$f1 = M('MatchPlayerWcg')->where($Map)->select();
			$f2 = M('MatchPlayer')->where($Map)->select();
			$f3 = array_merge($f1,$f2);
			foreach ($f3 as $key => $value) {
				$f3[$key]['project_name'] = $project_data[$value['project_id']]['name']; //获取项目名称
				//对球员的平均分
				$f3[$key]['average'] = $value['average']/10;
				if($project_id == 4){
					// 平均上场时间
					$f3[$key]['img'] = 'http://api.aifamu.com/img/playerimg/'.$value['id'].'.png';
					$f3[$key]['play_time'] = $value['play_time']/10;
				}elseif($project_id == 5 || $project_id == 6){
					// 10场平均kda
					$f3[$key]['KDA'] = $value['KDA']/10;
				}
				unset($f3[$key]['only_id']);
			}
			$this->cache('set',$cache_name_all,$f3,3600*24);
		}
		$this->returnMsg(0,'room',$f3);
		
	}
}