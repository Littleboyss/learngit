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
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>激活码</em></a>
  </div>
</div>
<div class="pad_10">
  <div class="table-list">
    <table id="checkList" class="tableList" width='98%' border='0'
	cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
		<th align="center">ID</th>
		<th align="center">激活码</th>
    <th align="center">使用状态</th>
    <th align="center">使用时间</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo["code"]); ?></td>
      <td align="center">
        <?php if($vo['use_status'] == 0): ?>未使用
          <?php else: ?>
         <span style="color:red;">已使用</span><?php endif; ?>
      </td>
      <td align="center">
        <?php if($vo['use_time'] != 0): echo (date('Y-m-d h:i',$vo['use_time'])); endif; ?>
      </td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="9" align="center"><?php echo ($show); ?></td>
    </tr>
	</tbody>
</table>
      </table>
  </div>
</div>
</body></html>