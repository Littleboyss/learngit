<?php
/**
* 竞猜需要登录部分
*/
class BetAction extends LoginAction{

    /**
    * 此方法只负责推荐随机整容,推荐整容是重复,请到原方法中判断,然后再次请求
    * @param $lineup_id 阵容类型 5/8人
    * @param $player 所有参赛的球员
    */
    protected function getpositionplayer($lineup_id,$players,$matchtoken){
        // echo json_encode($players);die;
        $lineup_array = $this->cache('get',$matchtoken . '1');
        if(!$lineup_array){
            // $lineup_array
            // print_r($players);
            $player_position = array(); // 存储球员位置的
            foreach ($players as $key => $value) { //把所有的选手的阵容位置分割出来
                if($value['position'] == 1){
                    $player_position[1]['salary'][] = $value['salary'];
                    $player_position[1]['players'][] = $value['id'];
                }
                if($value['position'] == 2){
                    $player_position[2]['salary'][] = $value['salary'];
                    $player_position[2]['players'][] = $value['id'];
                }
                if($value['position'] == 3){
                    $player_position[3]['salary'][] = $value['salary'];
                    $player_position[3]['players'][] = $value['id'];
                }
                if($value['position'] == 4){
                    $player_position[4]['salary'][] = $value['salary'];
                    $player_position[4]['players'][] = $value['id'];
                }
                if($value['position'] == 5){
                    $player_position[5]['salary'][] = $value['salary'];
                    $player_position[5]['players'][] = $value['id'];
                }
            }

            //print_r($player_position);die;

            $lineup_array = array(); //存储所选阵容的
            //计算排列组合数目 , 效率很低 , 加缓存
            foreach ($player_position[1]['salary'] as $key1 => $value1) {
                foreach ($player_position[2]['salary'] as $key2 => $value2) {
                    foreach ($player_position[3]['salary'] as $key3 => $value3) {
                        foreach ($player_position[4]['salary'] as $key4 => $value4) {
                            foreach ($player_position[5]['salary'] as $key5 => $value5) {
                                $salary_sum = $value1 + $value2 + $value3 + $value4 + $value5;
                                if($salary_sum <= 125 && $salary_sum >= 110){
                                    $lineup_array[] = array($player_position[1]['players'][$key1],$player_position[2]['players'][$key2],$player_position[3]['players'][$key3],$player_position[4]['players'][$key4],$player_position[5]['players'][$key5]);
                                }
                            }
                        }
                    }
                }
            }
            //print_r($lineup_array);die;
            $this->cache('set',$matchtoken . '1' ,$lineup_array,3600*24);
        }
            // echo json_encode($lineup_array);die;
        if($lineup_id == 1){
            return $lineup_array[rand(0,count($lineup_array) -1)]; // 返回一个随机的5人阵容
        }

        if($lineup_id == 2){ //选择8人整容
            //$lineup_array_2 = $this->cache('get',$matchtoken . '2');
            $lineup_5 = $lineup_array[rand(0,count($lineup_array) -1)]; // 获取一个随机的5人阵容
            if(!$lineup_array_2){

                $player_position = array(); // 存储球员位置的
                
                foreach ($players as $key => $value) { //把所有的选手的阵容位置分割出来
                    if(in_array($value['id'], $lineup_5)){
                        continue;
                    }
                    if($value['position'] == 1 || $value['position'] == 2){
                        $player_position[6]['salary'][] = $value['salary'];
                        $player_position[6]['players'][] = $value['id'];
                    }
                    if($value['position'] == 3 || $value['position'] == 4){
                        $player_position[7]['salary'][] = $value['salary'];
                        $player_position[7]['players'][] = $value['id'];
                    }
                    $player_position[8]['salary'][] = $value['salary'];
                    $player_position[8]['players'][] = $value['id'];
                }
                // echo json_encode($player_position);die;
                foreach ($player_position[6]['salary'] as $key6 => $value6) {
                    foreach ($player_position[7]['salary'] as $key7 => $value7) {
                        foreach ($player_position[8]['salary'] as $key8 => $value8) {
                            $salary_sum = $value6 + $value7 + $value8;
                            // echo $salary_sum;
                            if($salary_sum <= 75 && $salary_sum >73 && $player_position[6]['players'][$key6] != $player_position[8]['players'][$key8] && $player_position[7]['players'][$key7] != $player_position[8]['players'][$key8]){

                                $lineup_array_2[] = array($player_position[6]['players'][$key6],$player_position[7]['players'][$key7],$player_position[8]['players'][$key8]);
                            }
                        }
                    }
                }
                $this->cache('set',$matchtoken . '2',$lineup_array_2,3600*24);
            }

            $lineup_8 = $lineup_array_2[rand(0,count($lineup_array_2) -1)];
            return array_merge($lineup_5,$lineup_8);
        }
        if($lineup_id == 3 ){ //选择lol的阵容类别
            $lineup_array_3 = $this->cache('get',$matchtoken . '3');
            $lineup_5 = $lineup_array[rand(0,count($lineup_array) -1)]; // 获取一个随机的5人阵容
            if(!$lineup_array_3){

                $player_position = array(); // 存储球员位置的
                foreach ($players as $key => $value) { //把所有的选手的阵容位置分割出来
                    if(in_array($value['id'], $lineup_5)){
                        continue;
                    }
                    if($value['position'] == 6){
                        $player_position[6]['salary'][] = $value['salary'];
                        $player_position[6]['players'][] = $value['id'];
                    }elseif($value['position'] == 1 || $value['position'] == 2){
                        $player_position[7]['salary'][] = $value['salary'];
                        $player_position[7]['players'][] = $value['id'];
                    }elseif($value['position'] == 3 || $value['position'] == 4 || $value['position'] == 5){
                        $player_position[8]['salary'][] = $value['salary'];
                        $player_position[8]['players'][] = $value['id'];
                    }
                }
                
                foreach ($player_position[6]['salary'] as $key6 => $value6) {
                    foreach ($player_position[7]['salary'] as $key7 => $value7) {
                        foreach ($player_position[8]['salary'] as $key8 => $value8) {
                            $salary_sum = $value6 + $value7 + $value8;
                            // echo $salary_sum,'<br />';
                            if($salary_sum <= 75 && $salary_sum >= 65 && $player_position[6]['players'][$key6] != $player_position[8]['players'][$key8] && $player_position[7]['players'][$key7] != $player_position[8]['players'][$key8]){
                                $lineup_array_3[] = array($player_position[6]['players'][$key6],$player_position[7]['players'][$key7],$player_position[8]['players'][$key8]);
                            }
                        }
                    }
                }
                $this->cache('set',$matchtoken . '3',$lineup_array_3,3600*24);
            }
            $lineup_8 = $lineup_array_3[rand(0,count($lineup_array_3) -1)];
            $lineup_res = array_merge($lineup_5,$lineup_8);
            $salary_sum =0;// 工资总数
            // 判断一个队的人数不能超过4个
            foreach ($lineup_res as $item) {
                $salary_sum +=  $players[$item]['salary'];
                // 不把战队作为选手存入数组中
                if ($players[$item]['position'] == 6) {
                    continue;
                }
                $res[$players[$item]['team_id']][$item] = $item;
            }
            if($salary_sum>200){
                return false;
            }
            foreach ($res as  $team_num) {
                if (count($team_num) > 4) {
                    return false;
                }
            }
            return $lineup_res;
        }
        if($lineup_id == 4 ){ //选择dota2的阵容类别
            //$lineup_array_4 = $this->cache('get',$matchtoken . '4');
            $lineup_5 = $lineup_array[rand(0,count($lineup_array) -1)]; // 获取一个随机的5人阵容
           
            if(!$lineup_array_4){
                $player_position = array(); // 存储球员位置的
                foreach ($players as $key => $value) { //把所有的选手的阵容位置分割出来
                    if(in_array($value['id'], $lineup_5)){
                        continue;
                    }
                    if($value['position'] == 6){
                        $player_position[6]['salary'][] = $value['salary'];
                        $player_position[6]['players'][] = $value['id'];
                    }elseif($value['position'] == 1 || $value['position'] == 2){
                        $player_position[7]['salary'][] = $value['salary'];
                        $player_position[7]['players'][] = $value['id'];
                    }elseif($value['position'] == 3 || $value['position'] == 4 || $value['position'] == 5){
                        $player_position[8]['salary'][] = $value['salary'];
                        $player_position[8]['players'][] = $value['id'];
                    }
                }
                
                foreach ($player_position[6]['salary'] as $key6 => $value6) {
                    foreach ($player_position[7]['salary'] as $key7 => $value7) {
                        foreach ($player_position[8]['salary'] as $key8 => $value8) {
                            $salary_sum = $value6 + $value7 + $value8;
                            //echo $salary_sum,'<br />';
                            if($salary_sum <= 75 && $salary_sum >70 && $player_position[6]['players'][$key6] != $player_position[8]['players'][$key8] && $player_position[7]['players'][$key7] != $player_position[8]['players'][$key8]){
                                $lineup_array_4[] = array($player_position[6]['players'][$key6],$player_position[7]['players'][$key7],$player_position[8]['players'][$key8]);
                            }
                        }
                    }
                }
                $this->cache('set',$matchtoken . '4',$lineup_array_4,3600*24);
            }
            $lineup_8 = $lineup_array_4[rand(0,count($lineup_array_4) -1)];
            $lineup_res = array_merge($lineup_5,$lineup_8);

            $salary_sum =0;// 工资总数
            // 判断一个队的人数不能超过4个
            foreach ($lineup_res as $item) {
                $salary_sum +=  $players[$item]['salary'];
                // 不把战队作为选手存入数组中
                if ($players[$item]['position'] == 6) {
                    continue;
                }
                $res[$players[$item]['team_id']][$item] = $item;
            }
            if($salary_sum>200){
                return false;
            }
            foreach ($res as  $team_num) {
                if (count($team_num) > 4) {
                    return false;
                }
            }
            return $lineup_res;
        }
    }

	/**
	* 推荐阵容
    * 没有做房间配置检测
	*/
	public function recommendlineup(){
        $id = $this->_data['id']; // 房间的id
        if(!is_numeric($id)){
            $this->returnMsg(1);
        }
        // $lineup_id = $this->_data['lineup_id'];
        $lineup_type = M('MatchRoom')->field('lineup_id')->where(array('id' => $id))->find();
        $lineup_id = $lineup_type['lineup_id'];
        if(!$lineup_type){
            $this->returnMsg(1);
        }

        $lineup_type = C('MATCH_ROOM_LINEUP')[$lineup_id]; //阵容的id 5人/8人?
        if(!in_array($lineup_id, array_keys(C('MATCH_ROOM_LINEUP')))){
            $this->returnMsg(1);
        }
        $players = $this->getroomplayer($id); //获取房间的所有球员
        if($players === false){
            $this->returnMsg(1,'room');
        }
        $match_list = M('MatchRoomInfo')->where(array('room_id' => $id))->find();
        //为保证阵容推荐数据的唯一性,对比赛进行排序操作进行
        $match_array = explode(',', $match_list['match_team']);
        sort($match_array,SORT_REGULAR);
        $matchtoken = implode(',', $match_array);
        $break = 1;

        //print_r($players);die;
        // echo $lineup_id,'<br />';echo $matchtoken;die;

        $lineup_data = $this->getpositionplayer($lineup_id,$players,$matchtoken); //获取推荐的阵容
        //var_dump($lineup_data);exit;
        while(!$lineup_data) { 
            $break ++;
            if ($break == 10) { // 尝试10次
                $this->returnMsg(11,'room');// 暂时无法获取阵容
            }
            $lineup_data = $this->getpositionplayer($lineup_id,$players,$matchtoken); //获取推荐的阵容
        }
        $salary_sum =0;// 工资总数
        $score_sum =0;// 总积分
        foreach ($lineup_data as $key => $value) {
            $salary_sum += $players[$value]['salary'];
            $score_sum += $players[$value]['average'];
            $lineup_data[$key+1] = $value;
        }
        unset($lineup_data[0]);
        if ($lineup_id == 1) {
            $money = 125;
        }else{
            $money = 200;
        }
        $extra_data['salary_sum'] = $money-$salary_sum; //剩余工资
        $extra_data['score_sum'] =$score_sum;//number_format($score_sum/10,1);// 总积分
        $this->set_get_lineup($type = 'set',1,$match_list['match_team'],$lineup_id,$this->_user['id'],$lineup_data);
        // 返回阵容积分与可用工资
        
        $this->returnMsg(0,'room',$lineup_data,$extra_data);
	}

    //投注,竞猜,此接口需要好好测试下数据的正确性
    public function guess(){
        $id = $this->_data['id']; // 房间的id
        $guess_num = $this->_data['guess_num']; //下注次数
        $team_info = $this->_data['team_info']; //所选阵容,数组
        //获取房间的信息,用于验证,数据的正确性
        // echo json_encode($team_info);die;
        if(!is_numeric($id)){
            $this->returnMsg(7,'guess');// 没有接收到房间id
        }
        if(!is_numeric($guess_num) || $guess_num == 0){
            $this->returnMsg(8,'guess'); // 没有接收到投注数量
        }
        $Map['status'] = 1; //发布中
        $Map['settlement_status'] = 1; //未结算
        $IMap['room_id'] = $Map['id'] = $id;
        $MatchRoom = M('MatchRoom');
        $data = $MatchRoom->where($Map)->find();
        $data_info = M('MatchRoomInfo')->where($IMap)->find();
        $data['match_team'] = $data_info['match_team'];
        if(!$data){ //房间是否存在
            $this->returnMsg(2,'room');
        }
        if($data['match_end_time'] < time()){ //竞猜是否截止
            $this->returnMsg(3,'room');
        }

        // if($id == 116){
        //     if(!in_array($this->_user['id'], array(1,2,3,4))){
        //         $this->returnMsg(5,'guess'); //主播没有投注指定的房间,返回错误
        //     }            
        // }


        //查询投注的用户是否是主播用户
        if($this->_user['is_special'] == 1){
            if($this->_user['id'] != $data['special_uid']){
                $this->returnMsg(5,'guess'); //主播没有投注指定的房间,返回错误
            }
        }
        $UserGuessRecord = M('UserGuessRecord');
        // echo 123456;die;
        //主播投注,不检测满注和用户的门票数目
        if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){
            $specia_num = $user_guess_num = $UserGuessRecord->where(array('uid' => $this->_user['id'],'room_id' =>$id))->sum('guess_num');
            if($specia_num > 1){
                $this->returnMsg(4,'room'); //主播只能投注一次
            }
        }else{
            //检测房间是否已满
            if($data['allow_guess_num'] <= $data['now_guess_num']){
                $this->returnMsg(8,'room');
            }

            //检测用户门票是否足够
            if($this->_user['entrance_ticket'] < $data['price'] * $guess_num){
                $this->returnMsg(5,'room');
            }           
        }
        //检测用户是否投注过该房间
        $uid = $this->_user['id'];
        $user_guess_num = $UserGuessRecord->where(array('uid' => $uid,'room_id' =>$id))->sum('guess_num');
        if(($guess_num + $user_guess_num) > $data['allow_uguess_num']){
            $this->returnMsg(4,'room'); //投注达到上限禁止投注
        }

        if(($guess_num+$data['now_guess_num']) > $data['allow_guess_num']){
            $this->returnMsg(4,'room'); //投注达到上限禁止投注
        }
        
        $lineup_data = C('MATCH_ROOM_LINEUP')[$data['lineup_id']]; // 本房间阵容的配置
        if(count(array_unique($team_info)) != $lineup_data['num']){ //检测人数是否选择正确
            $this->returnMsg(9,'guess');// 投注人数不正确
        }
        $players = $this->getroomplayer($id); // 获取该房间所有的球员
        $players_id = array_keys($players); //房间所有球员的一维数组
        //var_dump($players);
        $salary = 0; //所选球员的工资和
        $lineup_check = array();
        $lineup_team = array(); //存储所选阵容的队伍id
        foreach ($team_info as $key => $value) { //检测所选阵容是否在房间的选手列表中

            $lineup_team[] = $players[$value]['team_id'];//将所选球员的队伍写到数组

            if(!in_array($value, $players_id)){
                //echo 111;die;
                $this->returnMsg(4,'guess'); //检测所选球员是否在房间球员中
            }
            $salary += $players[$value]['salary']; //计算所选球员的工资
            if($key <= 5){ //检测选择位置信息
                $lineup_check[] = $players[$value]['position'] == $key ? true : false;
            }else{
                // 如果为电竞项目
                if ($data['project_id'] == 5 || $data['project_id'] == 6) {
                    if($key == 7){
                        $positions = array(1,2,3,4,5);
                    }
                    if($key == 8){
                        $positions = array(1,2,3,4,5);
                    }
                    if($key == 6){
                        $positions = array(6);
                    }
                    //$res[$players[$value]['team_id']][$value] = $value;
                    $lineup_check[] = in_array($players[$value]['position'], $positions) ? true : false;
                }elseif($data['project_id'] == 4){
                    // NBA项目
                    if($key == 6){
                        $positions = array(1,2);
                    }
                    if($key == 7){
                        $positions = array(3,4);
                    }
                    if($key == 8){
                        $positions = array(1,2,3,4,5);
                    }
                    $lineup_check[] = in_array($players[$value]['position'], $positions) ? true : false;
                }
                
            }
        }
        // 如果为电竞项目
        if ($data['project_id'] == 5 || $data['project_id'] == 6) {
            // 判断一个队的人数不能超过4个
            foreach ($team_info as $item) {
                // 不把战队作为选手存入数组中
                if ($players[$item]['position'] == 6) {
                    continue;
                }
                $res[$players[$item]['team_id']][$item] = $item;
            }
            foreach ($res as  $team_num) {
                if (count($team_num) > 4) {
                    $this->returnMsg(10,'room');
                }
            }
        }elseif($data['project_id'] == 4){
            $team_len = count(array_unique($lineup_team)); //所选阵容队伍长度
            if($team_len <= 1){
                if(count($team_info) == 5){
                    $this->returnMsg(10,'room');
                }else{
                    $this->returnMsg(12,'room');
                } 
            }
        }
        //检测工资是否满足配置要求
        if($salary > $lineup_data['pay']){
            $this->returnMsg(6,'room');
        }
        //判断选择的整容位置是否满足配置要求
        foreach ($lineup_check as $key => $value) {
            if($value === false){
                 //echo 12456;die;
                $this->returnMsg(11,'guess');
            }
        }
        // $lineup_info = array();
        // $room_match = $this->getroommatch($id); //获取该房间所有的比赛数据
        // foreach ($room_match as $key => $value) {
        //     foreach ($team_info as $k => $v) {
        //         if(in_array($players[$v]['team_id'], array($value['team_a'],$value['team_b']))){
        //             $lineup_info[$k] = array('match_id',$value['id']);
        //         }
        //     }
        // }

        //全部验证成功,进行投注操作
        //  *$table_name = $this->gettable($id);
        // $lineup_token = md5(json_encode($team_info)); //阵容的md5值,用户判断用户一个阵容对一个房间是否使用多次
        //判断用户是否在该房间使用过相同的阵容
        //  *$UserGuessModule = M($table_name);
        //
        // echo 123465;die;
        $Lineup = M('Lineup');
        $lineup_token = md5(serialize($team_info)); //阵容的md5值

        $lineup_data = $Lineup->where(array('lineup_token' => $lineup_token,'match_id_token' => $data['match_team']))->find(); //获取阵容的信息

        $check_lineup = $UserGuessRecord->where(array('uid' => $uid,'room_id' => $id,'lineup_id' => $lineup_data['id']))->find();
        
        if($check_lineup){ //用户已经投注过该阵容
            if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){
                //主播投注过,不允许再投注了
                $this->returnMsg(5,'guess');
            }
            //添加分表相同阵容投注次数
            $Lineup->where(array('id' => $lineup_data['id']))->setInc('guess_num',$guess_num); 
            //添加主表相同阵容投注次数
            $res = $UserGuessRecord->where(array('lineup_id'=>$lineup_data['id'],'room_id'=>$id))->setInc('guess_num',$guess_num);
            $guess_create_id = $lineup_data['id'];

        }else{ //用户没有投注过该阵容
            // 获取房间的比赛信息
            $result = M('MatchRoomInfo')->where(array('room_id' => $id))->find();
            $match_id_token = $result['match_team'];
            //添加新纪录
            $_data['lineup'] = serialize($team_info);
            $_data['guess_num'] = $guess_num;
            $_data['match_id_token'] = $match_id_token;
            $_data['lineup_token'] = $lineup_token;
            $_data['add_time'] = time();
            $same = $Lineup->where('match_id_token = "'.$match_id_token.'" and lineup_token = "'.$lineup_token.'"')->find();
            if ($same) { //用户选取的新阵容存在
                $Lineup->where(array('id' => $same['id']))->setInc('guess_num',$guess_num);
                $result = $same['id'];
            }else{
                $result = $Lineup->add($_data);
            }
            if(!$result){
                $this->returnMsg(7,'room');
            }
            unset($_data['lineup_token']);
            unset($_data['match_id_token']);
            unset($_data['lineup']);
            $_data['room_id'] = $id;
            $_data['lineup_id'] = $result;
            $_data['uid'] = $this->_user['id'];
            $res = $UserGuessRecord->add($_data); //添加一条记录
            $guess_create_id = $result;
        }

        if(!$res){
            $this->returnMsg(12,'guess'); //参数错误
        }
        //主播投注,不增加房间的投注数目
        if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){

        }else{
            $MatchRoom->where(array('id' => $id))->setInc('now_guess_num',$guess_num);//添加房间投注数
        }
        $guess_check = true;
        if($data['price'] != 0){
            //减少用户的门票数目
            if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){
                $guess_check = true;
            }else{
                $UserUser = M('UserUser');
                $guess_check = $UserUser->where(array('id' => $this->_user['id']))->setDec('entrance_ticket',$data['price'] * $guess_num);
                // 入帐变表
                $this->insert_account(3,1,$this->_user['id'],$data['price'] * $guess_num);
            }
        }
        if($guess_check){
            $match_list = M('MatchRoomInfo')->where(array('room_id' => $id))->find();
            //设置已存阵容
            $this->set_get_lineup($type = 'set',2,$match_list['match_team'],$data['lineup_id'],$this->_user['id'],$team_info);
            $this->returnMsg(0,'guess',$guess_create_id);
        }else{
            $this->returnMsg(1,'guess');
        }
    }
    /**
    * 获取用户的已存阵容和推荐阵容
    */
        public function userlineup(){
        // $uid = $this->_user['id'];
        $id = $this->_data['id']; //房间的id号
        $lineup_id = $this->_data['lineup_id'];//阵容类型 1(5人) 2(8人)
        if(!is_numeric($id)){
            $this->returnMsg(1);
        }
        $MatchRoomInfo = M('MatchRoomInfo');
        $Lineup = M('Lineup');
        $result = $MatchRoomInfo->where(array('room_id' => $id))->find();
        
        if(!$result){
            $this->returnMsg(2,'room');
        }

        $match_array = explode(',', $result['match_team']);
        sort($match_array,SORT_REGULAR);
        $match_list = implode(',', $match_array);
        $Map['match_id_token'] = md5($match_list);

        if(in_array($lineup_id, array(1,2))){
            $Map['lineup_id'] = $lineup_id;
            $MatchPlayer = M('MatchPlayer');
        }else{
            $MatchPlayer = M('MatchPlayerWcg');
        }
        $player_map = $MatchPlayer->getField('id,average');
        $Map['uid'] = $this->_user['id'];
        $UserLineup = M('UserLineup');
        $data = $UserLineup->field('id,lineup_id,type,lineup,join_room_num')->where($Map)->select();
        // echo $UserLineup->getLastSql();die;
        foreach ($data as $key => $value) {
            $data[$key]['lineup'] = unserialize($value['lineup']);
            $sum = 0;
            foreach ($data[$key]['lineup'] as  $v) {
                $sum += $player_map[$v];
            }
            $data[$key]['lineup_score'] =    $sum/10;
        }
        $this->returnMsg(0,'room',$data);
    }
    /**
    * 获取用户的已存阵容个数
    */
    public function getlineupcount(){
        $id = $this->_data['id']; //房间的id号
        if(!is_numeric($id)){
            $this->returnMsg(1);
        }
        $MatchRoomInfo = M('MatchRoomInfo');
        $result = $MatchRoomInfo->where(array('room_id' => $id))->find();
        
        if(!$result){
            $this->returnMsg(2,'room');
        }

        $match_array = explode(',', $result['match_team']);
        sort($match_array,SORT_REGULAR);
        $match_list = implode(',', $match_array);
        $Map['match_id_token'] = md5($match_list);
        $Map['type'] = 2;
        $Map['uid'] = $this->_user['id'];
        $UserLineup = M('UserLineup');
        $count = $UserLineup->where($Map)->getfield('count(id)');
        if (!$count) {
            $count = 0;
        }
        $data['count'] = $count;
        $this->returnMsg(0,'room',$data);
    }
    // 撤销阵容,取消竞猜
    public function revokelineup(){
        $id = $this->_data['id']; // 房间的id
        if(!is_numeric($id)){
            $this->returnMsg(1); //参数错误
        }
        $roomdetail = $this->getroomdetail($id,'less');//获取房间的信息
        if($roomdetail['allow_guess_num'] == $roomdetail['now_guess_num']){ //判断房间的人数是否已满
            $this->returnMsg(3,'guess');
        }
        if(time() >= $roomdetail['match_end_time']){ //判断比赛是否已经开始
            $this->returnMsg(4,'guess');
        }
        $lineup_id = $this->_data['lineup_id']; //所选阵容的di
        if(!is_numeric($lineup_id)){
            $this->returnMsg(1);
        }

        $Map['uid'] = $this->_user['id']; //用户的id
        $Map['room_id'] = $id; //房间id
        $Map['lineup_id'] = $lineup_id; //阵容的id
        $UserGuessRecord = M('UserGuessRecord');
        $result = $UserGuessRecord->where($Map)->find();
        if(!$result){//用户没有的对此房间进行过该阵容投注,返回参数错误
            $this->returnMsg(2,'guess');
        }
        $room_match = M('MatchRoomInfo')->where(array('room_id' => $id))->find();
        //撤销该阵容,返回门票
        $f1 = $UserGuessRecord->where(array('id' => $result['id']))->delete(); //删除分表中的数据
        M('Lineup')->where(array('lineup_id' => $lineup_id,'match_id_token' => $room_match['match_team']))->setDec('guess_num',$result['guess_num']);//减少阵容的投注次数
        $f2 = M('MatchRoom')->where(array('id' => $id))->setDec('now_guess_num',$result['guess_num']); //减少房间的投注数
        if($f1 && $f2){ //返回用户的木头数量
            $check = true;
            if($roomdetail['price'] > 0){
                $check = M('UserUser')->where(array('id' => $this->_user['id']))->setInc('entrance_ticket',$roomdetail['price']*$result['guess_num']);
                $this->insert_account(14,1,$this->_user['id'],$roomdetail['price']*$result['guess_num'],true);
            }
            if($check){
                $this->returnMsg(0,'revoke');
            }else{
                $this->returnMsg(1,'system');
            }
        }else{
            $this->returnMsg(1,'system');
        }
    }
    //修改阵容
    //此接口操作的是比赛没有开始的阵容修改
    public function editlineup(){
        // $id = $this->_data['id']; // 房间的id
        $lineup_id = $this->_data['lineup_id']; //原下注的阵容id
        $team_info = $this->_data['team_info']; //所选阵容,数组
        //获取房间的信息,用于验证,数据的正确性
        $uid = $this->_user['id'];
        $UserGuessRecord = M('UserGuessRecord');
        $room_data = $UserGuessRecord->where(array('lineup_id' => $lineup_id,'uid' => $uid,'match_status' => 1))->find();
        // echo $UserGuessRecord->getLastSql();die;
        $id = $room_data['room_id']; //房间的id
        // echo $id;die;
        if(!is_numeric($id)){
            $this->returnMsg(1);
        }
        // echo 12346;die;
        $Map['status'] = 1; //发布中
        $Map['settlement_status'] = 1; //未结算
        $IMap['room_id'] = $Map['id'] = $id;
        $MatchRoom = M('MatchRoom');
        $data = $MatchRoom->where($Map)->find();
        $data_info = M('MatchRoomInfo')->where($IMap)->find();
        $data['match_team'] = $data_info['match_team'];
        if(!$data){ //房间是否存在
            $this->returnMsg(2,'room');
        }
        if($data['match_end_time'] < time()){ //竞猜是否截止
            $this->returnMsg(3,'room');
        }
        //查询投注的用户是否是主播用户
        if($this->_user['is_special'] == 1){
            if($this->_user['id'] != $data['special_uid']){
                $this->returnMsg(5,'guess'); //主播没有投注指定的房间,返回错误
            }
        }

        $lineup_data = C('MATCH_ROOM_LINEUP')[$data['lineup_id']]; // 本房间阵容的配置
        if(count(array_unique($team_info)) != $lineup_data['num']){ //检测人数是否选择正确
            $this->returnMsg(1);
        }

        $players = $this->getroomplayer($id); // 获取该房间所有的球员
        $players_id = array_keys($players); //房间所有球员的一维数组
        $salary = 0; //所选球员的工资和
        $lineup_check = array();
        foreach ($team_info as $key => $value) { //检测所选阵容是否在房间的选手列表中
            if(!in_array($value, $players_id)){
                $this->returnMsg(1); //检测所选球员是否在房间球员中
            }
            $salary += $players[$value]['salary']; //计算所选球员的工资
            if($key <= 5){ //检测选择位置信息
                $lineup_check[] = $players[$value]['position'] == $key ? true : false;
            }else{
                // 如果为电竞项目
                if ($data['project_id'] == 5 || $data['project_id'] == 6) {
                    if($key == 7){
                        $positions = array(1,2,3,4,5);
                    }
                    if($key == 8){
                        $positions = array(1,2,3,4,5);
                    }
                    if($key == 6){
                        $positions = array(6);
                    }
                    //$res[$players[$value]['team_id']][$value] = $value;
                    $lineup_check[] = in_array($players[$value]['position'], $positions) ? true : false;
                }elseif($data['project_id'] == 4){
                    // NBA项目
                    if($key == 6){
                        $positions = array(1,2);
                    }
                    if($key == 7){
                        $positions = array(3,4);
                    }
                    if($key == 8){
                        $positions = array(1,2,3,4,5);
                    }
                    $lineup_check[] = in_array($players[$value]['position'], $positions) ? true : false;
                }
            }
        }
         // 如果为电竞项目
        if ($data['project_id'] == 5 || $data['project_id'] == 6) {
            // 判断一个队的人数不能超过4个
            foreach ($team_info as $item) {
                // 不把战队作为选手存入数组中
                if ($players[$item]['position'] == 6) {
                    continue;
                }
                $res[$players[$item]['team_id']][$item] = $item;
            }
            foreach ($res as  $team_num) {
                if (count($team_num) > 4) {
                    $this->returnMsg(10,'room');
                }
            }
        }elseif($data['project_id'] == 4){

        }
        
        //检测工资是否满足配置要求
        if($salary > $lineup_data['pay']){
            $this->returnMsg(6,'room');
        }
        // echo json_encode($lineup_check);die;
        //判断选择的整容位置是否满足配置要求
        foreach ($lineup_check as $key => $value) {
            if($value === false){
                $this->returnMsg(1);
            }
        }

        $res = $UserGuessRecord->where(array('lineup_id' => $lineup_id,'uid' => $uid,'match_status' => 1))->count();
        if(!$res){
            $this->returnMsg(1);
        }
        // echo 1233456;die;
        // $lineup_id = $res['lineup_id'];//投注的阵容id
        $user_guess_num = $UserGuessRecord->where(array('lineup_id' => $lineup_id))->sum('guess_num'); //用户原阵容所投注次数
        $Lineup = M('Lineup');
        $lineup_token = md5(serialize($team_info)); //新阵容的md5值
        $lineup_data = $Lineup->where(array('lineup_token' => $lineup_token,'match_id_token' => $data['match_team']))->find(); //获取阵容的信息
        if($lineup_data && $lineup_data['id'] == $lineup_id){
            $this->returnMsg(6,'guess'); //阵容已经存在
        }

        if($lineup_data){ //阵容存在,直接去阵容id
            $result = $UserGuessRecord->where(array('lineup_id' => $lineup_id))->save(array('lineup_id' => $lineup_data['id']));
            //修改阵容投注次数
            $Lineup->where(array('id' => $lineup_data['id']))->setInc('guess_num',$user_guess_num);
        }else{ //阵容不存在,需要重新添加阵容到数据表

            $match_id_token = $data['match_team'];
            //添加新纪录
            $_data['lineup'] = serialize($team_info);
            $_data['guess_num'] = $user_guess_num;
            $_data['match_id_token'] = $match_id_token;
            $_data['lineup_token'] = $lineup_token;
            $_data['add_time'] = time();
            $new_lineup_id = $Lineup->add($_data); //新的阵容产生的id
            if(!$new_lineup_id){
                $this->returnMsg(7,'room');
            }
            $result = $UserGuessRecord->where(array('lineup_id' => $lineup_id))->save(array('lineup_id' => $new_lineup_id));
        }
        if($result){
            $this->returnMsg(0,'editlineup',$res);
        }else{
            $this->returnMsg(1,'editlineup');
        }
    }


    //匹配阵容加注
    public function addguess(){
        $lineup_id = $this->_data['lineup_id']; //阵容的id
        $room_id = $this->_data['room_id']; //房间的id
        $last_room_id = $this->_data['last_room_id']; //投注的上一个房间的房间id
        $guess_num = $this->_data['guess_num']; //下注次数
        if(!is_numeric($room_id)){
            $this->returnMsg(1,'addguess');
        }
        if(!is_numeric($last_room_id)){
            $this->returnMsg(2,'addguess');
        }
        if(!is_numeric($lineup_id)){
            $this->returnMsg(3,'addguess');
        }

        //验证阵容的id和房间号是否匹配
        $data_lineup = M('UserGuessRecord')->where(array('room_id' => $last_room_id,'lineup_id' => $lineup_id))->find();
        if(!$data_lineup){
            $this->returnMsg(4,'addguess'); //投注信息不存在
        }

        //验证新的房间使用
        $room1 = D('RoomAll')->where(array('id' => $room_id))->find();
        $room2 = D('RoomAll')->where(array('id' => $last_room_id))->find();
        if($room1['match_team'] != $room2['match_team']){
            $this->returnMsg(5,'addguess');
        }
        //验证房间的阵容选人配置是否一致
        if($room1['lineup_id'] != $room2['lineup_id']){
            $this->returnMsg(6,'addguess');
        }
        
        //获取阵容的id
        $lineup_info = M('Lineup')->where(array('id' => $lineup_id))->find();
        //进行投注操作
        $id = $room_id; // 房间的id

        $team_info = unserialize($lineup_info['lineup']); //所选阵容,数组
        //获取房间的信息,用于验证,数据的正确性
        // echo json_encode($team_info);die;
        if(!is_numeric($id)){
            $this->returnMsg(7,'guess');// 没有接收到房间id
        }
        if(!is_numeric($guess_num) || $guess_num == 0){
            $this->returnMsg(8,'guess'); // 没有接收到投注数量
        }
        $Map['status'] = 1; //发布中
        $Map['settlement_status'] = 1; //未结算
        $IMap['room_id'] = $Map['id'] = $id;
        $MatchRoom = M('MatchRoom');
        $data = $MatchRoom->where($Map)->find();
        $data_info = M('MatchRoomInfo')->where($IMap)->find();
        $data['match_team'] = $data_info['match_team'];
        if(!$data){ //房间是否存在
            $this->returnMsg(2,'room');
        }
        if($data['match_end_time'] < time()){ //竞猜是否截止
            $this->returnMsg(3,'room');
        }
        //查询投注的用户是否是主播用户
        if($this->_user['is_special'] == 1){
            if($this->_user['id'] != $data['special_uid']){
                $this->returnMsg(5,'guess'); //主播没有投注指定的房间,返回错误
            }
        }
        $UserGuessRecord = M('UserGuessRecord');
        // echo 123456;die;
        //主播投注,不检测满注和用户的门票数目
        if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){
            $specia_num = $user_guess_num = $UserGuessRecord->where(array('uid' => $this->_user['id'],'room_id' =>$id))->sum('guess_num');
            if($specia_num > 1){
                $this->returnMsg(4,'room'); //主播只能投注一次
            }
        }else{
            //检测房间是否已满
            if($data['allow_guess_num'] == $data['now_guess_num']){
                $this->returnMsg(8,'room');
            }

            //检测用户门票是否足够
            if($this->_user['entrance_ticket'] < $data['price'] * $guess_num){
                $this->returnMsg(5,'room');
            }           
        }
        //检测用户是否投注过该房间
        $uid = $this->_user['id'];
        $user_guess_num = $UserGuessRecord->where(array('uid' => $uid,'room_id' =>$id))->sum('guess_num');
        if(($guess_num + $user_guess_num) > $data['allow_uguess_num']){
            $this->returnMsg(4,'room'); //投注达到上限禁止投注
        }

        if(($guess_num+$data['now_guess_num']) > $data['allow_guess_num']){
            $this->returnMsg(4,'room'); //投注达到上限禁止投注
        }

        $lineup_data = C('MATCH_ROOM_LINEUP')[$data['lineup_id']]; // 本房间阵容的配置
        if(count(array_unique($team_info)) != $lineup_data['num']){ //检测人数是否选择正确
            $this->returnMsg(9,'guess');// 投注人数不正确
        }
        $players = $this->getroomplayer($id); // 获取该房间所有的球员
        $players_id = array_keys($players); //房间所有球员的一维数组
        //var_dump($players);
        $salary = 0; //所选球员的工资和
        $lineup_check = array();
        $lineup_team = array(); //存储所选阵容的队伍id
        foreach ($team_info as $key => $value) { //检测所选阵容是否在房间的选手列表中

            $lineup_team[] = $players[$value]['team_id'];//将所选球员的队伍写到数组

            if(!in_array($value, $players_id)){
                //echo 111;die;
                $this->returnMsg(10,'guess'); //检测所选球员是否在房间球员中
            }
            $salary += $players[$value]['salary']; //计算所选球员的工资
            if($key <= 5){ //检测选择位置信息
                $lineup_check[] = $players[$value]['position'] == $key ? true : false;
            }else{
                // 如果为电竞项目
                if ($data['project_id'] == 5 || $data['project_id'] == 6) {
                    if($key == 7){
                        $positions = array(1,2,3,4,5);
                    }
                    if($key == 8){
                        $positions = array(1,2,3,4,5);
                    }
                    if($key == 6){
                        $positions = array(6);
                    }
                    //$res[$players[$value]['team_id']][$value] = $value;
                    $lineup_check[] = in_array($players[$value]['position'], $positions) ? true : false;
                }elseif($data['project_id'] == 4){
                    // NBA项目
                    if($key == 6){
                        $positions = array(1,2);
                    }
                    if($key == 7){
                        $positions = array(3,4);
                    }
                    if($key == 8){
                        $positions = array(1,2,3,4,5);
                    }
                    $lineup_check[] = in_array($players[$value]['position'], $positions) ? true : false;
                }
                
            }
        }
        // 如果为电竞项目
        if ($data['project_id'] == 5 || $data['project_id'] == 6) {
            // 判断一个队的人数不能超过4个
            foreach ($team_info as $item) {
                // 不把战队作为选手存入数组中
                if ($players[$item]['position'] == 6) {
                    continue;
                }
                $res[$players[$item]['team_id']][$item] = $item;
            }
            foreach ($res as  $team_num) {
                if (count($team_num) > 4) {
                    $this->returnMsg(10,'room');
                }
            }
        }elseif($data['project_id'] == 4){
            $team_len = count(array_unique($lineup_team)); //所选阵容队伍长度
            if($team_len <= 1){
                if(count($team_info) == 5){
                    $this->returnMsg(10,'room');
                }else{
                    $this->returnMsg(12,'room');
                } 
            }
        }
        //检测工资是否满足配置要求
        if($salary > $lineup_data['pay']){
            $this->returnMsg(6,'room');
        }
        //判断选择的整容位置是否满足配置要求
        foreach ($lineup_check as $key => $value) {
            if($value === false){
                $this->returnMsg(11,'guess');
            }
        }

        $Lineup = M('Lineup');
        $lineup_token = md5(serialize($team_info)); //阵容的md5值

        $lineup_data = $Lineup->where(array('lineup_token' => $lineup_token,'match_id_token' => $data['match_team']))->find(); //获取阵容的信息

        $check_lineup = $UserGuessRecord->where(array('uid' => $uid,'room_id' => $id,'lineup_id' => $lineup_data['id']))->find();
        
        if($check_lineup){ //用户已经投注过该阵容
            if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){
                //主播投注过,不允许再投注了
                $this->returnMsg(5,'guess');
            }
            //添加分表相同阵容投注次数
            $Lineup->where(array('id' => $lineup_data['id']))->setInc('guess_num',$guess_num); 
            //添加主表相同阵容投注次数
            $res = $UserGuessRecord->where(array('lineup_id'=>$lineup_data['id'],'room_id'=>$id))->setInc('guess_num',$guess_num);
        }else{ //用户没有投注过该阵容
            // 获取房间的比赛信息
            $result = M('MatchRoomInfo')->where(array('room_id' => $id))->find();
            $match_id_token = $result['match_team'];
            //添加新纪录
            $_data['lineup'] = serialize($team_info);
            $_data['guess_num'] = $guess_num;
            $_data['match_id_token'] = $match_id_token;
            $_data['lineup_token'] = $lineup_token;
            $_data['add_time'] = time();
            $same = $Lineup->where('match_id_token = "'.$match_id_token.'" and lineup_token = "'.$lineup_token.'"')->find();
            if ($same) { //用户选取的新阵容存在
                $Lineup->where(array('id' => $same['id']))->setInc('guess_num',$guess_num);
                $result = $same['id'];
            }else{
                $result = $Lineup->add($_data);
            }
            if(!$result){
                $this->returnMsg(7,'room');
            }
            unset($_data['lineup_token']);
            unset($_data['match_id_token']);
            unset($_data['lineup']);
            $_data['room_id'] = $id;
            $_data['lineup_id'] = $result;
            $_data['uid'] = $this->_user['id'];
            $res = $UserGuessRecord->add($_data); //添加一条记录
        }

        if(!$res){
            $this->returnMsg(12,'guess'); //参数错误
        }
        //主播投注,不增加房间的投注数目
        if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){

        }else{
            $MatchRoom->where(array('id' => $id))->setInc('now_guess_num',$guess_num);//添加房间投注数
        }
        $guess_check = true;
        if($data['price'] != 0){
            //减少用户的门票数目
            if($data['is_special'] == 1 && $this->_user['id'] == $data['special_uid'] && $this->_user['is_special'] == 1){
                $guess_check = true;
            }else{
                $UserUser = M('UserUser');
                $guess_check = $UserUser->where(array('id' => $this->_user['id']))->setDec('entrance_ticket',$data['price'] * $guess_num);
                // 入帐变表
                $this->insert_account(3,1,$this->_user['id'],$data['price'] * $guess_num);
            }
        }
        if($guess_check){
            $match_list = M('MatchRoomInfo')->where(array('room_id' => $id))->find();
            //设置已存阵容
            $this->set_get_lineup($type = 'set',2,$match_list['match_team'],$data['lineup_id'],$this->_user['id'],$team_info);
            $this->returnMsg(0,'guess',$team_info, intval($lineup_id));
        }else{
            $this->returnMsg(1,'guess');
        }
    }

}