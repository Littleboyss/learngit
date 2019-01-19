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


<style type="text/css">
select optgroup {
    color: #2288cc;
}
select option {
    color: #2288cc;
}
</style>
<div class="subnav">
  <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>奖品列表</em></a>
        <span>|</span>
        <a href="<?php echo U('turnplate_add');?>"><em>奖品添加</em></a>
    </div>
      <div class="explain-col search-form">
    <form name="myform" action="<?php echo U('turnplate_list');?>" method="post">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                  <select name="class_id">
                    <option value="3"
                        <?php if($class_id == 3){ echo 'selected'; }?>
                    >全部</option>
                    <option value="1" <?php if($class_id == 1){ echo 'selected'; }?>>钻石转盘</option>
                    <option value="2"
                    <?php if($class_id == 2){ echo 'selected'; }?>
                  >木头转盘</option>
                  <option value="3"
                    <?php if($class_id == 3){ echo 'selected'; }?>
                  >活动奖励</option>
                  </select>
                </td>
                <td>
                    <input name="submit" type="submit" value="查询" class="button"/>
                </td>
            </tr>
        </table>
    </form>
    </div>
  </div>
</div>
<div class="pad_10">
  <div class="table-list">
    <form name="myform" action="" method="post" id="myform">
    <table id="checkList" class="tableList" width='98%' border='0' cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
    <th align="center">ID</th>
    <th align="center">权重</th>
    <th align="center">奖级</th>
    <th align="center">类型</th>
    <th align="center">奖品名称</th>
    <th align="center">奖品数量</th>
		<th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo['chance']); ?></td>  
      <td align="center"><?php echo ($vo['level']); ?></td>  
      <td align="center"><?php  if($vo['class_id'] == 2){ echo '木头转盘'; }elseif($vo['class_id'] == 1){ echo '钻石转盘'; }elseif($vo['class_id'] == 3){ echo '活动奖励'; } ?></td>  
      <td align="center"><?php echo ($vo['name']); ?></td>  
      <td align="center">  <?php echo ($vo['nums']); ?>   </td>    
      <td align="center">
          <a href="<?php echo U('turnplate_edit', array('id' => $vo['id']));?>">修改</a>
          &nbsp;
          <!-- <a href="<?php echo U('turnplate_del', array('id' => $vo['id'],'bonus_id'=> $vo['bonus_id']));?>">删除</a> -->
      </td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="11" align="center"><?php echo ($show); ?></td>
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
</body>
<style type="text/css">
  table tr td a img{
    width: 17px;
  }
</style>
<script type="text/javascript">
function edit(url) {
  window.top.art.dialog(
      { title:'修改', id:'edit', iframe:url ,width:'350px',height:'245px'},
      function(){
          var d = window.top.art.dialog({id:'edit'}).data.iframe;
          var form = d.document.getElementById('dosubmit');
          form.click();
          return false;
      },
      function(){
          window.top.art.dialog({id:'edit'}).close()
          
      }
  );
}
function oneStatus(obj){
  var setStatus = $(obj).attr('status') == 1 ? 0 : 1; // 更新后的状态
  var dataName = $(obj).attr('dataname');
  var id = $(obj).attr('dataid');
  $.ajax({
    dataType:"json",
    url:"/index.php?g=Gift&m=Exchange&a=edit",
    data: {
      'id': id,
      'state': setStatus
    },
    type:'post',
    success: function(data){
        if(setStatus == 1){
            $(obj).html('<img src="/images/yes.gif" title="点击关闭" />');
            $(obj).attr('state', 1);
        }else{
            $(obj).html('<img src="/images/no.gif" title="点击开启" />');
            $(obj).attr('state', 0);
        }
    }
  });
}
</script>
</html>