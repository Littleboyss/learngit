<?php
return array(
    // TP配置 ------------------------------------------------------------------
    'APP_GROUP_MODE' => 1,
    'APP_GROUP_LIST' => 'Api',
    'DEFAULT_GROUP' => 'Api',
    'DEFAULT_ACTION'=>'creatCharge',
    'URL_MODEL' => 0,
    'TMPL_FILE_DEPR' => '_',
    'TMPL_STRIP_SPACE' => false,//开启这个关闭dubug不报错
    'URL_CASE_INSENSITIVE'=>true,
    // 数据库配置信息
    'DB_TYPE' => 'mysqli', // 数据库类型
    'DB_HOST' => '172.17.149.108', // 服务器地址
    'DB_CHARSET'=> 'utf8mb4', 
    'DB_NAME' => 'www_aifamu_com', // 数据库名
    'DB_NAME2' => 'www_aifamu_com_user', // 数据库名
    'DB_USER' => 'sgamer', // 用户名
    'DB_PWD' => 'Sgamer2017!@#', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'fa_', // 数据库表前缀
    'msgConfig' => include dirname(__FILE__) . '/msgConfig.php', // 引入错误信息提示
    'LOGIN_KEY' => '.#54!23&3,21ag', //后台验证签名和登录验证的key
    'AVATAR_IMG' => 'http://api.aifamu.com/avator/icon.php?id=', //用户的头像地址
    'REG_URL' => 'http://passport1.sgamer.com/Api/reg', //注册的url
    'scale' => 10, //充值的兑换比例
    'SESSION_TYPE' =>'Memcache',
    'MEMCACHE_HOST' =>'172.17.149.106',
    'MEMCACHE_PORT' =>'11211',
    'LOGIN_STR' => 'auth_aifamu', //用户登录的cookie名称
    'HAND_1'=>'header_1',// 木头转盘手气值
    'HAND_2'=>'header_2',// 砖石转盘手气值
    'CACHE_DATA' => array('player_data_all','player_data_lol','room_type_data','room_type_name_all','project_name_all','rank_name_all','team_data','player_data4','player_data5','lineup_recommend_1','lineup_recommend_2','lineup_recommend_3','reward_rule_data','match_type_data','turnplate_bonus','goods_list','rank_name'),//缓存的数据名称 , 设计缓存的名称的时候只能到配置中获取,便于后台更新数据后做缓存清除

    'MATCH_ROOM_LINEUP' => array( 
        1 => array(
            'num' => 5,'pay' => 125,'position' => array(1 => '控卫',2 => '分卫',3 =>'小前',4=>'大前',5=>'中锋')
            ),
        2 => array(
            'num' => 8,'pay' => 200,'position'=>array(1=>'控卫',2=>'分卫',3 =>'小前',4=>'大前',5=>'中锋',6=>'后卫',7=>'前锋',8=>'任意')
            ),
        3 => array(
            'num' => 8,'pay' => 200,'position' => array(1=>'上单',2=>'打野',3=>'中单',4=>'ADC',5=>'辅助',6=>'团队',7=>'替补',8=>'替补')
            )
        ),

    'ROOM_OPEN_RULE' => array(1 => '必开',2 => '满%s人开'),//开房间标签
    'REWARD_RULE_TAG' => array(1 => '前%s|各得',2 => '前%s|均分',3 =>'获胜均分',4 =>'前200有奖',5 =>'前10%有奖',6=>'前100有奖'),
    'PRIZE_TYPE' => array(1 => '门票',2 =>'木头'),
    'RANK_NAME' => array(1 => '出入篮球',2 => '再创辉煌'), // 所有称号,暂时写在此处,称号过多,则写在数据库
    'TEAM_LOCATION' => array(1 => '东南赛区', 2 => '中部赛区',3 => '大西洋赛区',4 => '太平洋赛区',5 => '西北赛区',6 => '西南赛区',7 => '美国纽约布鲁克林区'),
    'TEAM_UNION' => array(1 => '东部',2 => '西部'),
    'USER_MONEY' => array('entrance_ticket' => 100,'diamond' => 100,'gold'=>100),
);