<?php

return array(
    // TP配置 ------------------------------------------------------------------
    'APP_GROUP_MODE' => 1,
    'APP_GROUP_LIST' => 'Admin',
    'DEFAULT_GROUP' => 'Admin',
    'URL_MODEL' => 0,
    'TMPL_FILE_DEPR' => '_',
    'TMPL_STRIP_SPACE' => false,//开启这个关闭dubug不报错
    // 数据库配置信息
    'DB_TYPE' => 'mysqli', // 数据库类型
    'DB_HOST' => '192.168.0.179', // 服务器地址
    'DB_CHARSET'=> 'utf8', 
    'DB_NAME' => 'www_aifamu_com', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'sgamer', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'fa_', // 数据库表前缀
    // 项目配置 ----------------------------------------------------------------
    'SITE_KEY' => 'com.aifamu.open',//页游开放平台的加密字符串
    'SITE_NAME' => '伐木GMS',
    'CMS_URL' => 'http://work.sgamer.com', // 线上CMS系统地址
    'MODULES' => array(
        'Admin' => '系统管理',
    	'User' => '注册用户',
    	'Bet' => '博彩系统',
    	'Gift' => '礼品商城',
        'Count' => '数据统计',
    ),
    //缓存的数据
    'CACHE_DATA' => array('project_data','playercountry_data','team_data','room_type','match_type_data'),
    'IMG_DOMAIN' => 'http://static.aifamu.com', // 图片域名
    'TMPL_ACTION_ERROR' => TMPL_PATH . 'success.html',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => TMPL_PATH . 'success.html',
    'APP_LIST' => array(
        '后台' => 'http://work.aifamu.com/index.php?g=Admin&m=Public&a=clearcacheall',
        'app' => 'http://api.aifamu.com/index.php?g=api&m=public&a=clearcache',
        'pc' => ''
    ),
    'TEAM_LOCATION' => array(1 => '东南赛区', 2 => '中部赛区',3 => '大西洋赛区',4 => '太平洋赛区',5 => '西北赛区',6 => '西南赛区',7 => '美国纽约布鲁克林区'),
    'TEAM_UNION' => array(1 => '东部',2 => '西部'),
    'MATCH_ROOM_LINEUP' => array( 
        1 => 'NBA5人',
        2 => 'NBA8人',
        3 => 'LOL8人',
        4 => 'Dota2_8人' 
    ),
    'ROOM_TAG' => array(
        1 => array('tag_img' => 'new.png','name' => '新手房'),
        2 => array('tag_img' => 'act.png','name' => '活动房'),
        3 => array('tag_img' => 'lol.png','name' => 'LOL房'),
        4 => array('tag_img' => 'dota2.png','name' => 'DOTA2房'),
        5 => array('tag_img' => 'nba5.png','name' => 'NBA5人'),
        6 => array('tag_img' => 'nba8.png','name' => 'NBA8人'),
        7 => array('tag_img' => 'z.png','name' => '主播房')
    ),
);
