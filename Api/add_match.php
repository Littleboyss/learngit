<?php
header('Content-type:text/html;charset=utf-8');
error_reporting(0); //屏蔽错误
require ('F:/work/Api/phpQuery.php');
$mysqlData = array(
    //'DB_HOST' => '192.168.0.179', // 服务器地址
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_CHARSET'=> 'utf8',
    'DB_NAME' => 'www_aifamu_com_1', // 数据库名
    'DB_USER' => 'root', // 用户名
    //'DB_PWD' => 'sgamer', // 密码
    'DB_PWD' => 'root', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'fa' // 数据库表前缀
);
$sql = "select id,only_id,home_id,team_a,team_b,score_a,score_b from fa_match_list where  project_id = 6 and id < 11606";
$DB = new DB($mysqlData['DB_HOST'],$mysqlData['DB_USER'],$mysqlData['DB_PWD'],$mysqlData['DB_NAME'],$mysqlData['DB_CHARSET']);
$res = $DB->getArrData($sql);
$sql1 = "select id,only_id,team_id from fa_match_player_wcg where  project_id = 6 ";
$DB = new DB($mysqlData['DB_HOST'],$mysqlData['DB_USER'],$mysqlData['DB_PWD'],$mysqlData['DB_NAME'],$mysqlData['DB_CHARSET']);

$res1 = $DB->getArrData($sql1);
foreach ($res1 as $k2 => $v2) {
    $only_ids[] = $v2['only_id'];
    $map[$v2['only_id']] = $v2['id'];
    $map_team[$v2['id']] = $v2['team_id'];
}
foreach ($res as $key => $value) {
    $only_id = $value['only_id'];
    $match_id = explode(',',$only_id);
    foreach ($match_id as $k => $v) {
        $datas = file_get_contents('https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id='.$v.'&key=094A15ACFD6CA245D0FC24A0B6378D96');
        $data = json_decode($datas,true);
        if ($k == 0) {
            $match_time = $data['result']['start_time']; // 添加比赛开始时间
        }
        $team_a = $map[$data['result']['dire_team_id']];
        $team_b = $map[$data['result']['radiant_team_id']];  
        if ($data['result']['duration']<1800) {
            if ($data['result']['radiant_win']) {
                $match[$k]['team'][$team_b]['is_fast'] = 1;
                $match[$k]['team'][$team_a]['is_fast'] = 0;
            }else{
                $match[$k]['team'][$team_b]['is_fast'] = 0;
                $match[$k]['team'][$team_a]['is_fast'] = 1;
            }
        }
        $match[$key][$k]['team'][$team_a]['first_kill'] = 0;
        $match[$key][$k]['team'][$team_a]['tower'] = substr_count(decbin($data['result']['tower_status_radiant']),'0'); // 先转成二进制，数零的个数
        $match[$key][$k]['team'][$team_b]['tower'] = substr_count(decbin($data['result']['tower_status_dire']),'0');
        $match[$key][$k]['team'][$team_b]['first_kill'] = 0;
        $match[$key][$k]['team'][$team_a]['opp'] = $data['result']['radiant_name'];
        $match[$key][$k]['team'][$team_b]['opp'] = $data['result']['dire_name'];
        $htmls = curl_request('http://www.trackdota.com/data/game/'.$v.'/live.json',array(),'GET',true);
        $kill_data = json_decode($htmls,true);
        foreach ($kill_data['log'] as $keys => $values) {
            if ( $values['action'] == 'kill') {
                $kill[] = $values;
            }
        }
        $first_kill = $kill[0]['account_id'];// 一血击杀者
        foreach ($data['result']['players'] as $k1 => $v1) {
            @$player_id = $map[$v1['account_id']]?$map[$v1['account_id']] : false;
            if (!@$map[$v1['account_id']]) {
                continue;
            }
            if ($v1['player_slot'] < 5) {
                $match[$key][$k]['player'][$player_id]['team_id'] = $team_a;// 队伍id
                $match[$key][$k]['player'][$player_id]['opp'] = $match[$key][$k]['team'][$team_a]['opp'];// 对手
                if($first_kill == $v1['account_id']){
                    $match[$key][$k]['team'][$team_b]['first_kill'] ++;
                }
            }else{
                $match[$key][$k]['player'][$player_id]['team_id'] = $team_b;// 队伍id
                $match[$key][$k]['player'][$player_id]['opp'] = $match[$key][$k]['team'][$team_b]['opp'];// 对手
                if($first_kill == $v1['account_id']){
                    $match[$key][$k]['team'][$team_a]['first_kill'] ++;
                }
            }
            $match[$key][$k]['player'][$player_id]['kills'] = $v1['kills'];    // 击杀
            $match[$key][$k]['player'][$player_id]['deaths'] = $v1['deaths'];  // 死亡
            $match[$key][$k]['player'][$player_id]['assists'] = $v1['assists']; // 助攻
            $match[$key][$k]['player'][$player_id]['last_hits'] = $v1['last_hits']; // 补刀
            $match[$key][$k]['player'][$player_id]['ten_kill'] = 0;
            if ($v1['kills'] >= 10) {
                $match[$key][$k]['player'][$player_id]['ten_kill']++;
            }
            if ($v1['assists'] >= 10) {
                $match[$key][$k]['player'][$player_id]['ten_kill']++;
            }
        }
        $match[$key][$k]['team'][$team_b]['roshan'] = 0;
        $match[$key][$k]['team'][$team_a]['roshan'] = 0;
        $html = curl_request('https://zh.dotabuff.com/matches/'.$v.'/log');
        phpQuery::newDocumentHTML($html);
        $log = pq('.match-log');
        $roshan = pq($log)->find('.roshan')->parent('div')->text();
        $roshan = pq($log)->find('.roshan')->parent('div')->text();
        $ro = explode("\n",$roshan);
        
        foreach ($ro as  $items) {
            if ($items) {
                if ($items == '夜魇 击杀了  肉山。') {
                    $match[$key][$k]['team'][$team_a]['roshan'] ++; 
                }elseif ($items == '天辉 击杀了  肉山。') {
                    $match[$key][$k]['team'][$team_b]['roshan'] ++; 
                }
            }
        }
    }
    $teams = array();   
    foreach ($match[$key] as $k3 => $v3) {
        foreach ($v3['team'] as $k4 => $v4) {
            if ($map_team[$k4] == $value['team_a']) {
                $score = $value['score_a'].':'.$value['score_b'];
            }else{
                $score = $value['score_b'].':'.$value['score_a'];
            }
            if ( ($score == '2:0' && $value['home_id'] != 2 )|| $score == '3:1' ) {
                $remain = 1;
            }elseif($score == '3:0'){
                $remain = 2;
            }else{
                $remain =0;
            }
            $win = array('1:0','2:0','2:1','3:0','3:1','3:2');
            if (in_array($score,$win)) {
                $is_win = 1;
            }else{
                $is_win = 0;
            }
            $teams[$key][$k4]['id'] = $k4;
            $teams[$key][$k4]['first_blood'] += $v4['first_kill'];
            $teams[$key][$k4]['tower'] += $v4['tower'];
            $teams[$key][$k4]['barons'] += $v4['roshan'];
            $teams[$key][$k4]['is_fast'] += $v4['is_fast'];
            $teams[$key][$k4]['remain'] = $remain;
            $teams[$key][$k4]['is_win'] = $is_win;
            $scores = scorerule_dota(6,$teams[$key][$k4]);
            $teams[$key][$k4]['score'] = $score;
            $teams[$key][$k4]['opp'] = $v4['opp'];
            $teams[$key][$k4]['scores'] = $scores*10;
        }
        foreach ($v3['player'] as $k5 => $v5) {
            if ($map_team[$v5['team_id']] == $value['team_a']) {
                $score = $value['score_a'].':'.$value['score_b'];
            }else{
                $score = $value['score_b'].':'.$value['score_a'];
            }
            if ( ($score == '2:0' && $value['home_id'] != 2 )|| $score == '3:1' ) {
                $remain = 1;
            }elseif($score == '3:0'){
                $remain = 2;
            }else{
                $remain =0;
            }
            $player[$key][$k5]['id'] = $k5;
            $player[$key][$k5]['kill'] += $v5['kills'];
            $player[$key][$k5]['death'] += $v5['deaths'];
            $player[$key][$k5]['assists'] += $v5['assists'];
            $player[$key][$k5]['jungle'] += $v5['last_hits'];
            $player[$key][$k5]['ten_kill'] += $v5['ten_kill'];
            $player[$key][$k5]['remain'] = $remain;
            $scores = scorerule_dota(1,$player[$key][$k5]);
            $player[$key][$k5]['scores'] = $scores*10;
            $player[$key][$k5]['opp'] = $v5['opp'];
            $player[$key][$k5]['score'] = $score;
        }    
    }
    $date = date('m/d',$match_time);
    $season = date('Y',$match_time);
    $match_id = $value['id'];
    $addtime = time();
    foreach ($player[$key] as $k6 => $v6) {
        $player_id = $v6['id'];
        $opp = $v6['opp'];
        $score = $v6['score'];
        $kill = $v6['kill'];
        $assists = $v6['assists'];
        $death = $v6['death'];
        $jungle = $v6['jungle'];
        $ten_kill = $v6['ten_kill'];
        $scores = $v6['scores'];
        $remain = $v6['remain'];
        $sql2 = "insert into fa_player_match_data_dota2 (player_id,match_id,date,opp,score,`kill`,assists,death,jungle,ten_kill,scores,remain,season,addtime) values ($player_id,$match_id,'$date','$opp','$score',$kill,$assists,$death,$jungle,$ten_kill,$scores,$remain,'$season',$addtime)";
        $res2 = $DB->query($sql2);
        if ($res2) {
            echo '插入成功';
        }else{
            echo '插入失败';
        }
        unset($sql2);
    }
    foreach ($teams[$key] as $k7 => $v7) {
        $player_id = $v7['id'];
        $opp = $v7['opp'];
        $score = $v7['score'];
        $tower = $v7['tower'];
        $barons = $v7['barons'];
        $first_blood = $v7['first_blood'];
        $is_win = $v7['is_win'];
        $is_fast = $v7['is_fast'];
        $scores = $v7['scores'];
        $remain = $v7['remain'];
        $sql3 = "insert into fa_player_match_data_dota2 (player_id,match_id,date,opp,score,`tower`,barons,first_blood,is_win,is_fast,scores,remain,season,addtime) values ($player_id,$match_id,'$date','$opp','$score',$tower,$barons,$first_blood,$is_win,$is_fast,$scores,$remain,'$season',$addtime)";
        $res3 = $DB->query($sql3);
        if ($res3) {
            echo '插入成功';
        }else{
            echo '插入失败';
        }
        unset($sql3);
    }
    $sql4 = 'UPDATE fa_match_list SET match_time ='.$match_time.' where id = '.$value['id'];// 更新比赛时间
    $DB->query($sql4);
}
function scorerule_dota($position,$player_match_data){
    if ($position == 6) {
        $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>15); //积分规则配置
    }else{
        $socre_rule = array('kill' => 3,'death' => -1,'assists' => 2,'jungle' => 0.02,'ten_kill' => 2,'barons' => 3,'dragons' => 2,'tower' => 1,'first_blood' => 2,'is_win' => 2,'is_fast' => 2,'remain'=>20); //积分规则配置 
    }
    foreach ($player_match_data as $key => $value) {
        $score_sum += $socre_rule[$key] * $value;
    }

    return number_format($score_sum,1);
}
/**
 * 数据库连接 数据获取类
 */
class DB{
    // private $host;
    // private $root;
    // private $pass;
    // private $dbname;
    // private $charset;
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
// 获取英文缩写
function initials($name){ 
  $nword = explode(" ",$name); 
  $new_name ='';
  foreach($nword as $letter){ 
  	if($letter != ''){
    	$new_name .= $letter{0}.''; 
  	}else{
  		continue;
  	}
  } 
  return strtoupper($new_name); 
} 
function curl_request($url, $data=array(), $method='GET',$gzip = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:5.0) Gecko/20110619 Firefox/5.0');
        #curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.117 Safari/537.36');// chrome
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1');// 360
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)');//ie10
        if($gzip) curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 关键在这里
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36 OPR/20.0.1387.91');//opera
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0');
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        } else if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_POST, false);
            $url = $url . '?' . http_build_query($data, '', '&');
        } else {
            // custom method
        }
        //dump($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) != CURLE_OK || $http_code != 200) {
            $response = '';
        }
        curl_close($ch);
        return $response;
    }