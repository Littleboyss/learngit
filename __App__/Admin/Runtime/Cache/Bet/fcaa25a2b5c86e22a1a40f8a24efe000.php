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
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>选手列表</em></a>
    <span>|</span><a href="<?php echo U('add');?>"><em>添加选手</em></a> </div>
  </div>
</div>
<div class="subnav">
    <div class="explain-col search-form">
    <form name="myform" action="<?php echo U('index');?>" method="post">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="180">项目：
                  <select name="project_id" type="text" id="select_project" />
                    <option value="0">所有</option>
                    <?php if(is_array($project)): $i = 0; $__LIST__ = $project;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($key == $project_id): ?>selected="selected"<?php endif; ?>><?php echo ($rs); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                  </select>
                </td>
                <td width="230">队伍：
                  <select name="team_id" type="text" id="team" />
                    <option value="0">所有</option>
                    <?php if(is_array($team)): $i = 0; $__LIST__ = $team;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($rs["id"]); ?>" <?php if($rs['id'] == $team_id): ?>selected="selected"<?php endif; ?>><?php echo ($rs["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                  </select>
                </td>
                <td width="230">
                  中文名:<input  name="name" value="<?php echo ($name); ?>" type="text" class="input-text">
                </td>
                 <td width="230">
                  唯一id:<input  name="only_id" value="<?php echo ($only_id); ?>" type="text" class="input-text">
                </td>
                <td>
                    <input name="submit" type="submit" value="查询" class="button"/>
                </td>
            </tr>
        </table>
    </form>
    </div>
</div>
<div class="pad_10">
  <div class="table-list">
    <form name="myform" action="<?php echo U('Dota2/HeroNew/del');?>" method="post" id="myform">
    
    <table id="checkList" class="tableList" width='98%' border='0'
	cellpadding='1' cellspacing='1' align="center">
  <thead>
	<tr align="center" class="h_tr">
    <th width="4%" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th align="center">ID</th>
		<th align="center">app显示名称</th>
    <th align="center">英文名</th>
    <th align="center">头像</th>
    <th align="center">队伍队</th>
		<th align="center">项目</th>
    <th align="center">待定</th>
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
      <td align="center"><?php echo ($team[$vo[team_id]][name]); ?></td>
      <td align="center"><?php echo ($project[$vo[project_id]]); ?></td>
      <td align="center"><?php echo ($arr[$vo[is_undetermined]]); ?></td>
      <td width="10%" align="center">
          <a href="<?php echo U('edit', array('id' => $vo['id']));?>">修改</a>
          &nbsp;
          <a href="<?php echo U('del', array('id' => $vo['id']));?>" onclick="return confirm('你确定删除该项目？')">删除</a>
      </td>
		</tr><?php endforeach; endif; ?>
    <tr>
      <td class="pagination" colspan="10" align="center"><?php echo ($show); ?></td>
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
<script type="text/javascript">
  $(function(){
      $('#select_project').on('change',function(){
          var project_id = $(this).val();
          var html = '';
          $.ajax({
              type:'post',
              url:"<?php echo U('getteamjson');?>",
              data:{'project_id':project_id},
              dataType:'json',
              success:function(e){
                  $.each(e,function(i, j) {
                      html += '<option value="' + j['id'] + '">' + j['name'] + '</option>';
                      $('#team').html(html);
                  });
                  if(e == null){
                      $('#team').html('');
                  }
              }
          });
      });
  });
</script>
</html>