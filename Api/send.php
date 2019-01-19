<?php
require ('./phpQuery.php');
	$player_id=$_GET['player_id'] ? $_GET['player_id'] : 100006;
	$content = file_get_contents('https://www.draftkings.com/player/card?id='.$player_id.'&scheduleId=5495987&contestTypeId=26');
	phpQuery::newDocumentHTML($content);
	$player[$player_id]['name']=pq('h1')->eq(0)->text(); // 获取选手名称
	// 判断受否采集到选手
	if(empty($player[$player_id]['name'])){
		echo '<script>location.href="http://127.0.0.3/send.php?player_id='.++$player_id.'"</script>';
		exit;
	}
	if($player[$player_id]['name'] == 'Too Many Requests'){
		echo '<script>location.href="http://127.0.0.3/send.php?player_id='.++$player_id.'"</script>';
		exit;
	}
	$player[$player_id]['team_name'] = trim(pq('h2')->eq(0)->text()); // 获取选手战队
	$player[$player_id]['kill'] = trim(pq('.stat-block-number')->eq(0)->text()); // 平均击杀
	$player[$player_id]['assists'] = trim(pq('.stat-block-number')->eq(1)->text()); // 平均助攻
	$player[$player_id]['death'] = trim(pq('.stat-block-number')->eq(2)->text()); // 平均死亡
	$player[$player_id]['jungle'] = trim(pq('.stat-block-number')->eq(3)->text()); // 平均打野
	$player[$player_id]['scores'] = trim(pq('.stat-block-number')->eq(4)->text()); // 平均积分
	//$last_season_info = pq('tbody:eq(0)')->find(); // 上赛季信息
	$last_match_info = pq('tbody:eq(0)')->find('tr:eq(1)'); // 最后一场信息
	$last_7day_info = pq('tbody:eq(0)')->find('tr:eq(2)'); // 最近7场场信息
	$last_season_info = pq('tbody:eq(0)')->find('tr:eq(3)'); // 上赛季信息
	for ($i=0; $i <7 ; $i++) { 
		$player[$player_id]['last_match_info'][] = trim(pq($last_match_info)->find('td:eq('.$i.')')->text());
	}
	for ($i=0; $i <7 ; $i++) { 
		$player[$player_id]['last_7day_info'][] = trim(pq($last_7day_info)->find('td:eq('.$i.')')->text());
	}
	for ($i=0; $i <7 ; $i++) { 
		$player[$player_id]['last_season_info'][] = trim(pq($last_season_info)->find('td:eq('.$i.')')->text());
	}
	$last_ten_info = pq('tbody:eq(1)')->find('tr'); // 上赛季信息
	for ($i=1; $i <11 ; $i++) { 
		for ($j=0; $j <12 ; $j++) { 
		$player[$player_id]['last_ten_info'][$i][] = trim(pq('tbody:eq(1)')->find('tr:eq('.$i.')')->find('td:eq('.$j.')')->text());
		}
	}
	//$player['info'];
	//var_dump($player);
	file_put_contents('./player/'.$player_id.'.txt',json_encode($player));
	echo '<script>location.href="http://127.0.0.3/send.php?player_id='.++$player_id.'"</script>';

