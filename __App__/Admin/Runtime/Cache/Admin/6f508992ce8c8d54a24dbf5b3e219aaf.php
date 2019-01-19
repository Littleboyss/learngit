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
    <a href='javascript:;' class="on"><em>举报管理列表</em></a>
  </div>
  <div class="explain-col search-form">
    <form action="<?php echo U('index');?>" method="post">
          关键字:<input placeholder="昵称(支持模糊搜索)" name="nickname" type="text" id="check_box" class="input-text">
          <input type="submit" value="搜索" class="button">
    </form>
  </div>
</div>
<div class="pad_10">
  <div class="table-list">
    <form name="myform" action="<?php echo U('Bet/Easy/easyDel');?>" method="post" id="myform">
    <table id="checkList" class="tableList" width='98%' border='0' cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
    <th width="4%" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th align="center">编号</th>
		<th align="center">举报人</th>
    <th align="center">举报时间</th>
    <th align="center">举报类型</th>
    <th align="center">举报id号</th>
    <th align="center">处理状态</th>
    <th align="center">处理人</th>
		<th align="center">处理时间</th>
    <th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
      <td align="center"><input class="inputcheckbox" name="id[]" value="<?php echo ($vo['id']); ?>" type="checkbox"></td>
			<td align="center"><?php echo ($vo["id"]); ?></td>
			<td align="center"><?php echo ($vo["user"]); ?></td>
      <td align="center"><?php echo (date("Y-m-d H:i",$vo["add_time"])); ?></td>
      <td align="center"><?php echo ($vo["class"]); ?></td>
      <td align="center"><?php echo ($vo["coment_id"]); ?></td>
      <td align="center"><a href="javascript:;" dataid="<?php echo ($vo["id"]); ?>" status="<?php echo ($vo["status"]); ?>" onclick="oneStatus(this)" dataname="status"><?php if($vo['status'] == 1): ?><img src="/images/no.gif" title="未处理,点击修改为已处理" /><?php elseif($vo['status'] == 2): ?><img src="/images/yes.gif" title="已处理" /></a><?php else: ?>已忽略<?php endif; ?></td>
      <td align="center" id="user<?php echo ($vo["id"]); ?>"><?php if($vo["author"] == ''): ?>暂无<?php else: echo ($vo["author"]); endif; ?></td>
      <td align="center" id="time<?php echo ($vo["id"]); ?>"><?php if($vo["modify_time"] == 0): ?>暂无<?php else: echo (date("Y-m-d H:i",$vo["modify_time"])); endif; ?></td>
      <td style="text-align:center;">
      <?php if($vo['status'] == 1): ?><a href="<?php echo U('changesta',array('id'=>$vo['id']));?>">忽略</a>&nbsp;<?php endif; ?>
       
        <a href="javascript:viewinfo(<?php echo ($vo['id']); ?>);">查看详情</a>&nbsp;
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
<style type="text/css">
  table tr td a img{
    width: 17px;
  }
</style>
<script type="text/javascript">
function viewinfo(id){
        window.top.art.dialog(
            { title:'给用户发送消息', id:'edit', iframe:"<?php echo U('viewinfo');?>"+'&id='+id,width:'450px',height:'180px'},
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

var user="<?php echo ($user); ?>";
function oneStatus(obj){
  
  var setStatus = $(obj).attr('status') == 1 ? 2 : 1; // 更新后的状态
  if(setStatus==1){
    return false;
  }
  var dataName = $(obj).attr('dataname');
  if(!confirm('操作后状态不可更改，您确定么?')){
    return false;
  }
  var ids=$(obj).attr('dataid');
  $.ajax({
    dataType:"json",
    url:"<?php echo U('ajaxStatus');?>",
    data:"id="+$(obj).attr('dataid')+"&status="+setStatus+"&name="+dataName ,
    success: function(data){
      if(data && data==1){
        if(setStatus == 2){
            $(obj).html('<img src="/images/yes.gif" title="已处理" />');
            $(obj).attr('status', 2);
        }else{
            $(obj).html('<img src="/images/no.gif" title="点击开启" />');
            $(obj).attr('status', 1);
        }
        $('#user'+ids).html(user);
        var myDate=new Date();
        var Y=myDate.getFullYear();
        var m=parseInt(myDate.getMonth())+1;
        var d=myDate.getDate();
        var H=myDate.getHours();
        var i=myDate.getMinutes();
        var s=myDate.getSeconds();
        var time=Y+"-"+m+"-"+d+" "+H+":"+i;
        $('#time'+ids).html(time);
      }
    }
  });
}
function getResult(obj) {
  var id = $(obj).attr('dataid');
  var result = $(obj).attr('result');
  // if(result==0){
  //   $("#jindu").prepend("<p>请先添加该竞猜的结果</p>");
  //   return false;
  // }
  $.ajax({
    type:'post',
    dataType:'json',
    url:"<?php echo U('Easy/endResult');?>",
    data:{id:id,result_id:result},
    // async:false,
    success: function(data){
      // alert(data.finish);
      // console.log(data);
      if(data.finish==1){
        var s=data.msg;
        var str = '';
        for(var i=0;i<s.length;i++){
          var f=s[i];
          str += '用户：'+f.user+'赢得木头数:'+f.gold+'<br />';
        }
        $("#jindu").prepend('<p>'+str+'结算完成'+'</p>');
      }else{
        $("#jindu").prepend('<p>'+data.msg+'</p>');
      }
    }
  });
}
</script>
</html>