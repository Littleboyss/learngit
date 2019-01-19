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


<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href="<?php echo U('index');?>"><em>管理员管理</em></a><span>|</span><a href='javascript:;' class="on"><em>添加管理员</em></a>
    </div>
</div>
<script type="text/javascript">
    <!--
    $(function(){
        $.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
        $("#username").formValidator({onshow:"请输入wrok.sgamer.com的用户名",onfocus:"请输入wrok.sgamer.com的用户名"}).inputValidator({min:1,onerror:"用户名不能为空"});
    })
    //-->
</script>
<div class="pad_10">
    <div class="common-form">
        <form name="myform" action="" method="post" id="myform">
            <table width="100%" class="table_form contentWrap">
                <tr>
                    <td width="80">用户名</td> 
                    <td><input type="text" name="username"  class="input-text" id="username" /></td>
                </tr>
                <tr>
                    <td>权限</td> 
                    <td>
                        <?php if(is_array(C("MODULES"))): $i = 0; $__LIST__ = C("MODULES");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label <?php if(($i) != "1"): ?>style="margin-left: 10px"<?php endif; ?>><input type="checkbox" name="rights[]" value="<?php echo ($key); ?>" /> <?php echo ($vo); ?></label><?php endforeach; endif; else: echo "" ;endif; ?>
                        <div class="onShow">拥有系统管理权限就拥有所有权限</div>
                    </td>
                </tr>
            </table>
            <div class="bk15"></div>
            <input type="submit" value="提交" class="button" />
        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("input[name='rights[]']").click(function(){
            if (this.value == 'Admin') {
                if (this.checked) {
                    $("input[name='rights[]']").not(this).attr("disabled", 'disabled');
                } else {
                    $("input[name='rights[]']").removeAttr("disabled");
                }
            }
        });
    });
</script>
</body>
</html>