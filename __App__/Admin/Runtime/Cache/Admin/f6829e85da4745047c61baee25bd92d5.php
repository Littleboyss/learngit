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



<link rel="stylesheet" type="text/css" href="/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="/js/calendar/win2k.css"/>
<script type="text/javascript" src="/js/calendar/calendar.js"></script>
<script type="text/javascript" src="/js/calendar/lang/en.js"></script>

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
    <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>退回流盘</em></a>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="" onsubmit="return false;">
            <table width="98%" border="0" cellpadding="0" cellspacing="1"
                   class="htable" align="center">
            </table>
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">
                    <tr>
                        <td class="tRight">赛事的id：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="match_id" value="" class="huge iptxt" />
                        </td>
                    </tr>
                    <tr id="subtn">
                        <td class="center"></td>
                        <td class="center" colspan="3">
                            <input onclick="if(confirm('请再次检查赛事id是否正确,你确定进行此操作吗？')){restore();}" type="submit" value="返回" />
                        </td>
                    </tr>
                </table>
            </form>
            <br /><br /><br />
    </div>
</div>
<div style="width:auto; height:400px; border:1px solid #CCC; overflow-y:scroll; text-align:left;" class="input-text" id="jindu"></div>
</body>
<script type="text/javascript">
    function restore() {
        var match_id = $('input[name=match_id]').val();
        if(match_id <= 0 || match_id == ''){
            alert('赛事id没有填写');
            return false;
        }
        $.ajax({
            data:{id:match_id},
            url:"<?php echo U('callbackrestoregold');?>",
            dataType:'json',
            success:function(e){
                if(e.error == 2){
                    alert(e.msg);
                }else if(e.error == 1){
                    var f = e.msg;
                    $('#jindu').prepend('<p>用户:'+f.uid+'昵称:'+f.user+'木头数:'+f.gold+'</p>')
                    restore();
                }else if(error.error == 0){
                    alert('处理完成');
                }else{
                    alert('处理完成 null');
                }
            },
            error:function(e){
                console.log(e);
                alert('无法执行请求，错误信息请看console.log');
                
            }
        });
    }
</script>
</html>