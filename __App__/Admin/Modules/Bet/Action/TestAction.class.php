<?php
include_once APP_PATH . 'phpexcel/PHPExcel.php';
class TestAction extends AdminAction{
	public function importexcel(){

		$sheet_num = $this->_get('sheet');
		if(!is_numeric($sheet_num)){
			exit('sheet错误,sheet只能为数字');
		}
		$objReader = PHPExcel_IOFactory::createReader('excel2007');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
		$filename = './excel/aifamu.xlsx';
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件

		$sheet = $objPHPExcel->getSheet($sheet_num);
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		if($sheet_num == 0){//新闻
			$this->truncatetable('news'); 
			for($j = 2;$j <= $highestRow;$j++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$j)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$j)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$j)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$j)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$j)->getValue();//获取C列的值
				$f = $sheet->getCell("F".$j)->getValue();//获取C列的值
				if(!$a){
					continue;
				}				
				$data['id'] = $a;
				$data['type_id'] = $b;
				$data['name'] = $c;
				$data['img'] = $d;
				$data['link'] = $e;
				$data['sort'] = $f;
				$data['add_time'] = time();
				// print_r($data);die;
				$result = M('News')->add($data);
			}
		}elseif($sheet_num == 1){ //新闻类型
			$this->truncatetable('news_type');
			for($j = 2;$j <= $highestRow;$j++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$j)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$j)->getValue();//获取B列的值
				if(!$a){
					continue;
				}
				$data['id'] = $a;
				$data['name'] = $b;
				$data['add_time'] = time();
				$result = M('NewsType')->add($data);
			}

		}elseif($sheet_num == 2){//竞猜房间列表
			$this->truncatetable('match_room');
			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$jj)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$jj)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$jj)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$jj)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$jj)->getValue();//获取C列的值
				$f = $sheet->getCell("F".$jj)->getValue();//获取C列的值
				$g = $sheet->getCell("G".$jj)->getValue();//获取C列的值
				$h = $sheet->getCell("H".$jj)->getValue();//获取C列的值
				$i = $sheet->getCell("I".$jj)->getValue();//获取C列的值
				$j = $sheet->getCell("J".$jj)->getValue();//获取C列的值
				$k = $sheet->getCell("K".$jj)->getValue();//获取C列的值
				$l = $sheet->getCell("L".$jj)->getValue();//获取C列的值
				$m = $sheet->getCell("M".$jj)->getValue();//获取C列的值
				$n = $sheet->getCell("N".$jj)->getValue();//获取C列的值
				$o = $sheet->getCell("O".$jj)->getValue();//获取C列的值
				$p = $sheet->getCell("P".$jj)->getValue();//获取C列的值
				$q = $sheet->getCell("Q".$jj)->getValue();//获取C列的值
				$r = $sheet->getCell("R".$jj)->getValue();//获取C列的值
				$s = $sheet->getCell("S".$jj)->getValue();//获取C列的值
				$t = $sheet->getCell("T".$jj)->getValue();//获取C列的值
				if(!$a){continue;}
				$data['id'] = $a;
				$data['type_id'] = $b;
				$data['project_id'] = $c;
				$data['name'] = $d;
				$data['match_start_time'] = time() + 3600*5;
				$data['match_end_time'] = time() + 3600*5;
				$data['is_hot'] = $g;
				$data['status'] = $h;
				$data['settlement_status'] = $i;
				$data['author'] = $j;
				$data['price'] = $k;
				$data['allow_guess_num'] = $l;
				$data['now_guess_num'] = 0;
				$data['allow_uguess_num'] = $m;
				$data['reward_id'] = $o;
				$data['prize_type'] = $p;
				$data['reward_num'] = $q;
				$data['open_id'] = $r;
				$data['open_num'] = $s;
				$data['prize_num'] = $t;
				// print_r($data);die;
				$data['add_time'] = time();
				$result = M('MatchRoom')->add($data);
				$_data['match_team'] = $n; //使用赛程
				$_data['room_id'] = $result;

				$res = M('MatchRoomInfo')->add($_data);
			}
		}elseif($sheet_num == 3){

		}elseif($sheet_num == 4){
			$this->truncatetable('reward_rule');

			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$jj)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$jj)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$jj)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$jj)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$jj)->getValue();//获取C列的值
				$f = $sheet->getCell("F".$jj)->getValue();//获取C列的值
				$g = $sheet->getCell("G".$jj)->getValue();//获取C列的值
				$h = $sheet->getCell("H".$jj)->getValue();//获取C列的值
				$i = $sheet->getCell("I".$jj)->getValue();//获取C列的值
				$j = $sheet->getCell("J".$jj)->getValue();//获取C列的值
				$k = $sheet->getCell("K".$jj)->getValue();//获取C列的值
				$l = $sheet->getCell("L".$jj)->getValue();//获取C列的值
				if(!$a){
					continue;
				}
				$data['id'] = $a;
				$data['name'] = $b;
				$data['type'] = $c;
				if($a >= 3){
					$data['data'] = serialize(array('1'=>$d,'2'=>$e,'3'=>$f,'4-5'=>$g,'6-10'=>$h,'11-20'=>$i,'21-50'=>$j,'51-100'=>$k,'101-200'=>$l));
				}else{
					$data['data'] = '';
				}
				$data['add_time'] = time();
				$result = M('RewardRule')->add($data);
			}
		}elseif($sheet_num == 8){
			$this->truncatetable('match_player');
			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$jj)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$jj)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$jj)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$jj)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$jj)->getValue();//获取C列的值
				$f = $sheet->getCell("F".$jj)->getValue();//获取C列的值
				$g = $sheet->getCell("G".$jj)->getValue();//获取C列的值
				$h = $sheet->getCell("H".$jj)->getValue();//获取C列的值
				$i = $sheet->getCell("I".$jj)->getValue();//获取C列的值
				$j = $sheet->getCell("J".$jj)->getValue();//获取C列的值
				$k = $sheet->getCell("K".$jj)->getValue();//获取C列的值
				$l = $sheet->getCell("L".$jj)->getValue();//获取C列的值
				$m = $sheet->getCell("M".$jj)->getValue();//获取C列的值
				$n = $sheet->getCell("N".$jj)->getValue();//获取C列的值
				$o = $sheet->getCell("O".$jj)->getValue();//获取C列的值
				$p = $sheet->getCell("P".$jj)->getValue();//获取C列的值
				$q = $sheet->getCell("Q".$jj)->getValue();//获取C列的值
				$r = $sheet->getCell("R".$jj)->getValue();//获取C列的值
				if(!$a){continue;}
				$data['id'] = $a;
				$data['only_id'] = $b;
				$data['name'] = $c;
				$data['e_name'] = $d;
				$data['img'] = $e;
				$data['nationality'] = $f;
				$data['birthday_date'] = '1992-10-08';
				$data['number'] = $h;
				$data['position'] = $i;
				$data['height'] = $j;
				$data['weight'] = $k;
				$data['team_id'] = $l;
				$data['join_year'] = $m;
				$data['is_undetermined'] = $n;
				$data['is_illness'] = $o;
				$data['is_ban'] = $p;
				$data['is_out'] = $q;
				$data['add_time'] = time();
				$result = M('MatchPlayer')->add($data);
			}
		}elseif($sheet_num == 9){
			$this->truncatetable('player_match_data');
			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$jj)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$jj)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$jj)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$jj)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$jj)->getValue();//获取C列的值
				$f = $sheet->getCell("F".$jj)->getValue();//获取C列的值
				$g = $sheet->getCell("G".$jj)->getValue();//获取C列的值
				$h = $sheet->getCell("H".$jj)->getValue();//获取C列的值
				$i = $sheet->getCell("I".$jj)->getValue();//获取C列的值
				$j = $sheet->getCell("J".$jj)->getValue();//获取C列的值
				if(!$a){
					continue;
				}
				$data['player_id'] = $a;
				$data['match_id'] = $b;
				$data['play_time'] = intval($c) * 10;
				$data['get_score'] = intval($d) * 10;
				$data['three_point'] = intval($e) * 10;
				$data['backboard'] = intval($f) * 10;
				$data['help_score'] = intval($g) * 10;
				$data['hinder_score'] = intval($h) * 10;
				$data['cover_score'] = intval($i) * 10;
				$data['mistake_score'] = intval($j) * 10;
				$data['add_time'] = time();
				$result = M('PlayerMatchData')->add($data);

			}
		}elseif($sheet_num == 10){
			$this->truncatetable('match_list');
			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$jj)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$jj)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$jj)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$jj)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$jj)->getValue();//获取C列的值
				$f = $sheet->getCell("F".$jj)->getValue();//获取C列的值
				$g = $sheet->getCell("G".$jj)->getValue();//获取C列的值
				$h = $sheet->getCell("H".$jj)->getValue();//获取C列的值
				$i = $sheet->getCell("I".$jj)->getValue();//获取C列的值
				$j = $sheet->getCell("J".$jj)->getValue();//获取C列的值
				$k = $sheet->getCell("K".$jj)->getValue();//获取C列的值
				if(!$a){
					continue;
				}
				$data['id'] = $a;
				$data['project_id'] = $b;
				$data['match_id'] = $c;
				$data['team_a'] = $d;
				$data['team_b'] = $e;
				$data['score_a'] = $f;
				$data['score_a'] = $g;
				$data['match_time'] = time() + 3600*41;
				$data['status'] = $i;
				$data['match_status'] = $j;
				$data['author'] = $k;
				$data['add_time'] = time();
				$result = M('MatchList')->add($data);
			}
		}elseif($sheet_num == 11){
			$this->truncatetable('match_type');
			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$jj)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$jj)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$jj)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$jj)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$jj)->getValue();//获取C列的值
				if(!$a){
					continue;
				}
				$data['id'] = $a;
				$data['project_id'] = $b;
				$data['name'] = $c;
				$data['introduce'] = $d;
				$data['add_time'] = time();
				$result = M('MatchType')->add($data);
			}
			
		}elseif($sheet_num == 12){
			$this->truncatetable('match_team');
			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$a = $sheet->getCell("A".$jj)->getValue();//获取A列的值
				$b = $sheet->getCell("B".$jj)->getValue();//获取B列的值
				$c = $sheet->getCell("C".$jj)->getValue();//获取C列的值
				$d = $sheet->getCell("D".$jj)->getValue();//获取C列的值
				$e = $sheet->getCell("E".$jj)->getValue();//获取C列的值
				$f = $sheet->getCell("F".$jj)->getValue();//获取C列的值
				$g = $sheet->getCell("G".$jj)->getValue();//获取C列的值
				$h = $sheet->getCell("H".$jj)->getValue();//获取C列的值
				$i = $sheet->getCell("I".$jj)->getValue();//获取C列的值
				if(!$a){
					continue;
				}
				$data['id'] = $a;
				$data['name'] = $b;
				$data['e_name'] = $c;
				$data['short_name'] = $d;
				$data['union'] = $e;
				$data['location'] = $f;
				$data['img'] = $g;
				$data['home_court'] = $h;
				$data['project_id'] = $i;
				$data['add_time'] = time();
				$result = M('MatchTeam')->add($data);
			}
		}
	

	}
	//清空表操作
	protected function truncatetable($table){
		$sql = 'truncate ' . $table;
		$mysqlData = array(
		    'DB_HOST' => '192.168.0.179', // 服务器地址
		    'DB_CHARSET'=> 'utf8mb4', 
		    'DB_NAME' => 'www_aifamu_com', // 数据库名
		    'DB_USER' => 'root', // 用户名
		    'DB_PWD' => 'sgamer', // 密码
		    'DB_PORT' => 3306, // 端口
		    'DB_PREFIX' => '' // 数据库表前缀 
		    );

		//创建对象并打开连接，最后一个参数是选择的数据库名称

		$DBK = new DBK($mysqlData['DB_HOST'],$mysqlData['DB_USER'],$mysqlData['DB_PWD'],$mysqlData['DB_NAME'],$mysqlData['DB_CHARSET']);
		$result = $DBK->query($sql);
		if($result){
			echo 'database is success';
		}else{
			echo 'database is error';
		}
		$DBK->closemysql();
	}

	//导入球员的数据
	public function import_player_data(){
		$excel_name = 'player_data.xls'; //需要导入数据的excel文件的名称
		$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
		$filename = './excel/' . $excel_name;
		if(!file_exists($filename)){
			echo 'file does not exist';die; //文件不存在
		}
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件

		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
			$data['id'] = $sheet->getCell("A".$jj)->getValue();//球员的id
			$data['name'] = $sheet->getCell("B".$jj)->getValue();//球员的中文名称
			$data['e_name'] = $sheet->getCell("C".$jj)->getValue();//球员的英文名称
			$data['img'] = $sheet->getCell("D".$jj)->getValue();//头像文件
			$data['birthday_date'] = $sheet->getCell("E".$jj)->getValue();//生日
			$data['number'] = $sheet->getCell("F".$jj)->getValue();//球衣号
			$data['height'] = $sheet->getCell("H".$jj)->getValue();//身高
			$data['weight'] = $sheet->getCell("I".$jj)->getValue();//体重
			$data['team_id'] = $sheet->getCell("J".$jj)->getValue();//所属球队
			$data['join_year'] = $sheet->getCell("K".$jj)->getValue();//选秀年
			$data['position'] = $sheet->getCell("L".$jj)->getValue();//位置
			$data['nationality'] = $sheet->getCell("M".$jj)->getValue();//位置
			$data['only_id'] = $sheet->getCell("N".$jj)->getValue();//官网id
			//转化数据类型
			$data['height'] = intval(substr($data['height'], 0, -2));
			$data['weight'] = substr($data['weight'], 0, -2) * 10;
			// print_r($data);
			$data['add_time'] = time();
			// die;
			M('MatchPlayer1')->add($data);
		}
	}


	//导入球员的数据
	public function import_player_tt(){
		$excel_name = 'ttnba_player.xls'; //需要导入数据的excel文件的名称
		$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
		$filename = './excel/' . $excel_name;
		if(!file_exists($filename)){
			echo 'file does not exist';die; //文件不存在
		}
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件

		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
			$id = $sheet->getCell("A".$jj)->getValue();//球员的id
			$pid = $sheet->getCell("B".$jj)->getValue();//球员的中文名称
			M('MatchPlayer')->where(array('id' => $pid))->setField('only_id',$id);
			// echo M('MatchPlayer')->getLastSql();die;
		}
	}


	//导入球员的比赛数据
	public function import_player_match_data(){
		set_time_limit(0);
		//获取所有的球员
		$players = M('MatchPlayer1')->select();


		foreach ($players as $key => $value) {
			$excel_name = $value['id'] . '.xls'; //需要导入数据的excel文件的名称
			$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
			$filename = './excel/data/data/' . $excel_name;
			if(!file_exists($filename)){
				echo $value['id'] . 'file does not exist .<br />'; //文件不存在
				continue;
			}
			$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件

			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow(); // 取得总行数 
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数

			for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
				$data['match_id'] = $sheet->getCell("A".$jj)->getValue();//获取比赛id
				$data['team_id'] = $sheet->getCell("B".$jj)->getValue();//球员的队伍id
				$data['play_time'] = $sheet->getCell("C".$jj)->getValue();//出场时间
				$data['play_time'] = $data['play_time']*10;
				$data['get_score'] = $sheet->getCell("D".$jj)->getValue();//得分
				$data['backboard'] = $sheet->getCell("E".$jj)->getValue();//篮板
				$data['help_score'] = $sheet->getCell("F".$jj)->getValue();//助攻
				$data['hinder_score'] = $sheet->getCell("G".$jj)->getValue();//抢断
				$data['cover_score'] = $sheet->getCell("H".$jj)->getValue();//盖帽分
				$data['mistake_score'] = $sheet->getCell("I".$jj)->getValue();//失误
				$data['three_point'] = $sheet->getCell("J".$jj)->getValue();//三分
				$data['score'] = $sheet->getCell("K".$jj)->getValue();//积分
				$data['score'] = $data['score'] * 10;
				$data['team_a_id'] = $sheet->getCell("L".$jj)->getValue();//主场id
				$data['team_b_id'] = $sheet->getCell("M".$jj)->getValue();//客场id
				$data['team_a_score'] = $sheet->getCell("N".$jj)->getValue();//主场分数
				$data['team_a_score'] = $sheet->getCell("O".$jj)->getValue();//客场分数
				$data['player_id'] = $value['id'];
				$data['add_time'] = time();
				// print_r($data);die;
				M('PlayerMatchData1')->add($data);
				// die;
			}

		}
	}

	//导入赛事(比赛)的数据
	public function import_match_data(){
		$excel_name = 'match_data.xls'; //需要导入数据的excel文件的名称
		$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
		$filename = './excel/' . $excel_name;
		if(!file_exists($filename)){
			echo 'file does not exist';die; //文件不存在
		}
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件

		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数

		for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
			$b = $sheet->getCell("B".$jj)->getValue();//id
			if($b == 1){
				continue;
			}

			$data['id'] = $sheet->getCell("C".$jj)->getValue();//id
			$data['team_a'] = $sheet->getCell("D".$jj)->getValue();//主队
			$data['team_b'] = $sheet->getCell("E".$jj)->getValue();//客队
			$data['score_a'] = $sheet->getCell("F".$jj)->getValue();//主队分
			$data['score_b'] = $sheet->getCell("G".$jj)->getValue();//客队分
			$data['match_time'] = $sheet->getCell("H".$jj)->getValue();//比赛时间
			$data['status'] = $sheet->getCell("J".$jj)->getValue();//比赛时间
			$data['match_time'] = strtotime($data['match_time']);//
			if($data['status'] == '完赛'){
				$data['match_status'] = 3;
			}else{
				$data['match_status'] = 1;
			}
			$data['add_time'] = time();
			// print_r()
			// print_r($data);
			M('MatchList1')->add($data);
			// die;
			
		}
	}

	//导入赛事(比赛)的数据
	public function import_match_datas(){
		set_time_limit(0);
		$excel_name = $_GET['file']; //需要导入数据的excel文件的名称
		$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
		$filename = './excel/' . $excel_name;
		if(!file_exists($filename)){
			echo 'file does not exist'.$filename;die; //文件不存在
		}
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件

		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数

		$data['match_time'] =  substr($excel_name , 0,-4);//比赛时间
		for($jj = 2;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据
			$data['play_id'] = $sheet->getCell("A".$jj)->getValue();//id
			$data['average'] = $sheet->getCell("D".$jj)->getValue()*10;//平均分
			if($data['play_id'] == 0 || $data['average']==0){
				continue;
			}
			$data['salary'] = $sheet->getCell("E".$jj)->getValue();//工资
			$data['ten_time'] = $sheet->getCell("F".$jj)->getValue()*10;//近十场平均分
			M('MatchAllData')->add($data);
		}
	}


	//关键词导出 k 1
	public function create_keword(){
		header('Content-type:text/html;charset=utf-8');
		$excel_name = 'k7.xlsx'; //需要导入数据的excel文件的名称
		$objReader = PHPExcel_IOFactory::createReader('excel2007');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
		$filename = './excel/' . $excel_name;
		if(!file_exists($filename)){
			echo 'file does not exist';die; //文件不存在
		}
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件
		$f_n = './excel/kewordk7.json';

		$json = file_get_contents($f_n);
		$k_w = json_decode($json,true);
		if(!$k_w){
			$k_w = array();
		}

		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		for($jj = 4;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据

			$k1 = $sheet->getCell("A".$jj)->getValue();//球员的id
			// $k2 = $sheet->getCell("B".$jj)->getValue();//球员的中文名称
			// $k3 = $sheet->getCell("C".$jj)->getValue();//球员的英文名称
			// $k4 = $sheet->getCell("D".$jj)->getValue();//头像文件
			// $k5 = $sheet->getCell("E".$jj)->getValue();//生日
			// $k6 = $sheet->getCell("F".$jj)->getValue();//球衣号
			// $k7 = $sheet->getCell("H".$jj)->getValue();//身高
			// $k8 = $sheet->getCell("I".$jj)->getValue();//体重

			// $k2 = str_replace('"', '', $k2);
			// $k3 = str_replace('"', '', $k3);
			if($k1 != '…' && $k1 != ''){
				$k_w[] = $k1;
			}
			// if($k2 != '…' && $k2 != ''){
			// 	$k_w[] = $k2;
			// }
			// if($k3 != '…' && $k3 != ''){
			// 	$k_w[] = $k3;
			// }
		}

		print_r($k_w);
		file_put_contents($f_n, json_encode($k_w,JSON_UNESCAPED_UNICODE));

	}

	// protected function save_json_file($file_name,$data){
	// 	$this
	// }
	//关键词导出k2
	public function create_kewordk2(){
		header('Content-type:text/html;charset=utf-8');
		$excel_name = 'k3.xls'; //需要导入数据的excel文件的名称
		$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007(支持2013版)|Excel5(支持2003版) for 2007 format
		$filename = './excel/' . $excel_name;
		if(!file_exists($filename)){
			echo 'file does not exist';die; //文件不存在
		}
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件
		$f_n = './excel/kewordk31.json';

		$json = file_get_contents($f_n);
		$k_w = json_decode($json,true);
		if(!$k_w){
			$k_w = array();
		}

		$sheet = $objPHPExcel->getSheet(1);
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		for($jj = 3;$jj <= $highestRow;$jj++){ //导入第一个sheet的数据

			$k_w[] = $sheet->getCell("A".$jj)->getValue();//球员的id
			$k_w[] = $sheet->getCell("B".$jj)->getValue();//球员的中文名称
			$k_w[] = $sheet->getCell("C".$jj)->getValue();//球员的英文名称
			$k_w[] = $sheet->getCell("D".$jj)->getValue();//头像文件
			$k_w[] = $sheet->getCell("E".$jj)->getValue();//生日
			$k_w[] = $sheet->getCell("F".$jj)->getValue();//球衣号
			$k_w[] = $sheet->getCell("H".$jj)->getValue();//身高
			$k_w[] = $sheet->getCell("I".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("J".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("K".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("L".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("M".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("N".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("O".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("P".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("Q".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("R".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("S".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("T".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("U".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("V".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("W".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("X".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("Y".$jj)->getValue();//体重
			$k_w[] = $sheet->getCell("Z".$jj)->getValue();//体重
			// $k26 = $sheet->getCell("I".$jj)->getValue();//体重
			// $k27 = $sheet->getCell("I".$jj)->getValue();//体重
			// $k28 = $sheet->getCell("I".$jj)->getValue();//体重
			// $k29 = $sheet->getCell("I".$jj)->getValue();//体重
			// $k30 = $sheet->getCell("I".$jj)->getValue();//体重

			
		}

		foreach ($k_w as $key => $value) {
			if($value == '' || is_object($value)){
				unset($k_w[$key]);

			}
		}

		print_r(array_values($k_w));
		file_put_contents($f_n, json_encode($k_w,JSON_UNESCAPED_UNICODE));

	}
}


/**
* 数据库连接 数据获取类
*/
class DBK{
	private $connect = null;

	public function __construct($host,$root,$pass,$dbname,$charset){

		if($this->connect == null){
			$mysqli = new mysqli($host,$root,$pass,$dbname);
			if (mysqli_connect_errno()){
				die('Unable to connect!'). mysqli_connect_error() . '数据库连接失败';
			}
			$mysqli->query('set names ' . $charset);
		}
		$this->connect = $mysqli;//书库连接成功,设置静态的变量,防止2次连接
	}
	public function query($sql){
		$result = $this->connect->query($sql);
		return $result;
	}
	//获取多行数据
	public function getArrData($sql){
		$result = $this->connect->query($sql);
		$data = array(); //所有的比赛存放的数组
		while($row = $result->fetch_assoc()){
			$data[] = $row;
		}
		return $data;
	}
	//关闭连接
	public function closemysql(){
		$this->connect->close();
	}




}