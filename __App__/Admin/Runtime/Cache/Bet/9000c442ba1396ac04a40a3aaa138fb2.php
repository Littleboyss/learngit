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


<div class="subnav">
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>房间列表</em></a>
    <span>|</span><a href="<?php echo U('add');?>"><em>添加房间</em></a></div>
  </div>
</div>
<div class="pad_10">
  <div class="table-list">
    <form name="myform" action="" method="post" id="myform">
    
    <table id="checkList" class="tableList" width='98%' border='0'
	cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
    <th width="4%" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th align="center">ID</th>
		<th align="center">房间图标</th>
    <th align="center">房间名称</th>
    <th align="center">排序号</th>
		<th align="center">房间简介</th>
		<th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
                <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
      <td align="center"><input class="inputcheckbox" name="id[]" value="<?php echo ($vo['hero_id']); ?>" type="checkbox"></td>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td width="200" align="center"><img src="<?php echo ($vo["img"]); ?>" height="50px" /></td>
      <td align="center"><?php echo ($vo["name"]); ?></td>
      <td align="center"><?php echo ($vo["sort"]); ?></td>
			<td align="center"><?php echo ($vo["introduce"]); ?></td>
      <td width="20%" align="center">
          <a href="<?php echo U('edit', array('id' => $vo['id']));?>">修改</a>
          &nbsp;
          <a href="<?php echo U('del', array('id' => $vo['id']));?>" onclick="return confirm('你确定删除该项目？')">删除</a>
      </td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="7" align="center"><?php echo ($show); ?></td>
    </tr>
	</tbody>
</table>
      </table>
      <!-- <div class="btn"><a href="#" onclick="javascript:$('input[type=checkbox]').attr('checked', true)">全选</a>/<a href="#" onclick="javascript:$('input[type=checkbox]').attr('checked', false)">取消</a>
        <input name="submit" type="submit" onclick="return confirm('确认删除吗？');" value="删除" id="submit" class="button">
      </div> -->
    </form>
  </div>
</div>
</body></html>