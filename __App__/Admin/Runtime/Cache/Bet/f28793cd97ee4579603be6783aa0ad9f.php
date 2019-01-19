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

<style type="text/css">
input.iptxt {
    width: 350px;
}
</style>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>添加比赛数据</em></a>
        <span>|</span>
        <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('add_match_data');?>">
            <table width='98%' border='0' cellpadding='0' cellspacing='0' align="center" class="rtable">
                <tr>
                </tr>
                <tr>
                    <td class="tRight">选择球员：</td>
                    <td class="tLeft" colspan="3">
                        <select name="player_id" id="select_player">
                            <?php if(is_array($playerlist['list'])): $i = 0; $__LIST__ = $playerlist['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($rs["id"]); ?>"><?php echo ($rs["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>
                <tbody id="player"></tbody>
                    

                    <tr>
                        <td class="center "></td>
                        <td class="center " colspan="3 ">
                            <input type="submit" value="保 存 " />
                        </td>
                    </tr>
                </table>
            </form>
            <br /><br /><br />
    </div>
</div>
<script type="text/javascript ">
function get_player_id(player_id){
  var html = '';
  $("#player").html();
  var match_id = <?php echo ($playerlist['match_id']); ?>;
   $.ajax({
            type:'post',
            url:"<?php echo U( 'get_player_data');?> ",
            data:{'player_id':player_id,
                'match_id' : match_id
            },
            dataType:'json',
            success:function(e){
                if (e.position != '6') {
                    html += "<tr>";
                    html += "<td class=\'tRight\'>击杀数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'kill\' value=\'"+e.kill+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                    html += "<tr>";
                    html += "<td class=\'tRight\'>死亡数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'death\' value=\'"+e.death+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                    html += "<tr>";
                    html += "<td class=\'tRight\'>助攻数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'assists\' value=\'"+e.assists+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                    html += "<tr>";
                    html += "<td class=\'tRight\'>补刀数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'jungle\' value=\'"+e.jungle+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                    html += "<tr>";
                    html += "<td class=\'tRight\'>上场场数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'times\' value=\'"+e.times+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>"; 
                    html += "<tr>";
                    html += "<td class=\'tRight\'>是否上场：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'is_join\' value=\'"+e.is_join+"\' class=\'huge iptxt\' />1是2否";
                    html += "</td>";
                    html += "</tr>"; 
                    html += "<tr>";
                    html += "<td class=\'tRight\'>十次击杀或助攻数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'ten_kill\' value=\'"+e.ten_kill+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                }else{
                    html += "<tr>";
                    html += "<td class=\'tRight\'>推塔数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'tower\' value=\'"+e.tower+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                    html += "<tr>";
                    html += "<td class=\'tRight\'>肉山击杀数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'barons\' value=\'"+e.barons+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                    html += "<tr>";
                    html += "<td class=\'tRight\'>获得一血的场数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'first_blood\' value=\'"+e.first_blood+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                    html += "<tr>";
                    html += "<td class=\'tRight\'>30分内赢得比赛的场数：</td>";
                    html += "<td class=\'tLeft\' colspan=\'3\'>";
                    html += "<input type=\'text\' name=\'is_fast\' value=\'"+e.is_fast+"\' class=\'huge iptxt\' />";
                    html += "</td>";
                    html += "</tr>";
                }
                html += "<tr>";
                html += "<td class=\'tRight \'>比赛时间：</td>";
                html += "<td class=\'tLeft \' colspan=\'3 \'>";
                html += "<input  type=\'text \' readonly=\"ture\" name=\'date\' value=\'"+e.date+"\' class=\'huge iptxt\' />";
                html += "</td>";
                html += "</tr>";
                html += "<tr>";
                html += "<td class=\'tRight \'>对手队伍名称：</td>";
                html += "<td class=\'tLeft \' colspan=\'3 \'>";
                html += "<input type=\'text \' readonly=\"ture\" name=\'opp\' value=\'"+e.opponents+" \' class=\'huge iptxt\' />";
                html += "</td>";
                html += "</tr>";
                html += "<tr><td><input type=\"text\" hidden name=\"player_id\" value='"+player_id+"' />";
                html += "<input type=\"text\" hidden name=\"match_id\" value='"+match_id+"' /></td></tr>";
                $("#player").html(html);
            }
        });
   
}
$('#select_player').on('change',function(){
    var player_id = $(this).val();
    get_player_id(player_id)
});
var player_id = $('#select_player').val();
get_player_id(player_id);
</script>
</body></html>