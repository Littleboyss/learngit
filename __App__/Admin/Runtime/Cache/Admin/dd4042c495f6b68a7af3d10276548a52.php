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
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>配置列表</em></a>
    <span>|</span><a href="<?php echo U('betconfadd');?>"><em>添加配置</em></a> </div>
  </div>
</div>

<div class="pad_10">
  <div class="table-list">
    <form name="myform" action="" method="post" id="myform">
    <table id="checkList" class="tableList" width='98%' border='0' cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
    <th width="4%" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th align="center">ID</th>
		<th align="center">中文名</th>
    <th align="center">英文名</th>
    <th align="center">头像</th>
		<th align="center">球队</th>
    <th align="center">平均分</th>
		<th align="center">出场次数</th>
    <th align="center">出场时间</th>
    <th align="center">待定</th>
    <th align="center">伤病</th>
    <th align="center">禁赛</th>
    <th align="center">操作</th>
	</tr>
  </thead>
  <?php $arr = array(1 => '是',2 => '否'); ?>
	<tbody id="checkList_tbody">
  <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
      <td align="center"><input class="inputcheckbox" name="id[]" value="<?php echo ($vo['id']); ?>" type="checkbox"></td>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo["name"]); ?></td>
      <td align="center"><?php echo ($vo["e_name"]); ?></td>
			<td align="center"><img src="<?php echo ($vo["img"]); ?>" height="50px;"></td>
      <td align="center"><?php echo ($team[$vo[team_id]]); ?></td>
      <td align="center"><?php echo ($vo["average"]); ?></td>
      <td align="center"><?php echo ($vo["play_num"]); ?></td>
      <td align="center"><?php echo ($vo["play_time"]); ?></td>
      <td align="center"><?php echo ($arr[$vo[undetermined]]); ?></td>
      <td align="center"><?php echo ($arr[$vo[illness]]); ?></td>
      <td align="center"><?php echo ($arr[$vo[ban]]); ?></td>
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