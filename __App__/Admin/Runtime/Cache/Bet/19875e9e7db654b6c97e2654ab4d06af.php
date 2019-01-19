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


<link rel="stylesheet" type="text/css" href="/js/calendar/jscal2.css" />
<link rel="stylesheet" type="text/css" href="/js/calendar/border-radius.css" />
<link rel="stylesheet" type="text/css" href="/js/calendar/win2k.css" />
<script type="text/javascript" src="/js/calendar/calendar.js"></script>
<script type="text/javascript" src="/js/calendar/lang/en.js"></script>
<style type="text/css">
input.iptxt {
    width: 350px;
}
</style>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>添加新闻</em></a>
        <span>|</span>
        <a href="<?php echo U('news');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('news_add');?>">
            <table width='98%' border='0' cellpadding='0' cellspacing='0' align="center" class="rtable">
                <tr>
                    <td class="tRight">所属项目：</td>
                    <td class="tLeft" colspan="3">
                        <select name="project_id" id="project">

                          <?php if(is_array($project)): foreach($project as $key=>$item): ?><option value="<?php echo ($key); ?>"><?php echo ($item); ?></option><?php endforeach; endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight">所属队伍：</td>
                    <td class="tLeft" colspan="3">
                        <select name="team_id" id="team">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight">选择球员：</td>
                    <td class="tLeft" colspan="3">
                        <select name="player_id" id="player">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight">信息来源：</td>
                    <td class="tLeft" colspan="3">
                        <input type="text" name="title" value="超级玩家"  />
                    </td>
                </tr>
                <tr>
                    <td class="tRight">详细信息：</td>
                   <td class="tLeft" colspan="3">
                        <textarea name="detail"  style="width: 350px;" rows="5" ></textarea>（255字以内包括标点符号）
                    </td>
                </tr>
                <tr>
                    <td class="center"></td>
                    <td class="center" colspan="3">
                        <input type="submit" value="保 存" />
                    </td>
                </tr>
            </table>
        </form>
        <br />
        <br />
        <br />
    </div>
</div>
<script type="text/javascript">
$(function(){
  var html = '';
        $.ajax({
            type:'post',
            url:"<?php echo U('getteamjson');?>",
            data:{'project_id':4},
            dataType:'json',
            success:function(e){
                $.each(e,function(i, j) {
                    html += '<option value="' + i + '">' + j + '</option>';
                    $('.team_numbers').html(html);
                });
                if(e == null){
                    $('.team_numbers').html('');
                }
            }
        });
      $('#project').on('change',function(){
          var project_id = $(this).val();
          var html = '';
          $.ajax({
              type:'post',
              url:"<?php echo U('getteamjson');?>",
              data:{'project_id':project_id},
              dataType:'json',
              success:function(e){
                  team_id = e[0]['id'];
                  get_player(project_id,team_id)
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
    $('#team').on('change',function(){
        var team_id = $(this).val();
        var project_id = $('#project').val();
        get_player(project_id,team_id)
      });
    function get_player(project_id,team_id){
      var html = '';
        $.ajax({
            type:'post',
            url:"<?php echo U('getplayerjson');?>",
            data:{
                'project_id':project_id,
                'team_id':team_id
            },
              dataType:'json',
              success:function(e){
                  $.each(e,function(i, j) {
                      html += '<option value="' + j['id'] + '">' + j['name'] + '</option>';
                      $('#player').html(html);
                  });
                  if(e == null){
                      $('#player').html('');
                  }
              }
          });
    }
  });
</script>
</body>

</html>