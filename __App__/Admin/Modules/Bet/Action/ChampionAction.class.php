<?php
/**
 * @author wangh 2017.2.20
 */

class ChampionAction extends AdminAction{

	//获取比赛的赛区和
	protected function match_loca_uoion(){
		$this->assign('location',C('TEAM_LOCATION'));
		$this->assign('union',C('TEAM_UNION'));
	}
	public function getteamjson(){ // 返回json 提供前端做项目塞选
		$Map = $this->_post();
		$team = M('Champion')->field('id,name')->where($Map)->order('name asc')->select();
		echo json_encode($team);die;
	}
	protected function getmatchtype(){
		$match_type = $this->getcache('match_type_data');
		// print_r($match_type);
		$this->assign('match_type',$match_type);
	}
	//列表
	public function index(){
		$Champion = M('Champion');
		import('ORG.Util.Page');
		$Map = $_REQUEST;
		if($Map['name'] == ''){
			unset($Map['name']);
		}
		if($Map['id'] == ''){
			unset($Map['id']);
		}
		if($Map['project_id'] == ''){
			unset($Map['project_id']);
		}
		$count = $Champion->where($Map)->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $Champion->where($Map)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$project_id = $_REQUEST['project_id'];
		$team = $Champion->order('name asc')->select();
		$this->assign("show", $show);
		$this->assign("team", $team);
		$this->assign("id", $Map['id']);
		$this->assign ('data', $data );
		$this->assign ('project_id',$project_id );
		$this->match_loca_uoion();
		$this->getproject(true);

		$this->display();	
	}
	//添加
	public function add(){
		$Champion = M('Champion');
		if (IS_POST) {
			$data = $Champion->create();
			if ($data) {
				$data['add_time'] = time();
				$data['match_start_time'] = strtotime($data['match_start_time']);
				$data['match_end_time'] = strtotime($data['match_end_time']);
				$data['bet_end_time'] = strtotime($data['bet_end_time']);
				$result = $Champion->add($data);
				if($result){
					$this->success('成功',U('index'));
				}else{
					$this->error('失败',U('index'));
				}
			}
		} else {
			$author = $_SESSION['admin']['nickname'];
            $this->assign('author',$author);
			$this->getmatchtype();
			$this->getproject();
			$this->display();
		}
	}
	//修改
	public function edit(){
		$Champion = M('Champion');
		$data = $Champion->create();
		if ($data) {
			$data['modify_time'] = time();
			$data['match_start_time'] = strtotime($data['match_start_time']);
			$data['match_end_time'] = strtotime($data['match_end_time']);
			$data['bet_end_time'] = strtotime($data['bet_end_time']);
			$Champion->save( $data );
			$this->success ('成功',U('index'));
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $Champion->where($Map)->find();
			$author = $_SESSION['admin']['nickname'];
            $this->assign('author',$author);
			$this->getproject();
			$this->getmatchtype();
			$this->assign('data',$data);
			$this->display ();
		}
	}
	//删除,最后做
	public function del(){
		exit('暂时关闭');
	}

	public function endindex(){
		$ChampionEndTeam = M('ChampionEndTeam');
		import('ORG.Util.Page');
		$count = $ChampionEndTeam->count();
		$page = new Page($count, 10);
		$show = $page->show();
		$data = $ChampionEndTeam->limit($page->firstRow . ',' . $page->listRows)->select();

		$this->assign("show", $show);
		$this->assign('data',$data);
		$this->display();	

	}


	public function endadd(){
		$ChampionEndTeam = M('ChampionEndTeam');
		if (IS_POST) {
			$data = $ChampionEndTeam->create();
			if ($data) {
				$data['add_time'] = time();

				$result = $ChampionEndTeam->add($data);
				if($result){
					$this->success('成功',U('index'));
				}else{
					$this->error('失败',U('index'));
				}
			}
		} else {
			$this->display();
		}	
	}

	public function endedit(){
		$ChampionEndTeam = M('ChampionEndTeam');
		$data = $ChampionEndTeam->create();
		if ($data) {
			$ChampionEndTeam->save( $data );
			$this->success ('成功',U('index'));
		} else {
			$id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
			if ($id <= 0) {
				$this->error ('参数错误',U('index'));
			}
			$Map['id'] = $id;
			$data = $ChampionEndTeam->where($Map)->find();
			$this->assign('data',$data);
			$this->display ();
		}
	}

	//冠军猜结算
	public function guess_res(){

		$champion_id = I('id');
		$champion_data = M('Champion')->where(array('id' =>$champion_id))->find();
		if(time() < $champion_data['match_end_time']){
			$this->returnMsg(0,'比赛还未结算,不允许结算');
		}
		$data = M('UserChampion')->where(array('champion_id' => $champion_id,'status' => 1))->limit(100)->select();
		if($data){
			$success = 0;
			$error = 0;
			foreach ($data as $key => $value) {
				$reward_info = $this->act_reward($value['ranking']);//奖品info
				if($reward_info['reward_goods_id']){
					$_data['uid'] = $value['uid'];
					$_data['type'] = 4; //商品
					$_data['goods_id'] = $reward_info['reward_goods_id']; //商品id
					$_data['status'] = 0;
					$_data['rank'] = $value['ranking'];
					$_data['price_type'] = 2;
					$_data['add_time'] =time();
					$res = M('UserPriceList')->add($_data);
					if($res){
						M('UserChampion')->where('id='.$value['id'])->setField('status',2);
						$success += 1; 
					}else{
						$error += 1; 
					}
				}else{
					M('UserChampion')->where('id='.$value['id'])->setField('status',2);
					$success += 1; 
				}
			}
			$this->returnMsg(1,'成功结算'.$success.'个,失败'.$error.'个');
		}else{
			$this->returnMsg(0,'结算完成');
		}
	}



    //冠军猜活动奖品,前后台请保持一致
    //$ranking 排名,所获得的奖品
    public function act_reward($ranking){
            if($ranking == 1){

                $reward_goods_id = 152; //奖品商品id , 存在此字段的时候奖品到数据库中取

            }elseif($ranking >= 2 && $ranking <= 3){

                $reward_goods_id = 153; 

            }elseif($ranking >= 4 && $ranking <= 6){

                $reward_goods_id = 80; 

            }elseif($ranking >= 7 && $ranking <= 11){

                $reward_goods_id = 156; 

            }elseif($ranking >= 12 && $ranking <= 31){

                $reward_goods_id = 162; 

            }elseif($ranking >= 32 && $ranking <= 36){

                $reward_goods_id = 157; 

            }elseif($ranking >= 37 && $ranking <= 41){

                $reward_goods_id = 7; 

            }elseif($ranking >= 42 && $ranking <= 51){

                $reward_goods_id = 155; 

            }elseif($ranking >= 52 && $ranking <= 61){

                $reward_goods_id = 143; 

            }elseif($ranking >= 62 && $ranking <= 66){

                $reward_goods_id = 145;

            }elseif($ranking >= 67 && $ranking <= 126){

                $reward_goods_id = 158; 

            }elseif($ranking >= 127 && $ranking <= 136){

                $reward_goods_id = 146; 

            }elseif($ranking >= 137 && $ranking <= 186){

                $reward_goods_id = 160; 

            }elseif($ranking >= 187 && $ranking <= 216){

                $reward_goods_id = 161; 

            }else{

                $reward_goods_id = 0; 

            }
            // $reward_goods_id
            $data = M('ShopGoods')->where(array('id' =>$reward_goods_id))->find();
            if($data){
                $reward_name = $data['name'];
                $reward_img = $data['image'] != '' ? $data['image'] : $data['avatar_img'];
            }else{
                $reward_name = '';
                $reward_img = '';
            }
            return array('reward_name' => $reward_name,'reward_img' => $reward_img,'reward_goods_id' => $reward_goods_id);
    }
}