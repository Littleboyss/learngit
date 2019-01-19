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
  <div class="content-menu ib-a blue line-x">
    <a href='javascript:;' class="on"><em>房间列表</em></a>
    <span>|</span>
    <a href="<?php echo U('add');?>"><em>添加房间</em></a>
  </div>
  <div class="explain-col search-form">
    <form name="myform" action="<?php echo U('index');?>" method="post">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="150">项目：
                  <select name="project_id" type="text"/>
                    <option value="0">所有</option>
                    <?php if(is_array($projects)): $i = 0; $__LIST__ = $projects;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($rs[id]); ?>" <?php if($rs[id] == $project_id): ?>selected<?php endif; ?>><?php echo ($rs[name]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
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
    <th width="1%" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th align="center">赛事编号</th>
    <th align="center">赛事名称</th>
		<th align="center">房间人数</th>
    <th align="center">比赛开始时间</th>
    <th align="center">竞猜结束时间</th>
    <th align="center">是否推荐</th>
    <th align="center">发布状态</th>
    <th align="center">结算状态</th>
		<th align="center">操作</th>
	</tr>
  </thead>
	<tbody id="checkList_tbody">
    <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr align='left'>
      <td align="center"><input class="inputcheckbox" name="id[]" value="<?php echo ($vo['hero_id']); ?>" type="checkbox"></td>
			<td align="center"><a title="查看本比赛的下注详情" href="<?php echo U('BetGuess/index',array('match_id'=>$vo['id']));?>"><?php echo ($vo["id"]); ?></a></td>
      <td align="center"><?php echo ($vo["name"]); if($vo['open_id'] == 1){ echo "<font class='red'>(必开)</font>"; }else{ if ($vo['now_guess_num']/$vo['allow_guess_num'] < 1) { echo "<font class='green'>(未满)</font>"; }else{ echo "<font class='red'>(已满)</font>"; } } ?></td>
      <td align="center"><?php echo ($vo["now_guess_num"]); ?>/<?php echo ($vo["allow_guess_num"]); ?></td>
      <td align="center"><?php echo (date("Y-m-d H:i",$vo["match_start_time"])); ?></td>
      <td align="center"><?php echo (date("Y-m-d H:i",$vo["match_end_time"])); ?></td>
      <td align="center"><a href="javascript:;" dataid="<?php echo ($vo["id"]); ?>" status="<?php echo ($vo["is_hot"]); ?>" onclick="oneStatus(this)" dataname="is_hot"><?php if($vo['is_hot'] == 2): ?><img src="/images/no.gif" title="点击开启" /><?php else: ?><img src="/images/yes.gif" title="点击关闭" /></a><?php endif; ?></td>

      <td align="center"><a href="javascript:;" dataid="<?php echo ($vo["id"]); ?>" status="<?php echo ($vo["status"]); ?>" onclick="oneStatus(this)" dataname="status"><?php if($vo['status'] == 2): ?><img src="/images/no.gif" title="点击开启" /><?php else: ?><img src="/images/yes.gif" title="点击关闭" /></a><?php endif; ?></td>
      <th align="center">
        <?php if($vo['settlement_status'] == 1): ?><img src="/images/no.gif"  /><?php else: ?><img src="/images/yes.gif"  /><?php endif; ?>
      </th>
      <td width="25%" align="center">
          <a onclick="if(confirm('确定结算吗?')){getResult(this);}" href="javascript:;" dataid="<?php echo ($vo["id"]); ?>">结算</a>
          &nbsp;
          <a onclick="if(confirm('确定流盘吗?')){giveup(this);}" href="javascript:;" dataid="<?php echo ($vo["id"]); ?>">流盘</a>
          &nbsp;
          <a href="<?php echo U('edit', array('id' => $vo['id']));?>">修改</a>
          &nbsp;
          <a href="<?php echo U('del', array('id' => $vo['id']));?>" onclick="return confirm('你确定删除该竞猜？')">删除</a>
          &nbsp;
          <a href="javascript:view_count(<?php echo ($vo["id"]); ?>);">下注详情</a>
          
          <?php if($vo[project_id] == 4): ?>&nbsp;<a target="_blank" href="<?php echo ($api_url); ?>index.php?/g=api&m=public&a=updateroomlineup_admin&room_id=<?php echo ($vo["id"]); ?>">更新比分数据</a>
          <?php elseif(($vo[project_id] == 5) or ($vo[project_id] == 6)): ?>
            &nbsp;<a target="_blank" href="<?php echo ($api_url); ?>index.php?/g=api&m=public&a=updateroomlineuplol_admin&project_id=<?php echo ($vo["project_id"]); ?>&room_id=<?php echo ($vo["id"]); ?>">更新比分数据</a>
          <?php else: endif; ?>
          &nbsp;
          <a target="_blank" href="<?php echo ($api_url); ?>index.php?/g=api&m=public&a=updateroom_admin&room_id=<?php echo ($vo["id"]); ?>">更新房间排名</a>

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
<div style="width:auto; height:300px; border:1px solid #CCC; overflow-y:scroll; text-align:left;" class="input-text" id="jindu"></div>
</body>
<style type="text/css">
  table tr td a img{
    width: 17px;
  }
</style>
<script type="text/javascript">
function view_count(id) {
    window.top.art.dialog(
        { title:'下注统计', id:'edit', iframe:"<?php echo U('guessCount');?>"+'&match_id='+id ,width:'300px',height:'100px'},
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
var start = 0;
var current_id;
function getResult(obj) {
  var match_id = $(obj).attr('dataid');
  //记录当前的id值，充值开始位置项
  $.ajax({
    dataType:"json",
    url:"<?php echo U('Bet/get_result');?>",
    data:"match_id="+match_id+"&start="+start,
    success: function(data){
      //console.log(data);
      if(data && data.error == 0){
        $('#jindu').append('<p>'+data.msg+'</p>');
      }else{
        getResult(obj);
        start += 1;
        $('#jindu').append('<p>'+data.msg+'</p>');
      }
      //d.show()
    }
  });
  if (start == 0) {
    $.ajax({
      dataType:"json",
      url:"<?php echo U('Bet/update_player_data');?>",
      data:"room_id="+match_id,
      success: function(data){
      }
    });    
  }
}
function giveup(obj) {
  var match_id = $(obj).attr('dataid');
  //记录当前的id值，充值开始位置项
  $.ajax({
    dataType:"json",
    url:"<?php echo U('Bet/giveup');?>",
    data:"match_id="+match_id+"&start="+start,
    success: function(data){
      //console.log(data);
      top.art.dialog().close()
      if(data && data.error == 0){
        alert(data.msg);
        start = start + 50;
        if (data.finish != 1) {
          giveup(obj);
        }
      }else{
         var d= art.dialog({ title: '结算状态', content: data.msg }); 
         d.show()
      }
    }
  });
}
function oneStatus(obj){
  var setStatus = $(obj).attr('status') == 1 ? 2 : 1; // 更新后的状态
  //console.log(setStatus)
  var dataName = $(obj).attr('dataname');
  $.ajax({
    dataType:"json",
    url:"<?php echo U('state');?>",
    data:"id="+$(obj).attr('dataid')+"&status="+setStatus+"&name="+dataName ,
    success: function(data){
      if(data && data==1){
        if(setStatus == 2){
            $(obj).attr('status', 2);
            $(obj).html('<img src="/images/no.gif" title="点击开启" />');
        }else{
            $(obj).attr('status', 1);
            $(obj).html('<img src="/images/yes.gif" title="点击关闭" />');
        }
      }
    }
  });
}
</script>
</html>