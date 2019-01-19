<?php
header('Content-type:text/html;charset=utf-8');
//error_reporting(0); //屏蔽错误
$player_id=@$_GET['player_id'] ? @$_GET['player_id'] : 100008;
$content = file_get_contents('./player/'.$player_id.'.txt');
$player = json_decode($content,true);// 转化为数组
if ($content == '') {
	echo '<script>location.href="http://127.0.0.3/save_player_data.php?player_id='.++$player_id.'"</script>';exit;
}
$mysqlData = array(
    'DB_HOST' => '192.168.0.179', // 服务器地址
    'DB_CHARSET'=> 'utf8',
    'DB_NAME' => 'www_aifamu_com_1', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'sgamer', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'fa' // 数据库表前缀
);
$name = trim($player[$player_id]['name']);// 选手名称
$short_name = initials($name); // 英文缩写
$addtime =time();//添加时间
$DB = new DB($mysqlData['DB_HOST'],$mysqlData['DB_USER'],$mysqlData['DB_PWD'],$mysqlData['DB_NAME'],$mysqlData['DB_CHARSET']);
// 先查出队伍id
$team_names = explode('|',$player[$player_id]['team_name']);
$team_name =trim($team_names[0]); // 队伍名称
$position =substr($team_names[1],-4); // 位置
$sql = "select id from fa_match_team where e_name = '$team_name'";
$res = $DB->getArrData($sql);
if (!$res) {
    echo '<script>location.href="http://127.0.0.3/save_player_data.php?player_id='.++$player_id.'"</script>';exit;
}
$team_id = $res[0]['id']; // 队伍id
$position_array = array('TOP'=>1,'JNG'=>2,'MID'=>3,'ADC'=>4,'SUP'=>5,'TEAM'=>6);
$pos = $position_array[$position];
$nums = str_replace(',','',$player[$player_id]['last_ten_info'][1][11]);// 字符替换
$salary =  ceil($nums*4/1000);
$average =  $player[$player_id]['scores']*10;
$results =  explode('-',$player[$player_id]['kill']);
$result = $results[0].'W-'.$results[1].'L';
$sqls = "insert into fa_match_player_wcg values ($player_id,$team_id,'$name',5,'$name','$salary',' ',' ',$average,0,$pos,'$result',2,2,2,2,0,$addtime)";
$res = $DB->query($sqls);
if($res){
    echo '添加成功';
    echo '<script>location.href="http://127.0.0.3/save_player_data.php?player_id='.++$player_id.'"</script>';exit;
}else{
    echo "添加失败";
}
echo "<pre>";
var_dump($sqls);exit;
$DB->closemysql(); // 关闭连接
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