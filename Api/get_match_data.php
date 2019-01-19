<?php
header('Content-Type:text/html;charset=utf-8');
require ('F:/work/Api/phpQuery.php');
$mysqlData = array(
    'DB_HOST' => '192.168.0.179', // 服务器地址
    'DB_CHARSET'=> 'utf8',
    'DB_NAME' => 'www_aifamu_com_1', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'sgamer', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'fa' // 数据库表前缀
);
$sql = "select id,code from fa_match_team where  project_id = 6 ";
$DB = new DB($mysqlData['DB_HOST'],$mysqlData['DB_USER'],$mysqlData['DB_PWD'],$mysqlData['DB_NAME'],$mysqlData['DB_CHARSET']);
$res = $DB->getArrData($sql);
foreach ($res as $key => $value) {
    $codes[] = $value['code'];
    $map[$value['code']] = $value['id'];
}
foreach ($codes as $key => $value) {
   $html =  curl_request('https://zh.dotabuff.com/esports/teams/'.$value.'/series');
   phpQuery::newDocumentHTML($html);
   $tr = pq('body')->eq(0)->find('tr');
    for ($i=1; $i <60 ; $i++) { 
        $bo = pq($tr)->eq($i)->find('td')->eq(1)->find('div')->eq(0)->text();// bo几
        $td['home_id'][] = substr($bo,0,1); 
        $score= pq($tr)->eq($i)->find('td')->find('.score-large')->html();
        $td['score'][] = str_replace(' – ',':',$score);
        $team_a = pq($tr)->eq($i)->find('.team-1')->find('.r-none-mobile')->eq(0)->find('.esports-link')->attr('href');// 队伍a
        $td['team_a'][] = substr(strrchr($team_a,'/'),1);
        $team_b = pq($tr)->eq($i)->find('.team-2')->find('.r-none-mobile')->eq(0)->find('.esports-link')->attr('href');// 队伍b
        $td['team_b'][] = substr(strrchr($team_b,'/'),1);
        $spans = array();
        for ($j=0; $j <5 ; $j++) {
            $span = pq($tr)->eq($i)->find('.series-game-icons')->find('.complete')->find('a')->eq($j)->attr('title');// 采集数据match_id
            if ($span) {
                $spans[] = substr(strrchr($span,'：'),3);
            }
        }
        $td['only_id'][] = implode(',',$spans);
    }
    $k = 0;
    foreach ($td['home_id'] as $key => $values) {
        if (!$values) {
            continue;
        }else{
            $data[$k]['home_id'] = $values;
            $data[$k]['score'] = $td['score'][$key];
            if (!$td['team_a'][$key]) {
                $team_a = $map[$value];
            }else{
                $team_a = @$map[$td['team_a'][$key]] ;
            }
            if (!$td['team_b'][$key]) {
                $team_b = $map[$value];
            }else{
                $team_b = @$map[$td['team_b'][$key]];
            }
            if (!$team_a || !$team_b) {
                continue;
            }
            $data[$k]['only_id'] = $td['only_id'][$key];
            $data[$k]['team_b'] = $team_a;
            $data[$k]['team_b'] =$team_b;
            $k++;
            $scoress = explode(':',$td['score'][$key]);
            $only_id = $td['only_id'][$key];
            $sql = "select id from fa_match_list where only_id = '$only_id' ";
            $ress = $DB->query($sql);
            if ($ress) {
                continue;
            }else{
                $sqls = "insert into fa_match_list  (`project_id`,`only_id`,`home_id`,`team_a`,`team_b`,`score_a`,`score_b`,`match_status`) values (6,'$only_id',$values,$team_a,$team_b,$scoress[0],$scoress[1],3)";
                $res = $DB->query($sqls);
            }
        }
    }
    file_put_contents('F:/work/Api/match/'.$value.'.txt',json_encode(@$data)); 
}
// function get_match_data($match_id){
//     $content = file_get_contents('https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id='.$match_id.'&key=094A15ACFD6CA245D0FC24A0B6378D96');
//     $data = json_decode($content,true);
//     if ($data == null) {
//         echo $match_id.'采集失败请稍后再试';
//     }
//     echo '<pre>';var_dump($data);
// }
// get_match_data(2905374145);
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
        $result = @$this->connect->query($sql);
        $objRes = mysqli_fetch_array($result,MYSQLI_ASSOC);
        if (!$objRes) {
            return false;
        }
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
function curl_request($url, $data=array(), $method='GET')
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