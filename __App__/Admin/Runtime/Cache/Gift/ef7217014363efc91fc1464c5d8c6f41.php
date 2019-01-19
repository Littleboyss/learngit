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
    <a onclick="return confirm('确认删除吗？');" href="<?php echo U('product_delete',array('id'=>$goods_id));?>">清空库存</a>
  </div>
</div>
<div class="pad_10">
  <div class="table-list">
      <table width="100%" cellspacing="0">
        <thead>
          <tr>
          <th width="15%">编号</th>
            <th width="45%">属性名称</th>
            <th width="50%">数量</th>
          </tr>
        </thead>
        <tbody>
          <form method="post" id="form1" action="<?php echo U('product_edit');?>">
          <?php if(is_array($rs)): $i = 0; $__LIST__ = $rs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
            <td align="center"><?php echo ($i); if(isset($v['id'])){ echo '<input type="text" hidden="" name="id[]" value="'.$v['id'].'">'; } ?></td>
              <td align="center"><?php  if(isset($v['attr_value'])){ echo $v['attr_value']; echo '<input type="text" hidden="" name="attr_value[]" value="'.$v['attr_value'].'">'; }else{ echo $v; echo '<input type="text" hidden="" name="attr_value[]" value="'.$v.'">'; } ?></td>
              <td align="center"><input align="center" type="text" name="attr_nums[]" value="<?php  if(isset($v['nums'])){ echo $v['nums']; }else{ echo 0; } ?>">
              <input type="text" hidden="" name="goods_id" value="<?php echo ($goods_id); ?>">
              </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            <tr id="subtn">
                <td class="center"></td>
                <td class="center"></td>
                <td class="center" colspan="2">
                    <input type="submit" value="保 存" />
                </td>
            </tr>
              </form>
        </tbody>
      </table>
    
  </div>
</div>
</body></html>