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
    <a href='javascript:;' class="on"><em>管理员管理</em></a><span>|</span><a href="<?php echo U('add');?>"><em>添加管理员</em></a> 
    </div>
</div>
<div class="pad_10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="10%" align="left">用户名</th>
                <th width="10%" align="left">昵称</th>
                <th width="10%" align="left">备注</th>
                <th width="" align="left">权限</th>
                <th width="15%" align="left">最后登录时间</th>
                <th width="10%" align="left">最后登录IP</th>
                <th width="5%" align="left">登录次数</th>
                <th width="10%">管理操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($admin)): $i = 0; $__LIST__ = $admin;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td align="center"><?php echo ($vo["id"]); ?></td>
                <td><?php echo ($vo["username"]); ?></td>
                <td><?php echo ($vo["nickname"]); ?></td>
                <td><?php echo ($vo["remark"]); ?></td>
                <td><?php echo ($vo["rights"]); ?></td>
                <td><?php if(($vo["logintime"]) == "0"): ?>-<?php else: echo (date('Y-m-d H:i:s',$vo["logintime"])); endif; ?></td>
                <td><?php echo ($vo["loginip"]); ?></td>
                <td><?php echo ($vo["logintimes"]); ?></td>
                <td align="center"><a href="javascript:edit(<?php echo ($vo["id"]); ?>)">修改</a> | <?php if (in_array($vo['username'], C('SUPER_ADMINS'))){ ?><font color="#cccccc">删除</font><?php }else{ ?><a href="<?php echo U('del', array('id' => $vo['id']));?>" onclick="return confirm('确定删除该管理员？')">删除</a><?php } ?></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
</div>
</div>
<script>
    function edit(id) {
        window.top.art.dialog(
            { title:'修改管理员信息', id:'edit', iframe:'/index.php?m=Adminuser&a=edit&id='+id ,width:'700px',height:'200px'},
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
</script>
</body>
</html>