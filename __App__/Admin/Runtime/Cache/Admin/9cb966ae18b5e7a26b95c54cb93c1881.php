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
  <div class="content-menu ib-a blue line-x"> <a href='javascript:;' class="on"><em>更新缓存</em></a> </div>
</div>
<div class="pad_10">
  <div class="common-form">
    <form name="myform" action="<?php echo U('Recache/del');?>" method="post" id="myform">
      <table width="100%" class="table_form contentWrap">
        <tr>
          <td width="80">项目选择</td>
          <td><?php $_result=C('APP_LIST');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$a): $mod = ($i % 2 );++$i;?><label style="margin-right: 10px"><input name="App" type="radio" value="<?php echo ($key); ?>" />&nbsp;<?php echo ($a); ?></label><?php endforeach; endif; else: echo "" ;endif; ?></td>
        </tr>
        <tr>
          <td>更新选择</td>
          <td>
            <label style="margin-right: 10px"><input name="del[]" type="checkbox" value="1" /> 编译缓存</label>
            <label style="margin-right: 10px"><input name="del[]" type="checkbox" value="2" /> 模板缓存</label>
            <label><input name="del[]" type="checkbox" value="3" /> 字段缓存</label>
          </td>
        </tr>
      </table>
      <div class="bk15"></div>
      <input type="submit" value="提交" class="button" />
    </form>
  </div>
</div>
</body>
</html>