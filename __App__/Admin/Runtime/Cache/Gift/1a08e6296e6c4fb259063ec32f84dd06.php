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
                <td width="40%">商品分类：
                  <select name="sub_id" type="text"/>
                    <option value="0">所有</option>
                    <?php
 foreach ($projects as $key => $value) { if ($value['pid'] == 0 ) { echo '<optgroup label="'.$value['name'].'">'; foreach ($projects as $k => $v) { if ($v['pid'] == $value['id']) { echo '<option value="'.$v['id'].'"'; if($sub_id == $v['id']){ echo 'selected'; } echo '>'.$v['name'].'</option>'; } } echo "</optgroup>"; } } ?>
                  </select>
                </td>
                <td width="10%">
                  <select name="status">
                    <option value="4"
                        <?php if($status == 4){ echo 'selected'; }?>
                    >全部</option>
                    <option value="1" <?php if($status == 1){ echo 'selected'; }?>>未处理</option>
                    <option value="2"
                    <?php if($status == 2){ echo 'selected'; }?>
                  >已处理</option>
                  <option value="3"
                    <?php if($status == 3){ echo 'selected'; }?>
                  >已签收</option>
                  </select>
                </td>
                <td width="40%">
                  订单号查询：  <input type="text" name="numbers" value="<?php echo ($numbers); ?>">
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
    <th align="center">订单编号</th>
    <th align="center">商品名称</th>
    <th align="center">用户名称</th>
    <th align="center">用户手机号</th>
    <th align="center">商品类型</th>
    <th align="center">商品购买数量</th>
    <th align="center">商品总价格</th>
    <th align="center">订单状态</th>
    <th align="center">添加时间</th>
		<th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo['numbers']); ?></td>  
      <td align="center"><?php echo ($vo['name']); ?></td>  
      <td align="center"><?php echo ($vo['username']); ?></td>  
      <td align="center"><?php echo ($vo['phone']); ?></td>  
      <td align="center"><?php  if($vo['goods_type'] == 2){ echo '木头'; }else{ echo '砖石'; } ?></td>  
      <td align="center"><?php echo ($vo['goods_nums']); ?></td>  
      <td align="center"><?php echo ($vo['price']); ?></td>  
      <td align="center"><?php  if($vo['status'] == 1){ echo '<font class="red">未处理</font>'; }elseif($vo['status'] == 2){ echo '已发送'; }elseif($vo['status'] == 3){ echo '已签收'; } ?></td>  
      <td align="center">  <?php echo (date("Y-m-d H:i",$vo['addtime'])); ?>   </td>    
      <td align="center">
          <a href="javascript:edit('<?php echo U('view',array('id'=>$vo['id'],'user_id'=>$vo['user_id']));?>');">查看</a>
          <a href="javascript:setStatus('<?php echo ($vo['id']); ?>');" onclick="return confirm('你确定更改为已签收？')">更改状态</a>
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
      { title:'订单详情', id:'edit', iframe:url ,width:'350px',height:'350px'},
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