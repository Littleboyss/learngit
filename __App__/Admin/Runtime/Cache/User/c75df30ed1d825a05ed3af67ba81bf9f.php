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
<style type="text/css">
	html{_overflow-y:scroll}
</style>
<link href="/kindeditor/plugins/code/prettify.css" rel="stylesheet" type="text/css" />
<link href="/kindeditor/themes/default/default.css" rel="stylesheet" type="text/css" />
</head>
<body>


<div class="pad_10">
    <div class="common-form">
        <form name="myform" action="" method="post" id="myform">
            <input type="hidden" name="id" value="<?php echo ($admin["id"]); ?>" />
            <table width="100%" class="table_form contentWrap">
                <tr>
                    <td width="80">用户名:</td> 
                    <td><?php echo ($nickname); ?></td>
                </tr>
                <tr>
                    <td width="80">真实姓名:</td> 
                    <td><?php if($data[true_name] == ''): ?>用户暂未填写<?php else: echo ($data["true_name"]); endif; ?></td>
                </tr>
                <tr>
                    <td width="80">电话:</td> 
                    <td><?php if($data[phone] == ''): ?>用户暂未填写<?php else: echo ($data["phone"]); endif; ?></td>
                </tr>
                <tr>
                    <td>邮寄地址:</td> 
                    <td><?php if($data[address] == ''): ?>用户暂未填写<?php else: echo ($data["address"]); endif; ?></td>
                </tr>
            </table>
            <div class="bk15"></div>
            <input type="button" value="提交" class="dialog" id="dosubmit" />
        </form>
    </div>
</div>
</body>
</html>