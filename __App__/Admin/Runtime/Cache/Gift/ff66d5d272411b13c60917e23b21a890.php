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
  <div class="explain-col search-form">
    <form name="myform" action="<?php echo U('index');?>" method="post">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="10%">
                  <select name="status">
                    <option value="4"
                        <?php if($status == 4){ echo 'selected'; }?>
                    >全部</option>
                    <option value="1" <?php if($status == 1){ echo 'selected'; }?>>已处理</option>
                    <option value="2"
                    <?php if($status == 2){ echo 'selected'; }?>
                  >未处理</option>
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
    <th align="center">用户ID</th>
    <th align="center">用户名称</th>
    <th align="center">反馈问题</th>
    <th align="center">提交时间</th>
    <th align="center">处理时间</th>
    <th align="center">客服名称</th>
    <th align="center">处理状态</th>
		<th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo['user_id']); ?></td>  
      <td align="center"><?php echo ($vo['username']); ?></td>  
      <td align="center"><?php echo ($vo['question']); ?></td>  
      <td align="center">  <?php echo (date("Y-m-d H:i",$vo['addtime'])); ?>   </td>    
      <td align="center">  <?php if ($vo['status'] == 2){ ?>
        <font class="red">未处理</font>
      <?php }else{ echo (date("Y-m-d H:i",$vo['deltime'])); ?>  
      <?php }?> 
      </td>    
      <td align="center"><?php echo ($vo['admin_name']); ?></td>  
      <td align="center"><?php  if($vo['status'] == 2){ echo '<font class="red">未处理</font>'; }elseif($vo['status'] == 1){ echo '<font class="green">已处理</font>'; } ?></td>  
      <td align="center">
          <a href="javascript:edit('<?php echo U('dealwith',array('id'=>$vo['id']));?>');">查看</a>
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
      { title:'反馈详情', id:'edit', iframe:url ,width:'500px',height:'400px'},
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
function setStatus(id){
  $.ajax({
    dataType:"json",
    url:"/index.php?g=Gift&m=Order&a=setStatus",
    data: {
      'id': id
    },
    type:'post',
    success: function(data){
      alert(data);
    }
  });
}
</script>
</html>