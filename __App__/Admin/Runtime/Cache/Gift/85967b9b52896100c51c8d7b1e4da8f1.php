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
.tRight{
    text-align: right;
}
select optgroup {
    color: #2288cc;
}
select option {
    color: #2288cc;
}
input.sm{
    width:100px;
}
</style>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <a href='javascript:;' class="on"><em>修改分类</em></a>
    <span>|</span>
    <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('edit');?>">
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">
                       <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
                    <tr>
                        <td class="tRight">所属分类：</td>
                        <td class="tLeft" colspan="3">
                            <select name="pid">
                            <option value="0">顶级分类</option>
                            <?php
 foreach ($projects as $k => $v) { echo '<option value="'.$v['id'].'"'; if($data['pid'] == $v['id']){ echo 'selected'; } echo '>'.str_repeat('&nbsp;',$v['level']*4).$v['name'].'</option>'; } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">名称：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="catename" value="<?php echo ($data["name"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>

                    <tr <?php  if (!isset($attribute)) { echo 'class="display"'; } ?>
                    >
                        <td class="tRight">该分类对应的属性名称：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="attr_name" value="<?php  echo implode(',',$attribute); ?>" class="huge iptxt" />(多个属性名称以逗号分隔)
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
            <br /><br /><br />
    </div>
</div>
</body></html>