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
<script>
    config.filterMode = false;
    KindEditor.ready(function(K) {
        var editor = K.create('#background',config);
    });

    KindEditor.ready(function(K) {
        var editor =  K.editor(config);
        var uploadbutton = K.uploadbutton({
            button : K('#wqico')[0],
            fieldName : 'imgFile',
            url :  './index.php?m=Index&a=upload',
            afterUpload : function(data) {
                if (data.error === 0) {
                    var url = K.formatUrl(data.url, 'absolute');
                    K('#ico').val(url);
                    // document.getElementById("player_icon").setAttribute("src",url);
                } else {
                    alert(data.message);
                }
            },
            afterError : function(str) {
                alert('自定义错误信息: ' + str);
            }
        });
        uploadbutton.fileBox.change(function(e) {
            uploadbutton.submit();
        });
    });
</script>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <a href='javascript:;' class="on"><em>添加球员</em></a>
    <span>|</span>
    <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('add');?>">
                <table width='98%' border='0' cellpadding='0' cellspacing='0' align="center" class="rtable">
                    
                    <tr>
                        <td class="tRight">ID：</td>
                        <td class="tLeft" colspan="3">
                            <input placeholder="数字" type="text" name="id" value="<?php echo ($data["id"]); ?>" class="sm input-text" />
                        </td>
                        
                    </tr>
                   <tr>
                        <td class="tRight">项目：</td>
                        <td class="tLeft" colspan="3">
                            <select name="project_id">
                                <option value="0">请选择</option>
                                <?php if(is_array($project)): $i = 0; $__LIST__ = $project;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($rs["id"]); ?>" <?php if($rs[id] == $data['project_id']): ?>selected="selected"<?php endif; ?>><?php echo ($rs["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                   <tr>
                        <td class="tRight">球队：</td>
                        <td class="tLeft" colspan="3">
                            <select name="team_id">
                                <?php if(is_array($team)): $i = 0; $__LIST__ = $team;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($key == $data['team_id']): ?>selected="selected"<?php endif; ?>><?php echo ($rs["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="tRight">中文名：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="name" value="" class="sm input-text" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">英文名：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="e_name" value="" class="sm input-text" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">球员图像：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico" name="img" value="" style="margin-top: 3px"> 
                            <input type="button" id="wqico" value="上传图片">
                        </td>
                    </tr>
                   <tr>
                        <td class="tRight">国籍：</td>
                        <td class="tLeft" colspan="3">
                            <select name="nationality">
                                <?php if(is_array($playercountry)): $i = 0; $__LIST__ = $playercountry;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($rs); ?>" <?php if($key == $data['nationality']): ?>selected="selected"<?php endif; ?>><?php echo ($rs); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
  

                    <tr>
                        <td class="tRight">生日：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="birthday_date" value="<?php echo ($data["birthday_date"]); ?>" class="sm input-text" id="mstime" />
                            <span id="stime">选择</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="tRight">号码：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="number" value="<?php echo ($data["number"]); ?>" class="sm input-text" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">位置：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="position" value="1" />控卫　
                            <input type="radio" name="position" value="2" />分卫
                            <input type="radio" name="position" value="3" />小前
                            <input type="radio" name="position" value="4" />大前
                            <input type="radio" name="position" value="5" />中锋
                            <!-- <input type="checkbox" name="position[]" value="6" />后卫
                            <input type="checkbox" name="position[]" value="7" />前锋
                            <input type="checkbox" name="position[]" value="8" />任意 -->　
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">身高：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="height" value="<?php echo ($data["height"]); ?>" class="sm input-text" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">体重：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="weight" value="<?php echo ($data["weight"]); ?>" class="sm input-text" />
                        </td>
                    </tr>

                    <tr>
                        <td class="tRight">选秀年：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="join_year" value="<?php echo ($data["join_year"]); ?>" class="sm input-text" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">平均分：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" placeholder="为35.5则填写355" name="average" value="<?php echo ($data["average"]); ?>" class="sm input-text" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">出场次数：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="play_num" value="<?php echo ($data["appearances"]); ?>" class="sm input-text" />
                        </td>
                    </tr>
                     <tr>
                        <td class="tRight">出场时间：</td>
                        <td class="tLeft" colspan="3">
                            <input placeholder="为35.5则填写355" type="text" name="play_time" value="<?php echo ($data["play_time"]); ?>" class="sm input-text" />
                        </td>
                    </tr>
                     <tr>
                        <td class="tRight">出场待定：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="is_undetermined" value="1" />是
                            <input type="radio" name="is_undetermined" value="2" checked="checked" />否
                        </td>
                    </tr>
                     <tr>
                        <td class="tRight">伤病：</td>
                        <td class="tLeft" colspan="3">
                           <input type="radio" name="is_illness" value="1" />是
                            <input type="radio" name="is_illness" value="2" checked="checked" />否
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">禁赛：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="is_ban" value="1" />是
                            <input type="radio" name="is_ban" value="2" checked="checked" />否
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">离队：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="is_out" value="1" />是
                            <input type="radio" name="is_out" value="2" checked="checked" />否
                        </td>
                    </tr>
                    <tr>
                        <td class="center"></td>
                        <td class="center" colspan="3">
                            <input type="submit" value="保 存" />
                        </td>
                    </tr>
                </table>
            </form>
    </div>
</div>
<script type="text/javascript">
Calendar.setup({
    weekNumbers: false,
    inputField : "mstime",
    trigger    : "stime",
    dateFormat: "%Y-%m-%d",
    // dateFormat: "%Y-%m-%d %H:%M",
    showTime: true,
    minuteStep: 1,
    onSelect : function() {
            this.hide();
        }
});
</script>
</body></html>