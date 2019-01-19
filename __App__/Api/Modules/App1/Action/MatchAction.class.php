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
		$cache_name = 'team_data_all';
		//获取缓存的信息
		$project_data = $this->getdata('project_name_all',86400);
		if($project_data == false){
			$this->returnMsg(1,'system');
		}
		$data = $this->cache('get',$cache_name);
		if(!$data){
			$data = M('MatchTeam')->field('id,project_id,name,e_name,short_name,location,union,img,home_court')->select();
			// echo M('MatchTeam')->getLastSql();
			foreach ($data as $key => $value) {

				$data[$key]['project_name'] = $project_data[$value['project_id']]['name'];
				$data[$key]['location_name'] = C('TEAM_LOCATION')[$value['location']] ? C('TEAM_LOCATION')[$value['location']] : '';
				$data[$key]['union_name'] = C('TEAM_UNION')[$value['union']] ? C('TEAM_UNION')[$value['union']] : '';

			}
			$this->cache('set',$cache_name,$data,3600*24);
		}
		$this->returnMsg(0,'room',$data);
	}
}