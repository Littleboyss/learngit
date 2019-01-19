<?php
return array(
    'Public' => array(
        array(
            'name' => '首页',
            'sub' => array(
                array(
                    'name' => '首页',
                    'url' => U('Index/main')
                ),
            ),
            'home' => U('Index/main')
        ),
    ),
    'Admin' => array(
        array(
            'name' => '管理员管理',
            'sub' => array(
                array(
                    'name' => '管理员列表',
                    'url' => U('Adminuser/index')
                ),
            ),
        ),
        array(
            'name' => '系统操作',
            'sub' => array(
                array(
                    'name' => '缓存清理',
                    'url' => U('Admin/System/cacheindex')
                ),
            ),
        ),
        array(
            'name' => '系统数据配置',
            'sub' => array(
                array(
                    'name' => '竞猜玩法列表',
                    'url' => U('Admin/Config/betconfindex')
                ),
            ),
        )
    ),
    'User' => array(
        array(
            'name' => '用户管理',
            'sub' => array(
                array(
                    'name' => '用户列表',
                    'url' => U('User/User/index')
                ),
                array(
                    'name' => '公告',
                    'url' => U('User/Notice/index')
                ),
                array(
                    'name' => 'TEST用户',
                    'url' => U('User/User/testuser')
                ),
            )
        ),
    ),
    'Bet' => array(
        array(
            'name' => '房间管理',
            'sub' => array(
                array(
                    'name' => '房间列表',
                    'url' => U('Bet/Bet/index')
                ),
                array(
                    'name' => '手动更新比赛',
                    'url' => U('Bet/Bet/updateroom')
                ),
                array(
                    'name' => '更新每日最佳阵容',
                    'url' => U('Bet/Bet/updatetop')
                ),
                array(
                    'name' => '自动投注比赛',
                    'url' => U('Bet/Bet/guess_auto')
                ),
            )
        ),
        array(
            'name' => '数据管理',
            'sub' => array(
                array(
                    'name' => '房间类型管理',
                    'url' => U('Bet/Room/index')
                ),
                array(
                    'name' => '项目管理',
                    'url' => U('Bet/Project/index')
                ),
                array(
                    'name' => '队伍管理',
                    'url' => U('Bet/Team/index')
                ),
                array(
                    'name' => '球员管理',
                    'url' => U('Bet/Player/index')
                ),
                array(
                    'name' => '选手管理-电竞',
                    'url' => U('Bet/WCGPlayer/index')
                ),
                array(
                    'name' => 'NBA赛程管理',
                    'url' => U('Bet/Match/index')
                ),
                array(
                    'name' => 'LOL赛程管理',
                    'url' => U('Bet/LOLMatch/index')
                ),
                array(
                    'name' => 'DOTA2赛程管理',
                    'url' => U('Bet/DOTA2Match/index')
                ),
                array(
                    'name' => '赛事管理',
                    'url' => U('Bet/Match/match')
                ),
                array(
                    'name' => '球员数据',
                    'url' => U('Bet/Player/data')
                ),
                array(
                    'name' => '球员相关信息',
                    'url' => U('Bet/Player/news')
                )
            )
        ),
        array(
            'name' => '冠军猜管理',
            'sub' => array(
                array(
                    'name' => '冠军猜管理',
                    'url' => U('Bet/Champion/index')
                ),
                array(
                    'name' => '淘汰记录',
                    'url' => U('Bet/Champion/endindex')
                ),
                array(
                    'name' => '日PV统计',
                    'url' => U('Bet/ChampionCount/pvcount')
                ),
                array(
                    'name' => '分享投注统计',
                    'url' => U('Bet/ChampionCount/sharecount')
                ), 
            )
        ),
        array(
            'name' => '其他',
            'sub' => array(
                array(
                    'name' => '幻灯管理',
                    'url' => U('Bet/Slide/index')
                )
            )
        ), 
    ),
    'Gift' => array(
        array(
            'name' => '礼品管理',
            'sub' => array(
                array(
                    'name' => '礼品列表',
                    'url' => U('Gift/Exchange/index')
                ),
                array(
                    'name' => '礼品分类',
                    'url' => U('Gift/Sub/index')
                ),
                array(
                    'name' => '订单管理',
                    'url' => U('Gift/Order/index')
                ),
                array(
                    'name' => '实物房间奖品领取',
                    'url' => U('Gift/Order/get_goods_room')
                ),
            )
        ),
        array(
            'name' => '领奖管理',
            'sub' => array(
                array(
                    'name' => '激活码管理',
                    'url' => U('Gift/Award/redcode_list')
                ),
                array(
                    'name' => '奖品管理',
                    'url' => U('Gift/Award/turnplate_list')
                ),
            )
        ),
        array(
            'name' => '称号管理',
            'sub' => array(
                array(
                    'name' => '称号管理',
                    'url' => U('Gift/Rank/index')
                ),
                array(
                    'name' => '称号分类',
                    'url' => U('Gift/Rank/class_list')
                ),
            )
        ),
        array(
            'name' => '客服',
            'sub' => array(
                array(
                    'name' => '玩家反馈',
                    'url' => U('Gift/Customer/index')
                )
            ),
        ),
    ),

    'Count' => array(
        array(
            'name' => '消费分析',
            'sub' => array(
                array(
                    'name' => '虚拟币来源',
                    'url' => U('Count/Currency/from')
                ),
                array(
                    'name' => '消费消耗',
                    'url' => U('Count/Currency/consume')
                ),
            ),
        ),
        array(
            'name' => '付费分析',
            'sub' => array(
                array(
                    'name' => '付费排行',
                    'url' => U('Count/Pay/top')
                ),
                array(
                    'name' => '付费趋势',
                    'url' => U('Count/Pay/trend')
                ),
                array(
                    'name' => '付费转化',
                    'url' => U('Live/Hero/index')
                ),
                array(
                    'name' => '付费渗透',
                    'url' => U('Live/Hero/index')
                ),
                array(
                    'name' => '付费习惯',
                    'url' => U('Live/Hero/index')
                ),
                array(
                    'name' => '付费间隔',
                    'url' => U('Live/Hero/index')
                ),
            )
        ),
        array(
            'name' => '用户统计',
            'sub' => array(
                array(
                    'name' => '总体概况',
                    'url' => U('Count/User/startcount')
                ),
    
            )
        )
    ),
);
