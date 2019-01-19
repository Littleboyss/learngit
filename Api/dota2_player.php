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

$sql1 = "select id,only_id,team_id from fa_match_player_wcg where  project_id = 6 ";
$DB = new DB($mysqlData['DB_HOST'],$mysqlData['DB_USER'],$mysqlData['DB_PWD'],$mysqlData['DB_NAME'],$mysqlData['DB_CHARSET']);

$res1 = $DB->getArrData($sql1);
foreach ($res1 as $k2 => $v2) {
    $datas = file_get_contents('http://api.steampowered.com/IDOTA2Match_570/GetTournamentPlayerStats/v1?account_id='.$v2['only_id'].'&league_id=65006&key=094A15ACFD6CA245D0FC24A0B6378D96');
    if (!$datas) {
    	echo "未获取到数据";
    }
    $data = json_decode($datas,true);
    $kda = ($data['result']['kills_average'] +$data['result']['assists_average'])/ $data['result']['deaths_average'];
	$v6['kill'] =$data['result']['kills_average'];
	$v6['assists']=$data['result']['assists_average'];
	$v6['death']=$data['result']['deaths_average'];
	$v6['jungle']=$data['result']['last_hits_average'];
	$kda = round($kda*10);
	$scores = scorerule_dota($v2['position'],$v6)*10;
    $sql2 = "UPDATE fa_match_player_wcg  SET KDA = $kda,average = $scores where  id = ".$v2['id'];
    $res = $DB->query($sql2);
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