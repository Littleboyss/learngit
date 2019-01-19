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
		width: 100px;
	}
</style>
</head>
<body>


<div class="subnav">
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>兑换列表</em></a>
  </div>
</div>
<div class="pad_10">
  <div class="table-list">
    <div class="explain-col search-form">
     <form name="myform" action="<?php echo U('index');?>" method="post" id="myform">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td width="10%">
          <select name="type">
            <option value="0" <?php if($type == 0): ?>selected="selected"<?php endif; ?>>全部</option>
            <?php if(is_array($category)): foreach($category as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>" <?php if($type == $v[id]): ?>selected="selected"<?php endif; ?>><?php echo ($v["catename"]); ?></option><?php endforeach; endif; ?>
          </select>
      </td> 
      <td width="10%">
          <select name="status">
            <option value="3" <?php if($status == 3): ?>selected<?php endif; ?>>全部</option>
            <option value="0" <?php if($status == 0): ?>selected<?php endif; ?>>未处理</option>
            <option value="1" <?php if($status == 1): ?>selected<?php endif; ?>>已处理</option>
          </select>
      </td>

      <td width="27%">奖品名称：<input type="text" name="name" value="<?php echo ($name); ?>" />(支持模糊查询)</td>
      <td>
          <input name="submit" type="submit" value="查询" class="button"/>
          <input name="butoon" onclick="location='<?php echo U('index');?>'" type="button" value="重置" class="button" />
      </td>
      </tr>
      </table>
  </form>
  </div>
    <table id="checkList" class="tableList" width='98%' border='0'
	cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
		<th align="center">用户ID</th>
    <th align="center">用户名</th>
    <th align="center">昵称</th>
    <th align="center">电话</th>
		<th align="center">兑换奖品</th>
    <th align="center">数量</th>
    <th align="center">兑换状态</th>
    <th align="center">兑换时间</th>	</tr>
  </thead>
  <?php $status = array(0=>'待处理',1=>'已发送'); ?>
	<tbody id="checkList_tbody">
  <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo["user"]); ?></td>
      <td align="center"><?php echo ($vo["nickname"]); ?></td>
      <td align="center"><?php echo ($vo["phone"]); ?></td>
      <td align="center"><?php echo ($vo["name"]); ?></td>
      <td align="center"><?php echo ($vo["num"]); ?></td>
      <td align="center" title="点击修改为已处理" onclick="changeStatus(this,<?php echo ($vo["e_id"]); ?>)">
      
      <?php if($vo[status] == 0): ?><span style="color:red;"><?php echo ($status[$vo[status]]); ?></span><?php else: echo ($status[$vo[status]]); endif; ?>
      </td>
      <td align="center"><?php echo (date('Y-m-d h:i',$vo['add_time'])); ?></td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="9" align="center"><?php echo ($show); ?></td>
    </tr>
	</tbody>
</table>
      </table>
  </div>
</div>
<script type="text/javascript">
function changeStatus(obj, a) {
  if(!confirm('你确定吗?修改后不可以复原?')){
    return false;
  }
  $.ajax({
    data:{id:a},
    url:"<?php echo U('sta');?>",
    success:function(e){
      if(e == 0){
        $(obj).html('已发送');
      }else{
        alert('已经处理过了');
      }
    }
  });
}
</script>
</body></html>