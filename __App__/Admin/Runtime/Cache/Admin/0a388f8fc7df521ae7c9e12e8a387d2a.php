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
    <a href='javascript:;' class="on"><em>广告列表</em></a>
    <span>|</span>
    <a href="<?php echo U('add');?>"><em>添加广告</em></a>
    <!-- <span>|</span>
    <a href="javascript:;" onclick="sort_save()"><em>更新排序号</em></a> -->
  </div>
</div>
<div class="pad_10">
  <div class="table-list">
    <form name="myform" action="<?php echo U('delAll');?>" method="post" id="myform">
    <table id="checkList" class="tableList" width='98%' border='0' cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
    <th width="4%" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th align="center">编号</th>
    <th align="center">名称</th>
		<th align="center">标签</th>
    <th align="center">链接类型</th>
    <th align="center">广告图片</th>
    <th align="center">简介</th>
    <th align="center">添加时间</th>
    <th align="center">发布状态</th>
		<th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
      <td align="center"><input class="inputcheckbox" name="id[]" value="<?php echo ($vo['id']); ?>" type="checkbox"></td>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo["title"]); ?></td>
			<td align="center"><?php echo ($vo["tags"]); ?></td>
      <td align="center"><?php if($vo[type] == 1): ?>内部链接<?php else: ?>外部链接<?php endif; ?></td>
      <td align="center"><img src="<?php echo ($vo["icon"]); ?>" style="height:50px;" /></td>
      <td align="center"><?php echo ($vo["introduce"]); ?></td>
      <td align="center"><?php echo (date("Y-m-d H:i",$vo["add_time"])); ?></td>
      <td align="center"><a href="javascript:;" dataid="<?php echo ($vo["id"]); ?>" status="<?php echo ($vo["status"]); ?>" onclick="oneStatus(this)" dataname="status"><?php if($vo['status'] == 1): ?><img src="/images/no.gif" title="点击开启" /><?php else: ?><img src="/images/yes.gif" title="点击关闭" /></a><?php endif; ?></td>
      <td width="20%" align="center">

          <a href="<?php echo U('edit', array('id' => $vo['id']));?>">修改</a>
          &nbsp;
          <a href="<?php echo U('del', array('id' => $vo['id']));?>" onclick="return confirm('你确定删除吗？')">删除</a>
      </td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="7" align="center"><?php echo ($show); ?></td>
    </tr>
	</tbody>
</table>
      <!-- <div class="btn">
      <a href="#" onclick="javascript:$('input[type=checkbox]').attr('checked', true)">全选</a>/<a href="#" onclick="javascript:$('input[type=checkbox]').attr('checked', false)">取消</a>
        <input name="submit" type="submit" onclick="return confirm('确认删除吗？');" value="删除" id="submit" class="button">
      </div> -->
    </form>
  </div>
</div>
</body>
<script type="text/javascript">
function oneStatus(obj){
  var setStatus = $(obj).attr('status') == 1 ? 2 : 1; // 更新后的状态
  var dataName = $(obj).attr('dataname');
  $.ajax({
    dataType:"json",
    url:"<?php echo U('status');?>",
    data:"id="+$(obj).attr('dataid')+"&status="+setStatus+"&name="+dataName ,
    success: function(data){
      if(data && data==1){
        if(setStatus == 2){
            $(obj).html('<img src="/images/yes.gif" title="点击关闭" />');
            $(obj).attr('status', 2);
        }else{
            $(obj).html('<img src="/images/no.gif" title="点击开启" />');
            $(obj).attr('status', 1);
        }
      }
    }
  });
}


//修改排序的方法
function sort_save(){
  $('input[name=sort]').each(function(){
    var id = $(this).attr('dataid');
    var sort = this.value;
    $.ajax({
      dataType:"json",
      url:"<?php echo U('sort');?>",
      data:{id:id,sort:sort},
      success: function(data){
        window.location.href = window.location.href;
      }
    });
  });
}
</script>
</html>