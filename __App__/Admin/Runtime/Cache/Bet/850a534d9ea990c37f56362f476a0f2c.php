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
    <a href='javascript:;' class="on"><em>修改房间</em></a>
    <span>|</span>
    <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('edit');?>">
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">
                    <tr>
                        <td class="tRight">状态：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="status" value="2" checked />待发布
                            <input type="radio" name="status" value="1" <?php if($data[status] == 1): ?>checked<?php endif; ?>/>已发布
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">名称：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="name" value="<?php echo ($data["name"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">所属赛事：</td>
                        <td class="tLeft" colspan="3">
                            <select name="match_id" id="select_project">
                                <option value="0">请选择</option>
                                <?php if(is_array($match_type)): $i = 0; $__LIST__ = $match_type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($data['match_id'] == $key): ?>selected<?php endif; ?>><?php echo ($rs["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
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
                        <td class="tRight">编辑：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="author" value="<?php echo ($author); ?>" readonly="" class="huge iptxt sm" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">图像：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico" name="img" value="<?php echo ($data["img"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico" value="上传图片">
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">简介：</td>
                        <td class="tLeft" colspan="3">
                            <textarea name="introduce" class="tarea"><?php echo ($data["introduce"]); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">比赛开始日期：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="match_start_time" value="<?php echo (date('Y-m-d H:i:s',$data["match_start_time"])); ?>" class="huge iptxt" id="mstime" />
                            <span id="stime">选择</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">比赛截止时间：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="match_end_time" value="<?php echo (date('Y-m-d H:i:s',$data["match_end_time"])); ?>" class="huge iptxt" id="abort_date" />
                            <span id="abort_date_trigger">选择</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">结算时间：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="bet_end_time" value="<?php echo (date('Y-m-d H:i:s',$data["bet_end_time"])); ?>" class="huge iptxt" id="abort_date1" />
                            <span id="abort_date_trigger1">选择</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="center"></td>
                        <td class="center" colspan="3">
                            <input type="text" hidden="" name="id" value="<?php echo ($data['id']); ?>">
                            <input type="submit" value="保 存" />
                        </td>
                    </tr>
                </table>
            </form>
            <br /><br /><br />
    </div>
</div>
<script>
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
</script>

</body></html>