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
    <a href='javascript:;' class="on"><em>添加赛程</em></a>
    <span>|</span>
    <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('add');?>">
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">


                    <tr>
                        <td class="tRight">所属赛事：</td>
                        <td class="tLeft" colspan="3">
                            <select name="match_name_id" id="select_project">
                                <option value="0">请选择</option>
                                <?php if(is_array($match_type)): $i = 0; $__LIST__ = $match_type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" ><?php echo ($rs["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
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
                        <td class="tRight">采集数据的唯一id：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="only_id" value="<?php echo ($data["only_id"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>

                    <tr>
                        <td class="tRight">比赛时间：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="match_time" value="" class="sm input-text" id="mstime" />
                            <span id="stime">选择</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="center"></td>
                        <td class="center" colspan="3">
                        <input type="text" name="project_id" value="4" hidden="">
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

        var html = '';
        $.ajax({
            type:'post',
            url:"<?php echo U('getteamjson');?>",
            data:{'project_id':4},
            dataType:'json',
            success:function(e){
                $.each(e,function(i, j) {
                    html += '<option value="' + i + '">' + j + '</option>';
                    $('.team_numbers').html(html);
                });
                if(e == null){
                    $('.team_numbers').html('');
                }
            }
        });

});
</script>
<script type="text/javascript">
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