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


<script language="javascript" type="text/javascript" src="/kindeditor/kindeditor-min.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="/kindeditor/lang/zh_CN.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="/kindeditor/kconfig.js" charset="UTF-8"></script>
<style type="text/css">
input.iptxt{
    width: 350px;
}
input.sm{
    width: 100px;
}
.sbtn{
    height: 35px;
    line-height: 35px;
    cursor: pointer;
    display: inline-block;
}
.sbtn:hover{
    color: blue;
}
.tRight{
    text-align: right;
}
.tarea{
    width:345px;
    height: 106px;
}
</style>

<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>页面生成</em></a>
        <span>|</span>
        <a href='http://www.aifamu.com/frame/bet.html' target="_blank"><em>查看生成的页面</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('add');?>">
            <table width="98%" border="0" cellpadding="0" cellspacing="1"
                   class="htable" align="center">
            </table>
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">
                    <tr>
                        <td class="tRight">标签：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="tag" value="" class="huge iptxt" />
                        </td>
                    </tr>
                    <tr id="subtn">
                        <td class="center"></td>
                        <td class="center" colspan="3">
                            <input onclick="obhtml()" type="button" value="生成" />
                        </td>
                    </tr>
                </table>
            </form>
    </div>
</div>
<script type="text/javascript">
    function obhtml(){
        var tag = $('input[name=tag]').val();
        $.ajax({
            data:{tag:tag},url:"<?php echo U('obHtml');?>",success:function(e){alert('生成成功');}
        });
    }
</script>
</body>
</html>