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


<div class="subnav">
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>充值记录</em></a>
  </div>
</div>
<div class="subnav">
<div class="explain-col search-form">
<form action="<?php echo U('User/UserCharge/index');?>" method="post">
      关键字:<input placeholder="订单号" name="keyword" type="text" id="check_box" class="input-text">
      <input type="submit" value="搜索" class="button">
</form>
</div>
</div>
<div class="pad_10">
  <div class="table-list">
    <table id="checkList" class="tableList" width='98%' border='0'
	cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
		<th align="center">订单号</th>
		<th align="center">用户名</th>
    <th align="center">昵称</th>
    <th align="center">金额（元）</th>
    <th align="center">木头数</th>
    <th align="center">充值时间</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["order_no"]); ?></td>
			<td align="center"><?php echo ($vo["user"]); ?></td>
      <td align="center"><?php echo ($vo["nickname"]); ?></td>
      <td align="center">￥<?php echo (number_format($vo['amount']/100,2)); ?></td>
      <td align="center"><?php echo ($vo["gold"]); ?></td>
      <td align="center"><?php echo (date('Y-m-d H:i',$vo['add_time'])); ?></td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="9" align="center"><?php echo ($show); ?></td>
    </tr>
	</tbody>
</table>
      </table>
  </div>
</div>
</body>
<script type="text/javascript">
function oneStatus(obj){
  var setStatus = $(obj).attr('status') == 1 ? 0 : 1; // 更新后的状态
  var dataName = $(obj).attr('dataname');
  $.ajax({
    dataType:"json",
    url:"<?php echo U('state');?>",
    data:"id="+$(obj).attr('dataid')+"&status="+setStatus,
    success: function(data){
      if(data && data==1){
        if(setStatus == 0){
            $(obj).html('<img src="/images/yes.gif" title="点击冻结" />');
            $(obj).attr('status', 0);
        }else{
            $(obj).html('<img src="/images/no.gif" title="点击解除冻结" />');
            $(obj).attr('status', 1);
        }
      }
    }
  });
}
</script>
</html>