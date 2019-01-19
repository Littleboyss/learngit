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
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>用户列表</em></a>
  </div>
  <div class="explain-col search-form">
    <form action="<?php echo U('User/User/index');?>" method="post">
          关键字:<input placeholder="手机号或昵称(昵称支持模糊搜索)" name="keyword" type="text" id="check_box" class="input-text">
          <input type="submit" value="搜索" class="button">
    </form>
  </div>

</div>

<div class="subnav">

</div>
<div class="pad_10">
  <div class="table-list">
    <table id="checkList" class="tableList" width='98%' border='0'
	cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
		<th align="center">ID</th>
    <th align="center">昵称</th>
		<th align="center">手机号</th>
    <th align="center">门票</th>
    <th align="center">木头</th>
    <th align="center">钻石</th>
    <th align="center">注册时间</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
			<td align="center"><?php echo ($vo["id"]); ?></td>
      <td align="center"><?php echo ($vo["username"]); ?></td>
			<td align="center"><?php if($vo[phone] != ''): ?><span style="color: red;"><?php echo ($vo["phone"]); ?></span><?php else: ?>无<?php endif; ?></td>
      <td align="center"><?php echo ($vo["entrance_ticket"]); ?></td>
      <td align="center"><?php echo ($vo["gold"]); ?></td>
      <td align="center"><?php echo ($vo["diamond"]); ?></td>
      <td align="center"><?php echo (date('Y-m-d H:i:s',$vo['add_time'])); ?></td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="9" align="center"><?php echo ($show); ?></td>
    </tr>
	</tbody>
</table>
      </table>
  </div>
</div>
<script>
    function sendMessage(id) {
        window.top.art.dialog(
            { title:'给用户发送消息', id:'edit', iframe:"<?php echo U('sendMessage');?>"+'&id='+id ,width:'700px',height:'400px'},
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
    function vaddress(id,nickname) {
        window.top.art.dialog(
            { title:'给用户发送消息', id:'edit', iframe:"<?php echo U('vaddress');?>"+'&id='+id+'&nickname='+nickname ,width:'450px',height:'180px'},
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
    function conutUser(obj,a){
      $.ajax({
        data:{reg_type:a},
        url:"<?php echo U('countUser');?>",
        success:function(e){
          $(obj).html(e);
        }
      });
    }

function oneStatus(obj){
  var setStatus = $(obj).attr('status') == 1 ? 2 : 1; // 更新后的状态
  var dataName = $(obj).attr('dataname');
  $.ajax({
    dataType:"json",
    url:"<?php echo U('state');?>",
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
</script>
</body>
</html>