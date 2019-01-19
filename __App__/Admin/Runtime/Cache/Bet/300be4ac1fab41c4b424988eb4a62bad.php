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


<link rel="stylesheet" type="text/css" href="/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="/js/calendar/win2k.css"/>
<script type="text/javascript" src="/js/calendar/calendar.js"></script>
<script type="text/javascript" src="/js/calendar/lang/en.js"></script>
<style type="text/css">
input.iptxt{
    width: 350px;
}
</style>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <a href='javascript:;' class="on"><em>修改赛程</em></a>
    <span>|</span>
    <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('edit');?>">
                <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>" />
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">

                    <tr>
                        <td class="tRight">所属赛事：</td>
                        <td class="tLeft" colspan="3">
                            <select name="match_name_id" id="select_project">
                                <option value="0">请选择</option>
                                <?php if(is_array($match_type)): $i = 0; $__LIST__ = $match_type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"  <?php if($key == $data[match_name_id]): ?>selected="selected"<?php endif; ?>><?php echo ($rs["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>

                    <!-- <tr>
                        <td class="tRight">赛事名称：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="name" value="<?php echo ($data["name"]); ?>" class="huge iptxt" />
                        </td>
                    </tr> -->

                    <tr>
                        <td class="tRight">主队得分：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="score_a" value="<?php echo ($data["score_a"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>

                    <tr>
                        <td class="tRight">客队得分：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="score_b" value="<?php echo ($data["score_b"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">自定义属性：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="match_status" value="1" <?php if($data[match_status] == 1): ?>checked="checked"<?php endif; ?>>未开始
　                          <input type="radio" name="match_status" value="2" <?php if($data[match_status] == 2): ?>checked="checked"<?php endif; ?>>比赛中
                            <input type="radio" name="match_status" value="3" <?php if($data[match_status] == 3): ?>checked="checked"<?php endif; ?>>已结束
　                      </td>
                    </tr>
                    <tr>
                        <td class="tRight">主队：</td>
                        <td class="tLeft" colspan="3">
                            <select name="team_a" class="team_numbers">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">客队：</td>
                        <td class="tLeft" colspan="3">
                            <select name="team_b" class="team_numbers">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">比赛地址：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="match_address" value="<?php echo ($data["match_address"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>
                    <?php  $only_ids = explode(',',$data['only_id']); foreach($only_ids as $rs){ echo '<tr>'; echo '    <td class="tRight">采集数据的唯一id：</td>'; echo '    <td class="tLeft" colspan="3">'; echo '        <input type="text" name="only_id[]" value="'.$rs.'" class="huge iptxt" />'; echo '        <input type="button" value="+" class="clone">'; echo '    </td>'; echo '</tr>'; } ?>
                    <tr>
                        <td class="tRight">是否是BO2：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="home_id" value="0" checked="checked" />否
                            <input type="radio" name="home_id" value="2" <?php if($data[home_id] == 2): ?>checked="checked"<?php endif; ?> />是
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">比赛时间：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="match_time" value="<?php echo (date("Y-m-d H:i:s",$data["match_time"])); ?>" class="sm input-text" id="mstime" />
                            <span id="stime">选择</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="center"></td>
                        <td class="center" colspan="3">
                        <input type="text" name="project_id" value="6" hidden="">
                            <input type="submit" value="保 存" />
                        </td>
                    </tr>
                </table>
            </form>
            <br /><br /><br />
    </div>
</div>
<script type="text/javascript">
$(function(){
var team_a = <?php echo ($data[team_a]); ?>;  
var team_b = <?php echo ($data[team_b]); ?>;  
        var html = '';
        $.ajax({
            type:'post',
            url:"<?php echo U('getteamjson');?>",
            data:{'project_id':6},
            dataType:'json',
            success:function(e){
                $.each(e,function(i, j) {
                    html += '<option value="' + j['id'] + '" ';
                    if (j['id'] == team_a) {
                        html += 'selected="selected"';
                    }
                    html += '>' + j['name'] + '</option>';
                });
                    $('.team_numbers').eq(0).html(html);
                html ='';
                $.each(e,function(i, j) {
                    html += '<option value="' + j['id'] + '" ';
                    if (j['id'] == team_b) {
                        html += 'selected="selected"';
                    }
                    html += '>' + j['name'] + '</option>';
                });
                $('.team_numbers').eq(1).html(html);
                if(e == null){
                    $('.team_numbers').html('');
                }
            }
        });

});
$('.clone').click(function(){
    var tr = $(this).parent().parent();
    tr.after(tr.clone(true));
})
Calendar.setup({
    weekNumbers: false,
    inputField : "mstime",
    trigger    : "stime",
    // dateFormat: "%Y-%m-%d",
    dateFormat: "%Y-%m-%d %H:%M",
    showTime: true,
    minuteStep: 1,
    onSelect : function() {
            this.hide();
        }
});
</script>
</body></html>