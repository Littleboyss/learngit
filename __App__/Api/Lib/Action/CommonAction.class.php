<?php

/**
 * Api基类 , 公共方法存放此处
 */
class CommonAction extends Action{

    protected $_data;  //post数据。
    protected $_user;  //存储用户基本信息

    public function __construct(){
        parent::__construct();
        // $dir = './'. date('Y/m_d');
        // if(!is_dir($dir)){
            // mkdir($dir,0777,true);
        // }
        // $path = $dir .'/'. date('H') . 'data.txt';
        // file_put_contents($path, json_encode($this->_request()) . '时间:'. date('Y/m/d H:i:s').'请求url:'.$_SERVER['REQUEST_URI']."\r\n",FILE_APPEND);
        // $this->_data = array_map('htmlspecialchars',$this->_request()); //线上阶段改为post,array_map 不能作用于多维数组
        $this->_data = $this->_request(); //线上阶段改为post
        
        $conf_a = C('is_maintenance_info'); //如果是系统维护则所有接口都不允许访问
        if($conf_a['status']){
            $this->returnMsg('400',$conf_a['msg']);
        }
    
    }

    /**
    * 检测用户是否已经登录
    * 如果需要做只允许用户在一个app进行登录,需要设置一个token,实时去查下token是否变化了
    */
    public function checklogin(){
        // $result = $this->en_de_crypt('de',cookie(C('LOGIN_STR'))); //cookie验证方式
        // if(!$result){
        //     $this->_user = false;
        //     return false;
        // }
        // $user_data = json_decode($result,true);
        // $this->_user = $user_data;
        // return $user_data;

        
        $token = $this->_data['user_token']; //token验证方式
        $result = M('UserUser')->where(array('token' => $token))->find();
        if(!$result){
            $this->_user = false;
            return false;
        }
        $this->_user = $result;
        return $result;
    }

    /**
    * 输出返回数据和错误提示信息
    */
    protected function returnMsg($error,$msgtype = false,$data = array(),$extra_data = array()){
        $items = array();
        $msg = C('msgConfig');
        $items['error'] = $error;
        $items['msg'] = $msgtype ? $msg[$msgtype][$error] : $msg['common'];
        $items['data'] = $data ? $data : array();
        $items['extra_data'] = $extra_data;
        echo json_encode($items);die;
    }

    /**
    * 缓存采用统一的方法,便于后期更换缓存方式
    * @param $data 需要缓存的数据
    * @param $type get:获取缓存 set:设置缓存 clear:清除缓存
    * @param $time 缓存的时间
    * @param $name 缓存的名称
    */
    protected function cache($type,$name,$data,$time = 6000){
        if($type == 'get'){
            $data = S($name);
            return $data;
        }
        if($type == 'set'){
            S($name,$data,$time);
            return true;
        }
        if($type == 'clear'){
            S($name,null);
            return true;
        }
        return false;
    }
    /*
    * 获取信息缓存
    */
    protected function getdata($name,$time = 3600){
        $data = $this->cache('get',$name); // 获取
        if(!$data){
            //获取房间类型
            if($name == 'room_type_name_all'){
                $data = M('MatchRoomType')->getField('id,name,tag_img');
            }
            //获取所有的项目
            if($name == 'project_name_all'){
                $data = M('MatchProject')->getField('id,name,img');
            }
            //获取所有球员
            if($name == 'player_data_all'){
                $data = M('MatchPlayer')->getField('id,team_id,name,img,salary,position,average,play_time,play_num,is_undetermined,is_illness,is_ban,is_out');
                foreach ($data as $key => $value) {
                    //对球员的平均分和平均时间进行格式化
                    $data[$key]['average'] = $value['average']/10;
                    $data[$key]['play_time'] = $value['play_time']/10;
                    $data[$key]['img'] = 'http://api.aifamu.com/img/playerimg/'.$value['id'].'.png';
                }
            }
            //获取所有lol选手
            if($name == 'player_data_lol'){
                $data = M('MatchPlayerWcg')->getField('id,team_id,name,project_id,img,e_name,salary,nationality,position,average,KDA,result,is_undetermined,is_illness,is_ban,is_out');
                foreach ($data as $key => $value) {
                    //对球员的平均分和平均时间进行格式化
                    $data[$key]['average'] = $value['average']/10;
                    $data[$key]['KDA'] = $value['KDA']/10;
                }
            }
            //获取所有奖金分配配置
            if($name == 'reward_rule_data'){
                $data = M('RewardRule')->getField('id,name,data');
            }
            //获取所有的赛事类型
            if($name == 'match_type_data'){
                $data = M('MatchType')->getField('id,name,introduce');
            }
            $this->cache('set',$name,$data,$time); //设置
        }
        return $data;
    }
    //返回比赛的开始时间
    protected function starttime($time){
        $s = array(0 => false,1 => '今天',2 => '明天', 3 => '后天');
        $nowtime = time();
        if($time < $nowtime){
            return date('m-d H:i',$time);
        }
        $time_stamp = strtotime(date('Y-m-d 00:00:00',$nowtime)); // 今天凌晨的时间戳
        $oneday = 3600*24;
        if($time < ($time_stamp + $oneday) && $time >= $nowtime){
            $match_time = 1;
        }elseif($time >= ($time_stamp + $oneday) && $time < ($time_stamp + $oneday*2)){
            $match_time = 2;
        }elseif($time >= ($time_stamp + $oneday*2) && $time < ($time_stamp + $oneday*3)){
            $match_time = 3;
        }else{
            $match_time = 0;
        }
        if($match_time > 0){
            return $s[$match_time] . date('H:i',$time);
        }else{
            return date('m-d H:i',$time);
        }
    }

    //加密-解密
    protected function en_de_crypt($type = 'en',$string = ''){
        $key = C('LOGIN_KEY'); //用于加密的key
        if($string == ''){
            return false;
        }
        if($type == 'en'){ //加密
            return des_encrypt($string,$key);
        }elseif($type == 'de'){ //解密
            return des_decrypt($string,$key);
        }else{
            return false;
        }
    }
    //获取房间所有的选手
    protected function getroomplayer($id,$is_return_key = false){
        $str = $is_return_key ? 'ttt' : 'fff';
        $cache_name = 'room_all_players_' . $id.$str;
        $room_players = $this->cache('get',$cache_name);
        if(!$room_players){
            $Map['status'] = 1; //发布中
            $IMap['room_id'] = $Map['id'] = $id;
            $data = M('MatchRoomInfo')->field('match_team')->where($IMap)->find();// 获取赛事列表
            $project_id = M('MatchRoom')->where($Map)->getField('project_id');// 获取项目id
            if(!$data){
                return false;
            }
            $match_list = explode(',', $data['match_team']);
            $MatchList = M('MatchList');

            $teams = array();//存储该房间所有比赛的队伍id
            foreach ($match_list as $key => $value) {
                $_data = $MatchList->field('team_a,team_b')->where(array('id' =>$value))->find();
                $teams[] = $_data['team_a'];
                $teams[] = $_data['team_b'];
            }
            //获取该该房间所有球员包括工资
            $teams = array_values(array_unique($teams)); //去除重复的队伍
            if($project_id == 5){
                // lol
                $cache_name_a = 'player_data_lol';
                $PlayerMatchData_name = 'PlayerMatchDataLol';
            }elseif ($project_id == 6) {
                // dota2
                $cache_name_a = 'player_data_lol';
                $PlayerMatchData_name = 'PlayerMatchDataDota2';
            }else{
                // NBA
                $cache_name_a = 'player_data_all';
                $PlayerMatchData_name = 'PlayerMatchData';
            }
            $players = $this->getdata($cache_name_a);
            if($players == false){
                return false;
            }
            $room_players = array();//存储该房间的所有的球员
            foreach ($players as $key => $value) {
                if(in_array($value['team_id'], $teams)){
                    if($is_return_key){
                        $room_players[] = $value;
                    }else{
                        $room_players[$value['id']] = $value;
                    } 
                }
            }
            //获取房间球员所属的比赛
            $match_list = $this->getroommatch($id);
            foreach ($room_players as $key => $value) {
                foreach ($match_list as $k => $v) {
                    if(in_array($value['team_id'], array($v['team_a'],$v['team_b']))){
                        $room_players[$key]['match_id'] = $v['id'];
                    }
                }
            }
            //获取球员的比赛状态
            $PlayerMatchData = M($PlayerMatchData_name);
            foreach ($room_players as $key => $value) {
                $_data = $PlayerMatchData->field('is_join')->where(array('match_id' => $value['match_id'],'player_id' => $key))->find();
                if($_data){
                    if($_data['is_join'] == 1){
                        $room_players[$key]['state'] = 1; //锁定状态
                    }else{
                        $room_players[$key]['state'] = 2; //非锁定状态
                    }
                }else{
                    $room_players[$key]['state'] = 2;
                }
            }
            $this->cache('set',$cache_name,$room_players,3600*24);
        }
        return $room_players;
    }
    //获取房间的详情信息
    //$id 房间的id
    //$type less(只获取列表信息)
    protected function getroomdetail($id,$type = 'less',$settlement_status = 0){
        $Map['status'] = 1; //发布中
        $IMap['room_id'] = $Map['id'] = $id;
        if($settlement_status != 0){
            $Map['settlement_status'] = $settlement_status; //未结算
        }
        $MatchRoom = M('MatchRoom');
        $data = $MatchRoom->where($Map)->find();
        if(!$data){
            return false;
        }
        $room_type_data = $this->getdata('room_type_name_all',86400); //获取房间类型,添加缓存
        $project_data = $this->getdata('project_name_all',86400);

        if($room_type_data === false || $project_data == false){
            return false;
        }
        $data['type_name'] = $room_type_data[$data['type_id']]['name']; //房间名称

        if($data['tag_img'] == 0){
            $data['type_img'] = $room_type_data[$data['type_id']]['tag_img']; // 房间图标
        }else{
            $data['type_img'] = C('ROOM_TAG_IMG_URL').C('ROOM_TAG')[$data['tag_img']]['tag_img']; // 房间图标
        }

        
        $data['project_name'] = $project_data[$data['project_id']]['name'];//项目图标
        $data['match_start_date'] = $this->starttime($data['match_start_time']); //房间开始时间的格式化
        $data['lineup'] = C('MATCH_ROOM_LINEUP')[$data['lineup_id']];//阵容数据配置
        $data['open_tag'] = sprintf(C('ROOM_OPEN_RULE')[$data['open_id']],$data['open_num']); //房间开奖规则的标签

        //获取奖励的标签
        if($data['reward_id'] == 1 || $data['reward_id'] == 2 || $data['reward_id'] == 12){
            $str = sprintf(C('REWARD_RULE_TAG')[$data['reward_id']],$data['prize_num']);
            $data['reward_tag'] = str_replace('|', '%', $str);
        }else{
            $data['reward_tag'] = C('REWARD_RULE_TAG')[$data['reward_id']];
        }
        $data['prize_name'] = C('PRIZE_TYPE')[$data['prize_type']]; //奖励的类型名称

        if($data['reward_id'] == 12){//实物奖品,返回实物奖品的名称
            $goods = M('ShopGoods')->where(array('id' => $data['prize_goods_id']))->find();

            $data['goods_name'] = $goods['name'];

        }else{
            $data['goods_name'] = '';
        }

        if($type == 'less'){
            return $data;
        }
        //获取比赛的详情信息
        
        $data['match_list'] = $this->getroommatch($id);

        $allteams = $this->cache('get','allteams');
        if(!$allteams){
            $allteams = M('MatchTeam')->getField('id,name');
            $this->cache('set','allteams',$allteams,3600*24);
        }
        $f = array();
        foreach ($data['match_list'] as $key => $value) {
            $f[] = $allteams[$value['team_a']];
            $f[] = $allteams[$value['team_b']];
        }

        $data['team_tags'] = implode(':', $f);


        $guess_user = M('UserGuessRecord')->where(array('room_id' => $id))->order('add_time desc')->limit(50)->select();
        foreach ($guess_user as $key => $value) {
            $user_data = $this->getuserdata($value['uid']);
            $join_user[$key]['username'] = $user_data['username'];
            $join_user[$key]['avatar_img'] = $user_data['avatar_img'];
            $join_user[$key]['rank_name'] = $user_data['rank_name'];
            $join_user[$key]['rank_img'] = $user_data['rank_img'];
            $join_user[$key]['guess_num'] = $value['guess_num'];
        }
        $data['join_user_info'] = '显示最新投注的前50名用户';
        $data['join_user'] = $join_user ? $join_user : array();
        return $data;
    }
    //获取用户的资料
    protected function getuserdata($uid){
        $UserUser = M('UserUser');
        $user_data = $UserUser->field('username,rank')->where(array('id' => $uid))->find();
        $user_data['avatar_img'] = C('AVATAR_IMG').$uid;
        $user_data['rank_name'] = $this->getrankname($user_data['rank']);
        $rank = $this->cache('get','rank_name_info');
        if(!$rank) {
            $rank = M('UserRank')->getField('id,name,avatar_img');
            $this->cache('set','rank_name_info',$rank);
        }
        $user_data['rank_img'] = $rank[$user_data['rank']]['avatar_img'] ? $rank[$user_data['rank']]['avatar_img'] : '';
        return $user_data;
    }
    //获取称号名
    //$rank_id 称号就的id
    protected function getrankname($rank_id = 1){
        $data = M('UserRank')->field('name')->where('id ='.$rank_id)->find();
        return $data['name'];
    }
    //获取房间所有比赛和所属赛事
    protected function getroommatch($id){
        $data_info = M('MatchRoomInfo')->where(array('room_id' => $id))->find();
        //查询房间使用的比赛的详情
        $teams = explode(',',$data_info['match_team']);
        $match_type = $this->getdata('match_type_data');
        // print_r($match_type);die;
        $MatchList = M('MatchList');
        $all_teams = $this->all_teams();
        // file_put_contents('./jjj.txt', json_encode($all_teams));
        foreach ($teams as $key => $value) {
            $_data = $MatchList->where(array('id' => $value))->find();
            // print_r($_data);die;
            $_data['img_a'] = $all_teams[$_data['team_a']]['img'];
            $_data['img_b'] = $all_teams[$_data['team_b']]['img'];

            $_data['name_a'] = $all_teams[$_data['team_a']]['name'];
            $_data['name_b'] = $all_teams[$_data['team_b']]['name'];

            $_data['match_name'] = $match_type[$_data['match_name_id']]['name'];
            $_data['match_time_date'] = date('Y-m-d H:i',$_data['match_time']);
            $data[] = $_data;
        }
        return $data;
    }

    /**
    * 获取和设置用户的所选阵容
    * @param $type set 设置  get 获取
    * @param $lineup_type 1推荐整容2已存整容
    * @param $match_list 比赛赛事的id列表 用,分隔 根据赛事来保存推荐的阵容
    * @param $lineup 所选阵容
    * @param $lineup_id 阵容id 5/8人
    * @param $uid 用户的id
    */
    protected function set_get_lineup($type = 'set',$lineup_type,$match_list,$lineup_id,$uid,$lineup){
        $UserLineup = M('UserLineup');

        if($type == 'set'){
            //查找该用户的该比赛的该阵容是否已经缓存过了
            $data['lineup_id'] = $lineup_id;
            $data['uid'] = $uid;
            $data['lineup_token'] = md5(json_encode($lineup));
            //为保证数据的唯一性,对比赛进行排序操作进行
            $match_array = explode(',', $match_list);
            // file_put_contents('./2.txt', $match_list);
            sort($match_array,SORT_REGULAR);
            $match_list = implode(',', $match_array);
            $data['match_id_token'] = md5($match_list);
            $result = $UserLineup->where($data)->find();
            if($result){ //阵容已经存在
                if($result['type'] == $lineup_type){
                    $res = true;
                }else{
                    $_data['type'] = $lineup_type;
                    $_data['join_room_num'] = $result['join_room_num'] + 1;
                    $res = $UserLineup->where(array('id' => $result['id']))->save($_data);

                }
            }else{
                $data['type'] = $lineup_type;
                $data['add_time'] = time();
                $data['lineup'] = serialize($lineup);
                $res = $UserLineup->add($data);            
            }
            return $result; // 返回数据操作的结果
        }else{

        }
    }
    // NBA积分规则
    // $player_id 选手的id
    // $player_match_data选手比赛的得分数据
    // @return 返回积分规则
    protected function scorerule($player_id,$player_match_data){
        $score_sum = 0;
        $socre_rule = array('get_score' => 1,'backboard' => 1.2,'help_score' => 1.5,'hinder_score' => 2,'cover_score' => 2,'mistake_score' => -1,'three_point' => 0.5); //积分规则配置
        foreach ($player_match_data as $key => $value) {
            $score_sum += $socre_rule[$key] * $value;
        }
        return $score_sum;
    }
    // 积分规则 DOTA和LOL
    // $player_id 选手的id
    // $player_match_data选手比赛的得分数据
    // @return 返回积分规则
    protected function scorerule_lol($player_id,$player_match_data){
        $score_sum = 0;
        $position = M('MatchPlayerWcg')->where(array('id'=>$player_id))->getField('position');
        //var_dump($position);exit;
        if ($position == 6) {
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
        }else{
            $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
        }
        foreach ($player_match_data as $key => $value) {
            if ($key == 'remain') {
                if ($player_match_data['is_win'] != 1) {
                    continue;
                }
            }else{
                $score_sum += $socre_rule[$key] * $value;
            }
        }

        return number_format($score_sum,1);
    }
    // 帐目变化入user_account表
    // $class_id     int     帐变类型
    // $type         tinyint 1为门票，2为砖石，3为木头
    // $uid          int     用户编号
    // $nums         int     数量
    // $is_back_nums bool    是否为账目增加默认为false
    protected function insert_account($class_id,$type,$uid,$nums,$is_back_nums=false,$room_id = 0){
        if ($is_back_nums) {
            $account['back_nums']=$nums;
        }else{
            $account['go_nums']=$nums;
        }
        $account['class_id']=$class_id;
        $account['type']=$type;
        $account['user_id']=$uid;
        $account['addtime']=time();
        if($room_id != 0){
            $account['room_id'] = $room_id;
        }
        $res = M('UserAccount')->add($account);
        return $res;
    }
    // 求二维数组各个元素平均值
    protected function array_avg($array, $avgby = NULL) { 
        $array_avg = array (); 
        $number = count ( $array ); 
        foreach ( $array as $key => $value ) { 
            if ($avgby) { 
                $avg_key = $value[$avgby]; 
                $array_avg[$avg_key]['count'] ++; 
                foreach ( $value as $k => $v ) { 
                    $array_avg[$avg_key][$k] += $v; 
                } 
            } else { 
                foreach ( $value as $k => $v ) { 
                    $array_avg[$k] += $v; 
                } 
            } 
        } 
        $array = array (); 
        foreach ( $array_avg as $key => $value ) { 
            if ($avgby) { 
                foreach ( $value as $k => $v ) { 
                    $array[$key][$k] = $v / $value['count']; 
                } 
            } else { 
                $array[$key] = $value / $number; 
            } 
        } 
        return $array; 
    }

    //c_url请求
    protected function c_url($url,$tree,$request='POST'){
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        //设置请求的方式,GET或POST
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, $request );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        //请求的头信息
        if(!empty($header)){
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );   
        }
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $tree );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $res = curl_exec ( $ch );
        curl_close ( $ch );
        return $res;
    }

    //上传url头像
    //$url 头像的url
    //$type 上传的类型 (头像等)
    protected function url_upload($url,$uid){
        $path = './avator/%s/';
        $userDir = floor($uid/500);
        $Folder = sprintf($path, $userDir);
        $data = $this->c_url($url,'','GET');
        if(!$data){
            // file_put_contents('./user_data.txt', $url.'___'.$data);
            return false;
        }
        if (!file_exists($Folder)) {
            // file_put_contents('./user_data.txt', 123);
            @mkdir($Folder,0777,true);
        }
        $file_name = $Folder.$uid.'.jpg';
        $res= file_put_contents($file_name, $data);
        return $res;        
    }

    //请求统计
    protected function pv_count($share = false,$other_pv = false){

        $date = intval(date('Ymd'));
        $ChampionCount = M('ChampionCount');
        $res = $ChampionCount->where(array('date' => $date))->find();
        if($res){
            $ChampionCount->where('id='.$res['id'])->setInc('pv',1); //访问量+1
            if($share){
                 $ChampionCount->where('id='.$res['id'])->setInc('share_pv',1); //访问量+1
            }
            if($other_pv){
                $ChampionCount->where('id='.$res['id'])->setInc('oher_pv',1); //访问量+1
            }
        }else{
            if($share){
                $arr = array('date' => $date,'pv' => 1,'share_pv' => 1);
            }else{
                $arr = array('date' => $date,'pv' => 1);
            }

            if($other_pv){
                $arr['oher_pv'] = 1;
            }

            $ChampionCount->add($arr); //访问量+1

        }
        // file_put_contents('./sql.txt', $ChampionCount->getLastSql());
    }

    //请求统计
    protected function ip_count(){
        $ip = ip2long(get_client_ip());
        $date = intval(date('Ymd'));
        $IpCount = M('IpCount');
        $res = $IpCount->where(array('date' => $date,'ip' => $ip))->find();
        file_put_contents('./json.txt', json_encode($res) .'__'. $IpCount->getLastSql()."\r\n",FILE_APPEND);
        if($res){
            $IpCount->where('id='.$res['id'])->setInc('ip_request',1); //访问量+1
        }else{
            $arr = array('date' => $date,'ip' => $ip,'ip_request' =>1);
            $IpCount->add($arr); //访问量+1
        }
    }

    //数据请求统计
    protected function data_count($start = false,$new_user = false,$pay_user = false,$pay_money = false){
        $date = intval(date('Ymd'));
        $Count = M('Count');
        $res = $Count->where(array('date' => $date))->find();
        if($res){
            if($start){
                $Count->where('id='.$res['id'])->setInc('start',1); //访问量+1
            }
            if($new_user){
                $Count->where('id='.$res['id'])->setInc('new_user',1); //访问量+1
            }
            if($pay_user){
                $Count->where('id='.$res['id'])->setInc('pay_user',1); //访问量+1
            }
            if($pay_money){
                $Count->where('id='.$res['id'])->setInc('pay_money',$pay_money); //单位分
            }
        }else{
            $data = array();
            $data['date'] = $date;
            if($start){
                $data['start'] = 1;
            }
            if($new_user){
                $data['new_user'] = 1;
            }
            if($pay_user){
                $data['pay_user'] = 1;
            }
            if($pay_money){
                $data['pay_money'] = $pay_money;
            }
            $Count->add($data); //访问量+1
        }
    }
    //返回项目的选手或球员
    protected function project_players($project_id){
        if ($project_id == 5 || $project_id == 6) {
            $MatchPlayer = M('MatchPlayerWcg') ;
        }elseif($project_id == 4){
            $MatchPlayer = M('MatchPlayer');
        }else{
            return false;
        }
        $cache_name = 'project_ids_player_'.$project_id;
        $players = $this->cache('get',$cache_name);
        if(!$players){
            if ($project_id == 4) {
                $field = 'id,team_id,name,salary,number,position,average';
            }else{
                $field = 'id,team_id,name,salary,img,average,position,result,KDA';
            }

            $players = $MatchPlayer->where(array('project_id' => $project_id))->getField($field);

            foreach ($players as $key => $value) {
                if($project_id == 4){
                    $players[$key]['img'] = 'http://api.aifamu.com/img/playerimg/'.$value['id'].'.png';
                }else{
                    $players[$key]['KDA'] = $value['KDA']/10;
                }
                $players[$key]['average'] = $value['average']/10;
            }
            $this->cache('set',$cache_name,$players,3600*24);
        }
        return $players;
    }

    //获取所有的队伍信息
    protected function all_teams(){
        $cache_name = 'all_teams_p';
        $teams = $this->cache('get',$cache_name);
        if(!$teams){
            $teams = M('MatchTeam')->getField('id,project_id,name,img,short_name');
            $teams = $this->cache('set',$cache_name,$teams,3600*24);
        }
        return $teams;
    }

    // 获取用户名分表的名称
    protected function get_hash_table($table,$userid) {  
        $str = crc32($userid);  
        if($str<0){  
        $hash = substr(abs($str), 0, 1);  
        }else{  
        $hash = substr($str, 0, 1);  
        }  

        return $table."_".$hash;  
    }
    
    //获取选手最近几场的积分变化
    protected function player_score_change($player_id,$project_id,$limit = 10){
        $cache_name = 'player_score_change.'.$player_id;
        $data = $this->cache('get',$cache_name);
        if(!$data){
            if($project_id == 4){ //NBA
                $Model = M('PlayerMatchData');
                $field = 'score';
            }
            if($project_id == 5){ //LOL
                $Model = M('PlayerMatchDataLol');
                $field = 'scores';
            }
            if($project_id == 6){ //DOTA2
                $Model = M('PlayerMatchDataDota2');
                $field = 'scores';
            }
            $Map['player_id'] = $player_id;
            $Map[$field] = array('gt',0);
            $scores = $Model->where($Map)->order('id desc')->limit($limit)->select();

            // file_put_contents('./data.txt', $Model->getLastSql."\r\n",FILE_APPEND);
            $data = array();
            for($i = 0;$i <10 ;$i++){
                $data[] = $scores[$i][$field] ? $scores[$i][$field]/10 : 0;
            }
            $this->cache('set',$cache_name,$data,3600*24);

        }
        return $data;
    }

}