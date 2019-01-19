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
    <a href='javascript:;' class="on"><em>修改公告</em></a>
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
                        <td class="tRight">标题：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="title" value="<?php echo ($data[title]); ?>" class="sm input-text" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">描述：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="depict" value="<?php echo ($data[depict]); ?>" class="sm input-text" />(20字以内)
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">公告内容：</td>
                        <td class="tLeft" colspan="3">
                            <textarea name="notice" id="background"  ><?php echo ($data[notice]); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="center"></td>
                        <td class="center" colspan="3">
                            <input type="text" name="id" value="<?php echo ($data['id']); ?>" hidden="" />
                            <input type="submit" value="保 存" />
                        </td>
                    </tr>
                </table>
            </form>
            <br /><br /><br />
    </div>
</div>
</body></html>