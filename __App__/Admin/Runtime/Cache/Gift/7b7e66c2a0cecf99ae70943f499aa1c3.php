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
    <a href='javascript:;' class="on"><em>商品列表</em></a>
    <span>|</span>
    <a href="<?php echo U('add');?>"><em>商品添加</em></a>
  </div>
  <div class="explain-col search-form">
    <form name="myform" action="<?php echo U('index');?>" method="post">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="250">商品分类：
                  <select name="sub_id" type="text"/>
                    <option value="0">所有</option>
                    <?php
 foreach ($projects as $key => $value) { if ($value['pid'] == 0 ) { echo '<optgroup label="'.$value['name'].'">'; foreach ($projects as $k => $v) { if ($v['pid'] == $value['id']) { echo '<option value="'.$v['id'].'"'; if($sub_id == $v['id']){ echo 'selected'; } echo '>'.$v['name'].'</option>'; } } echo "</optgroup>"; } } ?>
                  </select>
                </td>
                <td width="230">
                  商品名称:<input placeholder="名称" name="name" type="text" class="input-text">
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
    <th align="center">商品编号</th>
		<th align="center">商品缩略图</th>
    <th align="center">商品名称</th>
    <th align="center">发布者</th>
    <th align="center">商品价格</th>
    <th align="center">商品库存</th>
    <th align="center">是否上架</th>
    <th align="center">是否为热门推荐</th>
    <th align="center">添加时间</th>
		<th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><img src="<?php echo ($vo["avatar_img"]); ?>"></td>
      <td align="center"><?php echo ($vo["name"]); ?></td>
      <td align="center"><?php echo ($vo["author"]); ?></td>
      <td align="center" class="red"><?php echo ($vo["price"]); ?>
      <?php  if($vo['type'] == 2){ echo '木头'; }else{ echo '砖石'; } ?>
      </td>
      <td align="center"><?php echo ($vo["has_nums"]); ?></td>

      <td align="center"><a href="javascript:;" dataid="<?php echo ($vo["id"]); ?>" status="<?php echo ($vo["state"]); ?>" onclick="oneStatus(this)" dataname="state"><?php if($vo['state'] == 0): ?><img src="/images/no.gif" title="点击开启" /><?php else: ?><img src="/images/yes.gif" title="点击关闭" /></a><?php endif; ?></td>
      <td align="center"><a href="javascript:;" dataid="<?php echo ($vo["id"]); ?>" status="<?php echo ($vo["hot_sort"]); ?>" onclick="oneStatus(this)" dataname="hot_sort"><?php if($vo['hot_sort'] == 0): ?><img src="/images/no.gif" title="点击开启" /><?php else: ?><img src="/images/yes.gif" title="点击关闭" /></a><?php endif; ?></td>
      <td align="center"><?php echo (date("Y-m-d H:i",$vo["addtime"])); ?></td>
      <td width="22%" align="center">
        <?php if($vo['is_virtual'] == 1): ?><a href="javascript:edit('<?php echo U('import',array('id'=>$vo['id']));?>');">导入</a>
          &nbsp;
          <a title="随机生成10个卡号卡密" href="javascript:createCard(<?php echo ($vo['id']); ?>);">生成</a>
          &nbsp;
          <a href="javascript:edit('<?php echo U('view',array('id'=>$vo['id']));?>');">查看</a>
          &nbsp;
          <?php else: ?>
            <a href="javascript:product_edit('<?php echo U('product_edit',array('id'=>$vo['id']));?>');">更改商品库存</a>
              &nbsp;<?php endif; ?>       
        <a href="<?php echo U('edit', array('id' => $vo['id']));?>">修改</a>
        &nbsp;
        <a href="<?php echo U('del', array('id' => $vo['id']));?>" onclick="return confirm('你确定删除该商品？')">删除</a>
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
</body>
<style type="text/css">
  table tr td a img{
    width: 17px;
  }
</style>
<script type="text/javascript">
function edit(url) {
  window.top.art.dialog(
      { title:'号码导入', id:'edit', iframe:url ,width:'700px',height:'500px'},
      function(){
          var d = window.top.art.dialog({id:'edit'}).data.iframe;
          var form = d.document.getElementById('dosubmit');
          form.click();
          return false;
      },
      function(){
          window.top.art.dialog({id:'edit'}).close()
          location.reload()
      }
  );
}
function product_edit(url) {
  window.top.art.dialog(
      { title:'库存修改', id:'edit', iframe:url ,width:'700px',height:'500px'},
      function(){
          var d = window.top.art.dialog({id:'edit'}).data.iframe;
          var form =  $('.aui_state_highlight');
          form.click();
          window.top.art.dialog({id:'edit'}).close()
          return false;
      },
      function(){
          window.top.art.dialog({id:'edit'}).close()
          location.reload()
      }
  );
}
  function createCard(a){
    if(confirm('你确定生成吗?')){
      $.ajax({
        data:{id:a},
        url:"<?php echo U('createCard');?>",
        success:function(e){
          alert(e);
        }
      });
    }else{
      //return false;
    }
  }

function oneStatus(obj){
  var setStatus = $(obj).attr('status') == 1 ? 0 : 1; // 更新后的状态
  var dataName = $(obj).attr('dataname');
  console.log(dataName);
  var id = $(obj).attr('dataid');
  $.ajax({
    dataType:"json",
    url:"/index.php?g=Gift&m=Exchange&a=set_status",
    data: {
      'id': id,
      dataName: dataName,
      setStatus:setStatus
    },
    type:'post',
    success: function(data){
      console.log(data);
        if(setStatus == 1 ){
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