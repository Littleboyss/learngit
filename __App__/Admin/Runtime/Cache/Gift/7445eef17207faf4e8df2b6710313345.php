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
select optgroup {
    color: #2288cc;
}
select option {
    color: #2288cc;
}
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
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="">
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">
                    <tr>
                        <td class="tRight">用户ID：</td>
                        <td class="tLeft" colspan="3">
                            <?php echo ($data["user_id"]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">用户名：</td>
                        <td class="tLeft" colspan="3">
                            <?php echo ($data["username"]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">提交时间：</td>
                        <td class="tLeft" colspan="3">
                            <?php echo (date("Y-m-d H:i",$data["addtime"])); ?>
                        </td>
                    </tr>
                    <?php if($data['status']==1){ echo '<tr> <td class="tRight">处理时间：</td> <td class="tLeft" colspan="3"> '.date('Y-m-d H:i',$data['deltime']).' </td> </tr>'; }?>
                    <tr>
                        <td class="tRight">反馈问题：</td>
                        <td class="tLeft" colspan="3">
                            <?php echo ($data["question"]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">问题描述：</td>
                        <td class="tLeft" colspan="3">
                            <?php echo ($data["user_id"]); ?>

                        </td>
                    </tr>
                    <?php if($data['status']==1){ echo '<tr> <td class="tRight">处理回复：</td> <td class="tLeft" colspan="3"> '.$data['request'].' </td> </tr>'; }else{ echo ' <tr> <td class="tRight">处理回复：</td> <td class="tLeft" colspan="3"> <textarea name="request"></textarea> </td> </tr><input type="text" name="id" hidden="" value="'.$data['id'].'" /><input type="text" name="uid" hidden="" value="'.$data['user_id'].'" /><input type="text" name="username" hidden="" value="'.$data['username'].'" /><tr id="subtn"> <td class="center"></td> <td class="center" colspan="3"> <input type="submit" value="保 存" /> </td> </tr>'; }?>
                     </table>
            </form>
            <br /><br /><br />
    </div>
</div>
</body>
<script>
$("#show").on('click',function(){
    $('.img').show()
})
$("#hide").on('click',function(){
    $('.img').hide()
})

function checktype(a,b){
    $("." + a).css('display','table-row');
    $("." + b).css('display','none');
}
$("#sub_id").change(function(){	
    $('#list').html('');
    var sub_id = $('#sub_id option:selected').val()
	if (sub_id==4) {
        $('#list').html('<tr><td class="tRight">商品ID：</td> <td class="tLeft" colspan="3"> <input type="text" name="goods_id" value="<?php echo ($data["goods_id"]); ?>" class="huge iptxt" /> </td> </tr>');
    }
})


</script>
</html>