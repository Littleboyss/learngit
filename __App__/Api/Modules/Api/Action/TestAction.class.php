<?php
ini_set('max_execution_time', '0');
set_time_limit(0);
/**
 * 短信接口
 * @author chengy 2017.04.10
 */
class TestAction extends Action{

	private static $f = './lineup_json_data.json';

	public function test(){

        $data = M('RewardRule')->select();
        foreach ($data as $key => $value) {
            if($value['data'] != ''){
                $data[$key]['data'] = unserialize($value['data']);
            }
        }
        print_r($data);
		echo '<h1>123</h1>';
	}

    public function make_lineup_json_data(){
        exit('活动已经结束');
        $match_start_time = '2017-08-02';
        $match_end_time = '2017-08-12';
        $ChampionLineup = M('ChampionLineup');

        //将今天的阵容生成json数据,按日期存储
        $file = self::$f;
        $json = file_get_contents($file);
        $now_date = intval(date('j')) ;
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
        // echo $now_num;
        // die;
        $data = json_decode($json,true) ? json_decode($json,true) : array();
        $lineup_data = $ChampionLineup->select();
        // print_r($data);die;
        // $_data = array();
        for($i = 1;$i <= 11;$i++){
            if($now_num == $i){
                foreach ($lineup_data as $key => $value) {
                        //阵容数据已经记录时不需要再次记录
                        $data[$i][$value['id']] = $value['lineup_score'] / 10;
                        // $_data[$i][$value['id']] = 0;
                }
            }else{
                foreach ($lineup_data as $key => $value) {
                    if(!$data[$i][$value['id']]){
                        $data[$i][$value['id']] = 0;
                    }
                }
            }
        }
        // print_r($data);
        file_put_contents($file, json_encode($data));
    }
    public function get_lineup_change(){
    	$lineup_id = 15;
    	$file = self::$f;
    	$json = file_get_contents($file);
		$data = json_decode($json,true);
		$l = array();
		$d = array();
		foreach ($data as $key => $value) {
			$l[] = $value[$lineup_id];
			$d[] = $key;
		}
        return array('date' => $d,'score' => $l);
    }

    //更新冠军猜的排名
    public function update_lineup_ranking(){
        exit('活动已经结束');
        $UserChampion = M('UserChampion');
        $data = $UserChampion->where(array('champion_id' => 1))->select();
        $RedPacketRecord = M('RedPacketRecord');
        foreach ($data as $key => $value) {
            $sum = $RedPacketRecord->where('g_uid='.$value['uid'])->sum('score');
            $sum = $value['lineup_score']*10 + $sum;
            $UserChampion->where('id='.$value['id'])->setField('count_score',$sum);
        }
        sleep(2);
        $_data = $UserChampion->where(array('champion_id' => 1))->order('count_score desc,add_time asc')->select();
        foreach ($_data as $key => $value) {
            $UserChampion->where('id='.$value['id'])->setField('ranking',$key+1);
        }
    }

    public function test_wx(){
        $Model = A('Wx');
        $res = $Model->sendmessage(6,153);
        var_dump($res);
    }

}