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
<script type="text/javascript">
	window.focus();
</script>
<style type="text/css">
	html{_overflow-y:scroll}
</style>
</head>
<body>


<div class="pad_10">
    <div class="common-form">
        <form name="myform" action="" method="post" id="myform">
            <input type="hidden" name="id" value="<?php echo ($admin["id"]); ?>" />
            <table width="100%" class="table_form contentWrap">
                <tr>
                    <td width="80">举报人:</td> 
                    <td><?php echo ($data["user"]); ?></td>
                </tr>
                <tr>
                    <td width="80">被举报人:</td> 
                    <td><?php echo ($_data["user"]); ?></td>
                </tr>
                <tr>
                    <td width="80">评论的内容:</td> 
                    <td><?php echo ($_data["content"]); ?></td>
                </tr>
                <tr>
                    <td width="80">评论的比赛:</td> 
                    <td><a target="_blank" href="http://www.aifamu.com/detail/<?php echo ($_data["match_id"]); ?>.html">点击查看</a></td>
                </tr>
            </table>
            <div class="bk15"></div>
            <input type="button" value="提交" class="dialog" id="dosubmit" />
        </form>
    </div>
</div>
</body>
</html>