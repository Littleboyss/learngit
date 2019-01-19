<?php
/**
 * @author wangh 2017.2.20
 */
class PlayerAction extends AdminAction{
	//选手的国籍
	protected function getplayercountry(){
		$playercountry = $this->getcache('playercountry_data');
		$this->assign('playercountry',$playercountry);

	}
	public function getteamjson(){ // 返回json 提供前端做项目塞选
		$Map = $this->_post();
		$team = M('MatchTeam')->field('id,name')->where($Map)->order('name asc')->select();
		echo json_encode($team);die;
	}
	public function getplayerjson(){ // 返回json 提供前端做项目塞选
		$Map = $this->_post();
		if ($Map['project_id'] == 4) {
			$player = M('MatchPlayer');
		}else{
			$player = M('MatchPlayerWcg');
		}
		unset($Map['project_id']);
		$player_data = $player->field('id,name')->where($Map)->order('name asc')->select();
		echo json_encode($player_data);die;
	}
	//列表
	public function index(){
		$MatchPlayer = M('MatchPlayer');
		import('ORG.Util.Page');
		$Map = $this->_post();
		if($Map['team_id'] == 0){
			unset($Map['team_id']);
		}
		if($Map['name'] == ''){
			unset($Map['name']);
		}else{
			$Map['name'] = array('like',"%".$Map['name']."%");
		}
		if($Map['project_id'] == 0){
			unset($Map['project_id']);
		}
		$count = $MatchPlayer->where($Map)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $MatchPlayer->where($Map)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("show", $show);
		$this->assign ('data', $data );
		$this->getproject(true);
		$this->getteam();
		$this->display();	
	}
	//添加
	public function add(){
		$MatchPlayer = M('MatchPlayer');
		$data = $MatchPlayer->create();
		if ($data) {
			$data['add_time'] = time();
			// $data['position'] = implode(',', $data['position']);
			$result = $MatchPlayer->add($data);
			if($result){
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('index'));
			}
		} else {
			$this->getteam();
			$this->getplayercountry();
			$this->getproject();
			$this->display();
		}
	}
	//修改
	public function edit(){
		$MatchPlayer = M('MatchPlayer');
		if ($MatchPlayer->create()) {
			$data = $MatchPlayer->create();
			// $data['position'] = implode(',', $data['position']);
			$result = $MatchPlayer->save($data);
			if($result){
				$this->success('成功',U('index'));
			}else{
				$this->error('失败',U('index'));
			}
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $MatchPlayer->where($Map)->find();
			// $data['position'] = explode(',',$data['position']);
			// print_r($data);die;
			$this->getteam();
			$this->getplayercountry();
			$this->getproject();
			$this->assign('data',$data);
			$this->display();
		}
	}
	//删除,最后做
	public function del(){
		die;
	}

	//删除,最后做
	public function data(){
		$this->display();
	}
	//显示数据
	public function show_datas(){
		$data['play_id'] = $_POST['play_id'];

		$f = $_POST['f'];
		$f = str_replace("上一次工资",'$datas[\'sum\'][$key-1]',$f);
		$f = str_replace("单场得分",'$datas[\'score\'][$key]',$f);
		$f = str_replace("近十场平均分",'$datas[\'ten_time\'][$key]',$f);
		$f = str_replace("赛季平均分",'$datas[\'average\'][$key]',$f);
		if($data['play_id']){
			$match_data = M('MatchAllData')->where($data)->order('')->select();
			$player_id = M('MatchPlayer')->field('id')->where('only_id = '.$data['play_id'])->find();
			$match_list = M('PlayerMatchData')->field('match_time,score')->where('match_time > 1483200000 and player_id = '.$player_id['id'])->order(' match_time asc')->select();
			$time = array_column($match_list,'match_time');
			$score = array_column($match_list,'score');
			foreach ($time as $key => $value) {
				$month = (int)date("m", $value); 
				$day = (int)date("d", $value); 
				$match_list[$key]['date'] = $month.'.'.$day;
			}
			foreach ($match_list as $k => $v) {
				foreach ($match_data as $key => $value) {
					if ($value['match_time'] == $v['date'] && strlen($value['match_time']) == strlen($v['date'] )) {
						$list['ten_time'][] = $value['ten_time']; 
						$list['average'][] = $value['average']; 
						$list['salary'][] = $value['salary']; 
						$list['match_time'][] = $value['match_time']; 
						$list['score'][] = $v['score']; 
					}
				}
			}
			foreach ($list['average'] as $key => $value) {
				$datas['average'][$key] = (int)$value/10;
				$datas['score'][$key] = (int)$list['score'][$key]/10;
				$datas['salary'][$key] = (int)$list['salary'][$key];
				$datas['match_time'][$key] = $list['match_time'][$key];
				$datas['ten_time'][$key] = (int)$list['ten_time'][$key]/10;
			}
			$length = count($datas['average']);
			$datas['sum'][0] = $datas['salary'][0];
			$f = 'ceil('.$f.')';
			for ($key=1; $key < $length ; $key++) { 
				$datas['sum'][$key] = eval("return $f;");				
			}
			
			echo json_encode($datas);die;
		}else{
			$play_id = M('MatchPlayer')->field('only_id')->order('only_id asc')->select();
			echo json_encode($play_id);die;
		}
	}
	// 二维数组去重
	protected function array_unique_fb($array2D){ 
		foreach ($array2D as $v){
			$v=join(',',$v);//降维,也可以用implode,将一维数组转换为用逗号连接的字符串
			$temp[]=$v;
		}
		$temp=array_unique($temp);//去掉重复的字符串,也就是重复的一维数组
		foreach ($temp as $k => $v){
		    $temp[$k]=explode(',',$v);//再将拆开的数组重新组装
		}
		return $temp;
	}
	// 添加dota2初始数据
	public function add_dota2_player_data(){
		$player = M('MatchPlayerWcg');
		$player_match = M('PlayerMatchDataDota2');
		$player_data = $player->where('project_id = 6')->select();
		foreach ($player_data as $key => $value) {
			$player_id = $value['id'];// 用户id
			if ($value['position'] == 6) {
				$match_data = $player_match->query('SELECT AVG(scores) as average,concat(concat(sum(SUBSTRING(score,1,1)),\'W\') ,\'-\' , concat(sum(SUBSTRING(score,3)),\'L\'))as result FROM `fa_player_match_data_dota2` WHERE  player_id =  '.$player_id)[0];
				if ($match_data['average'] < 160) {
					$match_data['salary'] = 10;
				}else if($match_data['average'] > 300){
					$match_data['salary'] = 20;
				}else{
					$match_data['salary'] = round(($match_data['average']-160)/14+10);
				}
				$match_data['result'] = '0W-0L';
				$player->where('id ='.$player_id)->setField($match_data);
			}else{
				$match_data = $player_match->field('ROUND(AVG(scores)) as average ,ROUND(AVG((`kill`+assists)*10/death)) as KDA')->where('player_id ='.$player_id)->find();
				if ($match_data['average'] < 500) {
					$match_data['salary'] = 10;
				}else if($match_data['average'] > 1600){
					$match_data['salary'] = 50;
				}else{
					$match_data['salary'] = round(($match_data['average']-500)/27.5+10);
				}
				$match_data['result'] = '0W-0L';
				$player->where('id ='.$player_id)->setField($match_data);
			}
		}
	}
    public function champion_player_data(){
    	$player = M('MatchPlayerWcg');
		$player_match = M('PlayerMatchDataDota2');
		$player_data = $player->where('project_id = 6')->select();
		$socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2);
		foreach ($player_data as $key => $value) {
			if ($value['position'] == 6) {
				continue;
			}
			$player_id = $value['id'];// 用户id
			$match_data = $player_match->field('kill,death,assists,jungle,ten_kill ,score')->where('player_id ='.$player_id)->select();
			foreach ($match_data as $k1 => $v1) {
				foreach ($v1 as $k3 => $v3) {
		            $scores += $socre_rule[$k3] * $v3;
		        }
				$num = explode(':',$v1['score']); 
				$nums = $num[0]+$num[1];
				$match_data[$k1]['count'] = $nums;
				$players[$player_id][] = round($scores*10/$nums);
				unset($scores);
			}
	    	$player_ave[$player_id] = round(array_sum($players[$player_id])/count($players[$player_id]));
		}
		foreach ($player_ave as $k2 => $v2) {
			if ($v2<100) {
				$data['0~10'] ++;
			}elseif($v2<150){
				$data['10~15'] ++;
			}elseif($v2<200){
				$data['15~20'] ++;
			}elseif($v2<250){
				$data['20~25'] ++;
			}elseif($v2<300){
				$data['25~30'] ++;
			}elseif($v2<350){
				$data['30~35'] ++;
			}elseif($v2<400){
				$data['35~40'] ++;
			}elseif($v2<450){
				$data['40~45'] ++;
			}elseif($v2<500){
				$data['45~50'] ++;
			}elseif($v2<550){
				$data['50~55'] ++;
			}elseif($v2<600){
				$data['55~60'] ++;
			}elseif($v2<650){
				$data['60~65'] ++;
			}elseif($v2<700){
				$data['65~70'] ++;
			}elseif($v2<750){
				$data['70~75'] ++;
			}elseif($v2<800){
				$data['75~80'] ++;
			}
		}
		foreach ($data as$keys => $values) {
			$data[$keys] = round($values/count($player_ave)*1000);
		}
			echo'<pre>';var_dump($player_ave);die;
			$ke = 0;
		foreach ($data as $key => $value) {
			$last[$ke][] = $key;
			$last[$ke][] = $value/10;
			$ke ++;
		}
			var_dump(json_encode($last));die;
   	} 
   	public function news(){
   		$PlayerNews = M('PlayerNews');
		import('ORG.Util.Page');
		$count = $PlayerNews->count();
		$page = new Page($count, 20);
		$show = $page->show();
		$data = $PlayerNews->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		foreach ($data as $key => $value) {
			if ($value['project_id'] == 4) {
				$player_mode = M('MatchPlayer');
			}else{
				$player_mode = M('MatchPlayerWcg');
			}
			$res = $player_mode->where('id = '.$value['player_id'])->find();
			$data[$key]['name'] = $res['name'];
		}
		$this->assign("show", $show);
		$this->getproject(true);
		$this->assign ('data', $data );
   		$this->display();
   	}
   	//新闻添加
	public function news_add(){
		$PlayerNews = M ('PlayerNews');
		$data = $PlayerNews->create();
		if ($data) {
			$data['addtime'] = time();
			$result = $PlayerNews->add($data);
			if($result){
				$this->success('成功',U('news'));
			}else{
				$this->error('失败',U('news'));
			}
		} else {
			$this->getproject(true);
			$this->display();
		}
	}
	//新闻修改
	public function news_edit(){
		$PlayerNews = M ('PlayerNews');
		$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
		$data = $PlayerNews->create();
		if ($data) {
			$data['addtime'] = time();
			$result = $PlayerNews->save($data);
			if($result){
				$this->success('成功',U('news'));
			}else{
				$this->error('失败',U('news'));
			}
		} else {
			if ($id <= 0) {
				$this->error ('错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $PlayerNews->where($Map)->find();
			if ($data['project_id'] == 4) {
				$player_mode = M('MatchPlayer');
			}else{
				$player_mode = M('MatchPlayerWcg');
			}
			$res = $player_mode->where('id ='.$data['player_id'])->find();
			$data['team_id'] = $res['team_id'];
			$where['project_id'] = $data['project_id'];
			$team = M('MatchTeam')->field('id,name')->where($where)->order('name asc')->select();
			$player = $player_mode->field('id,name')->where('team_id = '.$res['team_id'])->order('name asc')->select();
			$this->assign('data',$data);
			$this->assign('team',$team);
			$this->assign('player',$player);
			$this->getproject(true);
			$this->display();
		}
	}
	//新闻修改
	public function news_del(){
		$PlayerNews = M ('PlayerNews');
		$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
		if ($id <= 0) {
			$this->error ('错误',U('index'));
		}
		$Map['id'] = $id;
		$data = $PlayerNews->where($Map)->delete();
		if($data){
			$this->success('成功',U('news'));
		}else{
			$this->error('失败',U('news'));
		}
	}
}