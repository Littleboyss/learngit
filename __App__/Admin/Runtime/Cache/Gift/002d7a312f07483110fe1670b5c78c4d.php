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
</div>
<div class="pad_10">
  <a href="<?php echo U('delcard',array('id'=>$gift_id));?>">删除全部</a>
  <div class="table-list">
      <table width="100%" cellspacing="0">
        <thead>
          <tr>
          <th width="5%">编号</th>
            <th width="15%">兑换码</th>
            <th width="22%">添加时间</th>
            <th width="8%">状态</th>
            <th width="22%">领取时间</th>
            <th width="5%">UID</th>
          </tr>
        </thead>
        <tbody>
          <?php if(is_array($rs)): $i = 0; $__LIST__ = $rs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
            <td align="center"><?php echo ($i); ?></td>
              <td align="center"><?php echo ($v['codes']); ?></td>
              <td align="center"><?php echo (date("Y-m-d H:i:s",$v['addtime'])); ?></td>
              <td align="center"><?php if($v['updatetime'] == 0): ?><font color="#CCCCCC">未使用</font>
                  <?php else: ?>
                  <font color="#FF0000">已使用</font><?php endif; ?></td>
              <td align="center"><?php if(!empty($v['updatetime'])): echo (date("Y-m-d H:i:s",$v['updatetime'])); else: ?>未领取<?php endif; ?></td>
              <td align="center"><?php echo ($v['user_id']); ?></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
          <tr>
            <td colspan="8" class="pagination" align="center"><?php echo ($show); ?></td>
          </tr>
        </tbody>
      </table>
    
  </div>
</div>
</body></html>