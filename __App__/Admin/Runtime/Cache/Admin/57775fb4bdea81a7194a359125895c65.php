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
    <a href='javascript:;' class="on"><em>刀币领取详情</em></a>
    </div>
</div>
<div class="pad_10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th>昵称</th>
                <th>用户id</th>
                <th>QQ账号</th>
                <th>完美账号</th>
                <th>领取的时间</th>
                <th>处理状态</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td align="center"><?php echo ($vo["id"]); ?></td>
                <td align="center"><?php echo ($vo["nickname"]); ?></td>
                <td align="center"><?php echo ($vo["uid"]); ?></td>
                <?php $zh = unserialize($vo[info]); ?>
                <td align="center"><?php echo ($zh[qq]); ?></td>
                <td align="center"><?php echo ($zh[wm]); ?></td>
                <td align="center"><?php if(($vo["logintime"]) == "0"): ?>-<?php else: echo (date('Y-m-d H:i:s',$vo["add_time"])); endif; ?></td>
                <td align="center"><?php if($vo[status] == 1): ?><span onclick="sta(this,<?php echo ($vo["id"]); ?>)" style="color:red;">未处理</span><?php else: ?>已处理<?php endif; ?></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            <tr>
      <td class="pagination" colspan="7" align="center"><?php echo ($page); ?></td>
    </tr>
        </tbody>
    </table>
</div>
</div>
<script type="text/javascript">
    function sta(obj,id) {
        if(!confirm('你确定标记为已处理吗？')){
            return false;
        }
        $.ajax({
            data:{id:id},
            url:"<?php echo U('sta');?>",
            success:function(e){
                if(e == 0){
                    $(obj).css('color', '');
                    $(obj).html('已处理');
                }else{
                    alert(e);
                }
            }
        });
    }
</script>
</body>
</html>