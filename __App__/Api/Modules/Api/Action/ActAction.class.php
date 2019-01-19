<?php

// https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxcb820196a31b4bf5&redirect_uri=http%3A%2F%2Fapi.aifamu.com%2Findex.php%3Fg%3Dapi%26m%3Dpublic%26a%3Dwxlogin&response_type=code&scope=snsapi_userinfo&state=#wechat_redirect
//活动相关
class ActAction extends CommonAction{
    private static $prize_date = '2017年9月12号'; //奖品有效期
    private $champion_id = 1; //此次冠军猜的id
	private static $f = './lineup_json_data.json';
	private $wx_redirect = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=#wechat_redirect';
	private static $wxappid = 'wxcb820196a31b4bf5';
	private static $wxappsecret = 'b779850be348a07e3e9816e1f1ca6e44';
	//创建微信授权登录的url
	public function create_url(){
		$uid = I('token'); //分享过来的uid
		$url = 'http://api.aifamu.com/index.php?g=api&m=act&a=get_user_info';
        $type = I('type');
        if($type){
            $url = $url.'&uid=reward';
        }
        // $url_code = 'http://act.aifamu.com/index.html?code=';
		if($uid){
			$url = $url.'&uid='.$uid;
		}
		redirect(sprintf($this->wx_redirect,self::$wxappid,urlencode($url)));
	}


	public function get_user_info(){
        $code = I('code');
        $uid = I('uid');
        if($uid == 'reward'){
            $url_code = 'http://ti7.aifamu.com/reward/www/index.html?code=';
        }else{
            $url_code = 'http://ti7.aifamu.com/#/home/?code=';
        }
		// $url_code = 'http://act.aifamu.com/?code=/#/home';

        if(!$code){
        	$this->returnMsg(1,'wxlogin');
        }
        //通过code，换取access_token
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.self::$wxappid.'&secret='.self::$wxappsecret.'&code='.$code.'&grant_type=authorization_code';
        $result = $this->c_url($url);
        // echo $result;
        $oauthMessage = json_decode($result,true);
        if($oauthMessage['access_token']){
            $access_token = $oauthMessage['access_token'];
            $openid = $oauthMessage['openid'];
            $unionid = $oauthMessage['unionid'];
        }else{
        	$this->returnMsg(1,'wxlogin',$oauthMessage);
        }
        $UserOpenInfo = M('UserOpenInfo');
        $connect_data = $UserOpenInfo->where(array('wx_openid' => $unionid))->find();
        $UserUser = M('UserUser');
        if($connect_data && $connect_data['user_id'] != 0){

			$user_data = $UserUser->where(array('id'=>$connect_data['user_id']))->find();
            //生成唯一token
            $user_data['token'] = md5($connect_data['user_id'] . time() . rand(1,9999));
            $UserUser->where(array('id' => $user_data['id']))->setField('token',$user_data['token']);
			if($user_data){
				M('UserMoreInfo')->add(array('uid' => $connect_data['user_id'])); //添加其他信息
				// redirect($url_code.$this->en_de_crypt('en',$user_data['token']));
                if($uid){
                    redirect($url_code.$user_data['token'].'&token='.$uid);
                }
                //成功操作时.将用户的token保存到缓存中,有效期2小时
                // S($connect_data['user_id'],array($openid,$access_token),7200);

                redirect($url_code.$user_data['token']);
			}else{
				$this->returnMsg(3,'login'); // 登录失败
			}
        }else{
        	//注册操作
			if(!$connect_data){//防止 重复添加openid到数据库

				$wx_user_data = $this->get_wx_userinfo($access_token,$openid);
				//获取到微信的用户信息
					
				// 注册用户
				$data['type'] = 'wx';
				$data['add_time'] = time();
		        $data['ip'] = get_client_ip(1);// 返回ipv4地址，int
		        // $data['username'] = $this->get_rand_username( $data['type']);
		        $data['username'] = $wx_user_data['nickname'];
		        $data['entrance_ticket'] = C('USER_MONEY')['entrance_ticket'];// 门票
				$data['diamond'] = C('USER_MONEY')['diamond'];// 砖石
				$data['gold'] = C('USER_MONEY')['gold'];// 木头
		        $id = $UserUser->add($data);
		        if ($id) {
                    $this->data_count(false,true);//新增用户统计
		        	$_user_more['uid'] = $id;
		        	// if($uid > 0 && is_numeric($uid)){
		        	// 	$_user_more['friend_uid'] = $uid;
		        	// }
		        	M('UserMoreInfo')->add($_user_more); //添加其他信息

		        	$this->url_upload($wx_user_data['headimgurl'],$id); //上传微信头像到我们服务器

		        	$token = md5(time().$id.mt_rand(1000,9999));// 登陆的token

		        	$UserUser->where('id = '.$id)->setField('token',$token);

		        	$error['user_id'] = $id ;

		        	M('UserErrorTry')->add($error);

					$this->set_connect_info($id,$unionid,'wx',$openid);

					$wx_user_data = $this->get_wx_userinfo($access_token,$openid);
					$this->url_upload($wx_user_data['headimgurl'],$id);
                    // redirect($url_code.$this->en_de_crypt('en',$token));
                    //成功操作时.将用户的token保存到缓存中,有效期2小时
                    // S($id,array($openid,$access_token),7200);


                    if($uid){
                        redirect($url_code.$token.'&token='.$uid);
                    } 
					redirect($url_code.$token);

	        	}
			}else{
				$this->returnMsg(1,'system');
			}
		}

        
    }
    private function set_connect_info($uid,$openid,$type,$pay_openid){
        if(!in_array($type, array('qq','wx','wb'))){
            return false;
        }
        $UserOpenInfo = M('UserOpenInfo');
        $data = $UserOpenInfo->where(array('user_id' => $uid))->find();
        if($data){
            $_data[$type.'_openid'] = $openid;
            $_data['wx_pay_openid'] = $pay_openid;
            $res = $UserOpenInfo->where(array('id' => $data['id']))->save($type.'_openid',$_data);
        }else{
            $_data['user_id'] = $uid;
            $_data[$type.'_openid'] = $openid;
            $_data['addtime'] = time();
            $_data['wx_pay_openid'] = $pay_openid;
            $res = $UserOpenInfo->add($_data);
        }
        if($res){
            return truen;
        }
        return false;
    }
    /**
     *  根据openid获取用户的基本信息
     *  unionid	只有将公众号绑定到微信开放平台帐号后，才会出现该字段。
     */
    private function get_wx_userinfo($access_token,$openid){
        
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $result = json_decode($this->c_url($url),true);
        return $result;
    }


    public function get_user_data(){
        $this->pv_count();
        $this->ip_count();
        usleep(500); //延迟0.5秒,防止投注之后无法获取到投注信息

    	$code = $this->_data['code']; //用户加密后的token
        // $token = $this->en_de_crypt('en',$code);
    	$token = $code;
    	// if(!$token){
    	// 	$this->returnMsg(1); //解密时不正确
    	// }

    	$data = M('Champion')->where(array('id' => $this->champion_id))->find();
    	//1.获取用户的信息
        $user_token = $this->_data['user_token'];
        if($user_token){
            $token = $user_token;
        }

    	$UserUser = M('UserUser');
    	$user_data = $UserUser->field('id,token,username')->where(array('token' => $token))->find();
    	if(!$user_data){
    		$this->returnMsg(1,'login');
    	}
        $UserMoreInfo = M('UserMoreInfo');
        $result_more_info = $UserMoreInfo->where(array('uid' => $user_data['id']))->find();
        if(!$result_more_info){
            $UserMoreInfo->add(array('uid' => $user_data['id']));
        }


        //获取我的微信openid
        $openid_data = M('UserOpenInfo')->where(array('user_id' => $user_data['id']))->find();
        $user_data['img'] = C('AVATAR_IMG').$user_data['id'];
        $user_data['openid'] = $openid_data['wx_openid'];
    	$UserChampion = M('UserChampion');
    	//2.检查用户的投注信息
    	$guess_info = $UserChampion->where(array('champion_id' => $this->champion_id,'uid' => $user_data['id']))->find();
    	if(!$guess_info){
    		$this->returnMsg(100,'act',array('user_data' => $user_data)); //用户没有投注过,需跳转到竞猜页面
    	}

    	$you_jiang_num = 216; //有奖的名次


    	//3.用户投注过,返回用户的信息,用户投注的阵容,用户的好友信息,分享信息
    		//(1)返回我阵容的历史积分情况信息
        $my_score_change = $this->get_lineup_change($guess_info['lineup_id']);
        $now_h = date('j');
        $now_h = $now_h - 4;


    		//(2)一个最高分,一个有奖分,一个我当前分
    	$max_score = $UserChampion->order('count_score desc')->find();//最高分
    	$door_score = $UserChampion->where(array('ranking' => $you_jiang_num))->find();//有奖分
    	$_data['max_score'] = $max_score['count_score'] ? $max_score['count_score']/100 : 0;
    	$_data['door_score'] = $door_score['count_score'] ? $door_score['count_score']/100 : 0;
    	$_data['my_score'] = $guess_info['count_score'] ? $guess_info['count_score']/100 : 0;
    	$_data['my_ranking'] = $guess_info['ranking']; //我当前名次
    	$_data['count_ranking'] = $data['guess_num'] + $data['virtual_guess_num']; //总人数

        $dx = $guess_info['lineup_score']/10;

        $_data['match_start_time'] = date('m-d',$data['match_start_time']); //比赛开始时间
        $_data['match_end_time'] = date('m-d',$data['match_end_time']); //比赛结束时间
        if($my_score_change['score'][$now_h] > $dx){
            $_data['rank_change'] = 'down';
        }elseif($my_score_change['score'][$now_h] == $dx){
            $_data['rank_change'] = 'none';
        }else{
            $_data['rank_change'] = 'up';
        }
        $_data['aaaa'] = $guess_info['lineup_score'];
    	$eliminate_data = $this->get_eliminate_player(); //获取所有的已经淘汰了的球员
    	//获取阵容的信息
    	$lineup_info = M('ChampionLineup')->where(array('id' => $guess_info['lineup_id']))->find();
    	$lineup_info_data = unserialize($lineup_info['lineup']);
    	$user_lineup_info = array();

        //选手的比赛天数
        $now_time = time();
        if($now_time >= $data['match_start_time'] && $now_time <= $data['match_end_time']){
            $play_days = floor(($now_time - $data['match_start_time'])/86400) + 1;//已经比赛的天数
        }elseif($now_time >= $data['match_end_time']){
            // $play_days = floor(($data['match_end_time'] - $data['match_start_time'])/86400) + 1;//已经比赛的天数
            $play_days = 9;
        }else{
            $play_days = 1;
        }
        // print_r($eliminate_data);die;
    	foreach ($lineup_info_data as $key => $value) {
    		$user_lineup_info[$value]['score'] = $this->champion_player_data($value)/10; //选手得分
            $user_lineup_info[$value]['eliminate'] = in_array($value,$eliminate_data['t_players']) ? 1 : 2; //1已经淘汰 2未淘汰
            if($user_lineup_info[$value]['eliminate'] == 1){ //已经淘汰了的选手定格在淘汰的天数
                $days = $data['match_end_time'] - strtotime($eliminate_data['players_info'][$value]);
                // echo $eliminate_data['players_info'][$value];die;
                $user_lineup_info[$value]['play_days'] = floor($days/86400) + 1;
            }else{
                $user_lineup_info[$value]['play_days'] = $play_days; //比赛的天数
            }
    		
    	}


    	//获取好友的信息
        $RedPacketRecord = M('RedPacketRecord');
        $friends = $RedPacketRecord->where(array('g_uid' => $user_data['id']))->limit(5)->select();
        // print_r($friends);
        $friend_user_data = array();
        // foreach ($friends as $key => $value) {
        //     $res = $UserChampion->where(array('uid' => $value['g_uid']))->find();
        //     // print_r($res);
        //     $f = $UserUser->where(array('id' => $value['g_uid']))->field('uid,username')->find();
        //     // print_r($f);
        //     $user_info['img'] = C('AVATAR_IMG').$value['g_uid'];
        //     $user_info['username'] = $f['username'];
        //     $user_info['ranking'] = $res['ranking'];
        //     $user_info['lineup_score'] = $res['lineup_score']/10;
        //     $user_info['get_score'] = $value['score']/100;
        //     $friend_user_data[] = $user_info;
        // }
        for ($i = 0;$i <= 4;$i++) {
            $res = $UserChampion->where(array('uid' => $friends[$i]['s_uid']))->find();

            $f = $UserUser->where(array('id' => $friends[$i]['s_uid']))->field('uid,username')->find();

            if(!$f){
                $friend_user_data[] = ''; 
            }else{
                $user_info['img'] = C('AVATAR_IMG').$friends[$i]['s_uid'];
                $user_info['username'] = $f['username'];
                $user_info['ranking'] = $res['ranking'] ? $res['ranking'] : 0;
                $user_info['lineup_score'] = $res['count_score']/100 ? $res['count_score']/100 : 0;
                $user_info['get_score'] = $friends[$i]['score']/100;
                $friend_user_data[] = $user_info;     
            }
        }

        //获取我的参与奖品
        $join_price = M('UserPriceList')->where(array('uid'=>$user_data['id'],'price_type' => 1))->getField('type,num,goods_id');
        if($join_price['4']){//实物
            $_info = M('ShopGoods')->where('id='.$join_price[4]['goods_id'])->find();
            $prize['img'] = $_info['image'] ? $_info['image'] : $_info['avatar_img'];
            $prize['name'] = $_info['name'];
        }elseif($join_price['1']){ //门票
            $prize['img'] = 'http://static.aifamu.com/images/bet/201707/_5971d2b77cc764988.png';
            $prize['name'] = '门票'.$join_price['1']['num'].'个';
        }else{
            $prize['img'] = '';
            $prize['name'] = '暂无奖品';
        }

        //我的积分排名奖品
        $my_reward_info = $this->act_reward($_data['my_ranking']);

        $prize['rank_img'] = $my_reward_info['reward_img'];
        $prize['rank_name'] = $my_reward_info['reward_name'];
        $prize['prize_date'] = self::$prize_date;  
        

        


        //获取所有的阵容变化信息
        $json = file_get_contents(self::$f);
        $all_lineup_info = json_decode($json,true);
        // print_r($all_lineup_info);
        $now_h = date('j');
        $now_h = $now_h - 3;
        // $now_h = $this->return_now_num($now_h);
        //获取排行榜
        $guess_rank = $UserChampion->where('champion_id='.$this->champion_id)->order('count_score desc,add_time asc')->limit(10)->field('lineup_id,uid,ranking,lineup_score,count_score')->select();
        foreach ($guess_rank as $key => $value) {
            $u = $UserUser->where(array('id' => $value['uid']))->field('username')->find();
            $guess_rank[$key]['username'] = $u['username'];
            $guess_rank[$key]['img'] = C('AVATAR_IMG').$value['uid'];
            $guess_rank[$key]['lineup_score'] = $value['count_score']/100;
            $last_s = $all_lineup_info[$now_h][$value['lineup_id']];
            $last_a = $value['lineup_score']/10;
            if($last_s == $last_a){
                $guess_rank[$key]['rank_change'] = 'none';
            }else{
                // echo $now_h,'<br />';
                // echo $all_lineup_info[$value['lineup_id']][$now_h];
                // die;
                if($last_s > $last_a){
                    $guess_rank[$key]['rank_change'] = 'down';
                }else{
                    $guess_rank[$key]['rank_change'] = 'up';
                }

            }
        }
        $items['rank_info'] = $guess_rank;
        $items['prize'] = $prize;
    	$items['friends_data'] = $friend_user_data;
    	$items['user_lineup_info'] = $user_lineup_info;
    	$items['ranking_info'] = $_data;
        $items['user_data'] = $user_data;
    	$items['my_score_change'] = $my_score_change;
    	$this->returnMsg(0,'act',$items);
    }
    public function return_now_num(){
        $now_date = date('j');
        switch ($now_date) {
            case 3:
                $now_num = 1;
                break;
            case 4:
                $now_num = 2;
                break;
            case 5:
                $now_num = 3;
                break;
            case 6:
                $now_num = 4;
                break;
            case 7:
                $now_num = 5;
                break;
            case 8:
                $now_num = 6;
                break;
            case 9:
                $now_num = 7;
                break;
            case 10:
                $now_num = 8;
                break;
            case 11:
                $now_num = 9;
                break;
            case 12:
                $now_num = 10;
                break;
            case 13:
                $now_num = 11;
                break;
            default:
                $now_num = 1;
                break;
        }
        return $now_num;        
    }
	// 获取球员初始积分
    private function champion_player_data($player_id){
		$player_match = M('PlayerMatchDataDota2');
		$player_data = $player_match->where('project_id = 6')->select();
		$socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2);		
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
    	return round(array_sum($players[$player_id])/count($players[$player_id]));
   }

    //获取   
	public function get_eliminate_player(){
        $_data = M('ChampionEndTeam')->where(array('champion_id' => $this->champion_id))->find();
		$team_ids = explode(',', $_data['teams']);
        $team_arr = array(); //所有淘汰的队伍
        $team_info = array(); //淘汰队伍队员的信息
        foreach ($team_ids as $key => $value) {
            $s_j = explode(':', $value);
            $team_arr[] = $s_j[0];
            $team_info[$s_j[0]] = $s_j[1];
        }
		$sql = '';
		foreach ($team_arr as $key => $value) {
			if((count($team_arr) - 1) == $key){
				$sql .= 'team_id='.$value;
			}else{
				$sql .= 'team_id='.$value . ' or ';
			}
		}
		$data = M('MatchPlayerWcg')->where($sql)->getField('id,team_id');
        // echo M('MatchPlayerWcg')->getLastSql();die;
        // print_r($team_info);die;
        $players_info = array();
        foreach ($data as $key => $value) {
            $players_info[$key] = $team_info[$value];
        }
        return array('t_players' => array_keys($data),'players_info' => $players_info); //返回淘汰的所有选手和淘汰队伍的信息
	}
    public function get_eliminate_player_a(){
        $_data = M('ChampionEndTeam')->where(array('champion_id' => $this->champion_id))->find();
        $team_ids = explode(',', $_data['teams']);
        $team_arr = array(); //所有淘汰的队伍
        $team_info = array(); //淘汰队伍队员的信息
        foreach ($team_ids as $key => $value) {
            $s_j = explode(':', $value);
            $team_arr[] = $s_j[0];
            $team_info[$s_j[0]] = $s_j[1];
        }
        $sql = '';
        foreach ($team_arr as $key => $value) {
            if((count($team_arr) - 1) == $key){
                $sql .= 'team_id='.$value;
            }else{
                $sql .= 'team_id='.$value . ' or ';
            }
        }
        $data = M('MatchPlayerWcg')->where($sql)->getField('id,team_id');
        // echo M('MatchPlayerWcg')->getLastSql();die;
        // print_r($team_info);die;
        $players_info = array();
        foreach ($data as $key => $value) {
            $players_info[$key] = $team_info[$value];
        }

        print_r(array('t_players' => array_keys($data),'players_info' => $players_info));
    }
    //获取阵容的
    public function get_lineup_change($lineup_id){
        $file = self::$f;
        $json = file_get_contents($file);
        $data = json_decode($json,true);
        $l = array();
        $d = array();
        $now = time();
        $day = ceil(($now - 1501689600) / 24*3600);// 判断当前时间与开赛时间的天数差
        foreach ($data as $key => $value) {
            // if ($key > $day) {
            //     continue;
            // }
            $l[] = $value[$lineup_id] ? $value[$lineup_id] : '';
            $d[] = $key;
        }
        return array('date' => $d,'score' => $l);
    }


    //分享页面api
    public function share_lineup(){
        $this->pv_count(true);
        $this->ip_count();
        $UserUser = M('UserUser');
        //获取分享者的用户信息
        $wx_token = $this->_data['token']; //分享者的微信openid
        $share_user_data = M('UserOpenInfo')->where(array('wx_openid' => $wx_token))->find();
        if(!$share_user_data || !$share_user_data['user_id']){
            $this->returnMsg(2,'act');
        }
        $share_user_info = $UserUser->where('id='.$share_user_data['user_id'])->find();

        //获取登录用户的信息
        $user_token = $this->_data['user_token']; //用户的token
        $user_data = $UserUser->where(array('token' => $user_token))->find();
        if(!$user_data){
            $this->returnMsg(1,'act'); //用户不存在
        }
        $UserChampion = M('UserChampion');
        //检测登录的用户是否投注过
        $guess_res = $UserChampion->where(array('champion_id' =>$this->champion_id,'uid' => $user_data['id']))->find();
        if($guess_res){
            $data['user_is_guess'] = 'yes';
        }else{
            $data['user_is_guess'] = 'no';
        }
        //检测用户是否已经领取过积分奖励
        $g_res = M('RedPacketRecord')->where(array('s_uid' => $share_user_data['user_id'],'g_uid' => $user_data['id']))->find();
        if(!$g_res){
            //$Champion = A('Champion');
            //$g_score = $Champion->get_red_packet_a($wx_token,true);
            $g_score = array('error' =>1 ,'msg' => '积分领取已经截止') ;
        }else{
            $g_score = array('error' =>0 ,'msg' => $g_res['score']/100) ;
        }

        //获取分享者阵容的信息
        $guess_info = $UserChampion->where(array('champion_id' =>$this->champion_id,'uid' => $share_user_data['user_id']))->find();
        $lineup_info = M('ChampionLineup')->where(array('id' => $guess_info['lineup_id']))->find();
        $lineup_info_data = unserialize($lineup_info['lineup']);

        //获取分享者的好友信息
        $RedPacketRecord = M('RedPacketRecord');
        $friends = $RedPacketRecord->where(array('s_uid' => $share_user_data['user_id']))->select();
        
        $friend_user_data = array();
        foreach ($friends as $key => $value) {
            $f = $UserUser->where(array('id' => $value['g_uid']))->field('id,username')->find();
            $user_info['img'] = C('AVATAR_IMG').$f['id'];
            $user_info['username'] = $f['username'];
            $user_info['get_score'] = $value['score']/100;
            $user_info['date'] = date('Y-m-d H:i:s',$value['time']);
            $friend_user_data[] = $user_info;
        }
        //用户积分情况
        if($g_score['error'] == 0){
            $data['my_score'] = $g_score['msg'];
            $data['my_score_msg'] = '';
        }else{
            $data['my_score'] = 0;
            $data['my_score_msg'] = $g_score['msg'];
        }

        $data['lineup'] = $lineup_info_data;
        $data['friend'] = $friend_user_data;
        $data['share_user_info'] = $share_user_info['username'];
        $this->returnMsg(0,'act',$data);

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
    //海报页面打开统计接口
    public function other_count(){
        $this->pv_count(false,true);
    }
}