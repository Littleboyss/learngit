<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php if(($addbg) == "1"): ?>class="addbg"<?php endif; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo (C("SITE_NAME")); ?></title>
<link href="/css/reset.css" rel="stylesheet" type="text/css" />
<link href="/css/zh-cn-system.css" rel="stylesheet" type="text/css" />
<link href="/css/table_form.css" rel="stylesheet" type="text/css" />
<link href="/css/dialog.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript" src="/js/dialog.js"></script>
<link rel="stylesheet" type="text/css" href="/css/style/zh-cn-styles1.css" title="styles1" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="/css/style/zh-cn-styles2.css" title="styles2" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="/css/style/zh-cn-styles3.css" title="styles3" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="/css/style/zh-cn-styles4.css" title="styles4" media="screen" />
<script language="javascript" type="text/javascript" src="/js/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/admin_common.js"></script>
<script language="javascript" type="text/javascript" src="/js/styleswitch.js"></script>
<script language="javascript" type="text/javascript" src="/js/formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="/js/formvalidatorregex.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="/kindeditor/kindeditor-min.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="/kindeditor/lang/zh_CN.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="/kindeditor/kconfig.js" charset="UTF-8"></script>
<script type="text/javascript">
	window.focus();
</script>
<link href="/kindeditor/plugins/code/prettify.css" rel="stylesheet" type="text/css" />
<link href="/kindeditor/themes/default/default.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	html{_overflow-y:scroll}
	table tr td img{
		width: 79px;
	}
</style>
</head>
<body>


<link rel="stylesheet" type="text/css" href="/js/calendar/jscal2.css" />
<link rel="stylesheet" type="text/css" href="/js/calendar/border-radius.css" />
<link rel="stylesheet" type="text/css" href="/js/calendar/win2k.css" />
<script type="text/javascript" src="/js/calendar/calendar.js"></script>
<script type="text/javascript" src="/js/calendar/lang/en.js"></script>
<style type="text/css">
input.iptxt {
    width: 350px;
}

input.sm {
    width: 100px;
}

.sbtn {
    height: 35px;
    line-height: 35px;
    cursor: pointer;
    display: inline-block;
}

.sbtn:hover {
    color: blue;
}

.tRight {
    text-align: right;
}

.tarea {
    width: 345px;
    height: 106px;
}
</style>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>添加房间</em></a>
        <span>|</span>
        <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('add');?>">
            <input name="id" type="hidden" value="<?php echo ($data["id"]); ?>" />
            <table width='98%' border='0' cellpadding='0' cellspacing='0' align="center" class="rtable">
                <tr>
                    <td class="tRight">状态：</td>
                    <td class="tLeft" colspan="3">
                        <input type="radio" name="status" value="2" checked />待发布
                        <input type="radio" name="status" value="1" <?php if($data[status] == 1): ?>checked<?php endif; ?>/>已发布
                    </td>
                </tr>
                <tr>
                    <td class="tRight">是否为热门推荐：</td>
                    <td class="tLeft" colspan="3">
                        <input type="radio" name="is_hot" value="1" checked />是
                        <input type="radio" name="is_hot" value="2" <?php if($data['is_hot'] == 2): ?>checked<?php endif; ?> />否
                    </td>
                </tr>
                <tr>
                    <td class="tRight">分类：</td>
                    <td class="tLeft" colspan="3">
                        <select name="type_id" id="type_id">
                            <?php if(is_array($roomtype)): $i = 0; $__LIST__ = $roomtype;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($i); ?>" <?php if($data['type_id'] == $i): ?>selected<?php endif; ?>><?php echo ($rs); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>
                <tr hidden class="type_id">
                    <td class="tRight">特殊的主播房间用户id：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="special_uid" value="<?php echo ($data['special_uid']); ?>" class="huge iptxt sm" />
                    </td>
                </tr>
                <tr hidden class="type_id">
                    <td class="tRight">特殊的主播房间名称：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="special_name" value="<?php echo ($data['special_name']); ?>" class="huge iptxt sm" />
                    </td>
                </tr>
                <tr hidden class="type_id">
                    <td class="tRight">是否是主播房间：</td>
                    <td class="tLeft" colspan="3">
                        <input type="radio" name="is_special" value="1"  />是
                        <input type="radio" name="is_special" value="2" checked="checked" />否
                    </td>
                </tr>
                <tr>
                    <td class="tRight">项目：</td>
                    <td class="tLeft" colspan="3">
                        <select name="project_id" id="project">
                            <?php if(is_array($project)): $i = 0; $__LIST__ = $project;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$values): $mod = ($i % 2 );++$i;?><option value="<?php echo ($values['id']); ?>" <?php if($data['project_id'] == $values['id']): ?>selected<?php endif; ?>><?php echo ($values['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="tRight">房间图标：</td>
                    <td class="tLeft" colspan="3">
                        <select name="tag_img" id="project">
                        <option value="0">无</option>
                            <?php if(is_array($room_tag)): $i = 0; $__LIST__ = $room_tag;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$values): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($values['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="tRight">房间使用阵容：</td>
                    <td class="tLeft" colspan="3">
                        <select name="lineup_id" id="lineup_id">
                            <option value="0">请选择</option>
                            <?php if(is_array($lineup)): $i = 0; $__LIST__ = $lineup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($rs); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight">奖励类型：</td>
                    <td class="tLeft" colspan="3">
                        <select name="reward_id" id="class_change">
                            <?php if(is_array($reward)): $i = 0; $__LIST__ = $reward;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($rs['id']); ?>" <?php if($data['reward_id'] == $rs['id']): ?>selected<?php endif; ?>><?php echo ($rs['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>
                <tr hidden id="show_prize_num">
                    <td class="tRight">中奖数量：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" placeholder="如果奖励是实物,则此处填写奖金的人数" name="prize_num" value="<?php echo ($data['prize_num']); ?>" class="huge iptxt" />百分比
                    </td>
                </tr>

                <!-- <tr> -->
                <tr hidden id="show_num">
                    <td class="tRight">奖池数量：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="reward_num" value="<?php echo ($data['reward_num']); ?>" class="huge iptxt sm" />
                    </td>
                </tr>

                <tr>
                    <td class="tRight">房间标题：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="name" value="<?php echo ($data["name"]); ?>" class="huge iptxt" />
                    </td>
                </tr>
                <tr>
                    <td class="tRight">允许用户投注次数：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="allow_uguess_num" value="<?php echo ($data["allow_uguess_num"]); ?>" class="huge iptxt sm" />
                    </td>
                </tr>
                <tr>
                    <td class="tRight">该房间允许的投注次数：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="allow_guess_num" value="<?php echo ($data["allow_uguess_num"]); ?>" class="huge iptxt sm" />
                    </td>
                </tr>
                <tr>
                    <td class="tRight">价格：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="price" value="" class="huge iptxt sm" />
                    </td>
                </tr>
                <tr>
                    <td class="tRight">奖品类型：</td>
                    <td class="tLeft" colspan="3">
                        <select name="prize_type">
                            <option value="1">门票</option>
                            <option value="2" <?php if($data[ 'prize_type']==2 ){echo 'selected=""';}?>>木头</option>
                            <option value="4" <?php if($data[ 'prize_type']==4 ){echo 'selected=""';}?>>实物</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="tRight">实物商品id：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="prize_goods_id" value="<?php echo ($data["prize_goods_id"]); ?>" class="huge iptxt sm" />
                    </td>
                </tr>

                <tr>
                    <td class="tRight">是否为必开：</td>
                    <td class="tLeft" colspan="3">
                        <select name="open_id" id="open_id">
                            <option value="1">是</option>
                            <option value="2" <?php if($data[ 'open_id']==2 ){echo 'selected=""';}?>>否</option>
                        </select>
                    </td>
                </tr>
                <tr style="display: none;" id="open_num">
                    <td class="tRight">开房条件：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="open_num" value="<?php echo ($data['open_num']); ?>" class="huge iptxt sm" />
                    </td>
                </tr>
                <tr>
                    <td class="tRight">编辑：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="author" value="<?php echo ($author); ?>" readonly="" class="huge iptxt sm" />
                    </td>
                </tr>
                <tr>
                    <td class="tRight">比赛开始日期：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="match_start_time" value="" class="huge iptxt" id="mstime" />
                        <span id="stime">选择</span>
                    </td>
                </tr>
                <tr>
                    <td class="tRight">比赛截止时间：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="match_end_time" value="" class="huge iptxt" id="abort_date" />
                        <span id="abort_date_trigger">选择</span>
                    </td>
                </tr>

                <tr>
                    <td class="tRight">自动发布时间：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="show_date" value="" class="huge iptxt" id="abort_date_1" />
                        <span id="abort_date_trigger_1">选择</span>
                    </td>
                </tr>

                <tr>
                    <td class="tRight">添加赛程：</td>
                </tr>
                <tbody id="match_list"></tbody>
                <tr>
                    <td class="tRight">结算时间(最后比赛赛事时间加一小时)：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="end_time" value="" class="huge iptxt" id="abort_date1" />
                        <span id="abort_date_trigger1">选择</span>
                    </td>
                </tr>
                <style type="text/css">
                .teampart,
                .perpart {
                    display: none;
                }
                </style>
                <tr id="subtn">
                    <td class="center"></td>
                    <td class="center" colspan="3">
                        <input type="submit" value="保 存" />
                    </td>
                </tr>
            </table>
        </form>
        <br />
        <br />
        <br />
    </div>
</div>
</body>
<script>
Calendar.setup({
    weekNumbers: false,
    inputField: "abort_date_1",
    trigger: "abort_date_trigger_1",
    dateFormat: "%Y-%m-%d",
    showTime: true,
    minuteStep: 1,
    onSelect: function() {
        this.hide();
    }
});

Calendar.setup({
    weekNumbers: false,
    inputField: "abort_date",
    trigger: "abort_date_trigger",
    dateFormat: "%Y-%m-%d %H:%M",
    showTime: true,
    minuteStep: 1,
    onSelect: function() {
        this.hide();
    }
});
Calendar.setup({
    weekNumbers: false,
    inputField: "abort_date1",
    trigger: "abort_date_trigger1",
    dateFormat: "%Y-%m-%d %H:%M",
    showTime: true,
    minuteStep: 1,
    onSelect: function() {
        this.hide();
    }
});
/*$("#open_match").click(function() {
    $( "#showInfo" ).dialog();
});*/



Calendar.setup({
    weekNumbers: false,
    inputField: "mstime",
    trigger: "stime",
    dateFormat: "%Y-%m-%d %H:%M",
    showTime: true,
    minuteStep: 1,
    onSelect: function(cal) {
        var t = cal.date;
        var date = this.selection.get();
        date = Calendar.intToDate(date);
        t.setFullYear(date.getFullYear());
        t.setMonth(date.getMonth());
        t.setDate(date.getDate());
        t.setMinutes(t.getMinutes() - 10);
        date = Calendar.printDate(t, "%Y-%m-%d %H:%M");
        $("#abort_date").val(date);
        this.hide();
    }
});

function show_tr(type) {
    if (type == 5) {
        $('.type_id').show();
    } else {
        $('.type_id').hide();
    }
}

function show_opne(type) {
    if (type == 2) {
        $('#open_num').show();
    } else {
        $('#open_num').hide();
    }
}
var type = $("#type_id").val();
show_tr(type);
var types = $("#open_id").val();
show_opne(types);
$("#type_id").change(function() {
    var type = $("#type_id").val();
    show_tr(type);
})
$("#open_id").change(function() {
    var type = $("#open_id").val();
    show_opne(type);
})

function checktype(a, b) {
    $("." + a).css('display', 'table-row');
    $("." + b).css('display', 'none');
}
$("#class_change").change(function() {
    var type = $("#class_change").val();
    show_prize_num(type);
})

function show_prize_num(type) {
    if (type == 1 || type == 2 || type == 3 || type == 7 || type == 12) {
        $('#show_prize_num').show();
        $('#show_num').show();
    } else {
        $('#show_prize_num').hide();
        $('#show_num').hide();
    }
}
var price = $("#class_change").val();
show_prize_num(price);
$('#match_list').on('click', 'input[name="check_all"]', function(event) {
   var open = $(this).val();
    $('.check_'+open).attr("checked",this.checked); 
});
$('#project').click(function() {
    var open = $(this).val();
    get_match_list(open);
    if (open == 5) {
        // 自动选中lol的阵容
        $('#lineup_id').val(3);
    }else if(open == 4){
        $('#lineup_id').val(2);
    } else if(open == 6){
        $('#lineup_id').val(4);
    }
});
get_match_list(4);
function get_match_list(project_id){
  var html = '';
  $("#player").html();
   $.ajax({
        type:'post',
        url:"<?php echo U( 'get_match_list');?> ",
        data:{'project_id':project_id,
        },
        dataType:'json',
        success:function(e){
            //console.log(e);
            if(e.error == 0){
                $.each(e.match_list,function(index, el) {
                    html += '<tr><td>'+index+' <input type="checkbox" value="'+index+'" name="check_all"></td></tr>';
                    $.each(el,function(i, item) {
                        if (item['id']%3 == 1) {
                            html += '<tr>';
                        }
                        html += '<td class="tRight">'+item['match_time']+'<input class="check_'+index+'" type="checkbox" value="'+item['id']+'" name="match_team[]"><img style="width:15px" src="'+item['a_img']+'">'+item['a_name']+'——'+item['b_name']+'<img style="width:15px" src="'+item['b_img']+'"></td>';
                         if (item['id']%3 == 0) {
                            html += '</tr>';
                        }
                    })
                });
                $('#match_list').html(html); 
            }else{
                $('#match_list').html('');
            }
        }
    });
}
</script>

</html>