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
  <div class="table-list">
      <table width="100%" cellspacing="0">
        <tbody>
            <tr>
              <td >商品数量</td>
              <td align="center"><?php echo ($data['nums']); ?></td>
            </tr>
            <tr>
              <td>商品名称</td>
              <td align="center"><?php echo ($data['name']); ?></td>
            </tr>
            <?php if(isset($data['codes_list'])){ ?>
            <tr>
              <th>卡号</th>
              <th>卡密</th>
            </tr>
            <?php foreach($data['codes_list'] as $v){?>
              <tr>
                <td align="center"><?php echo $v['codes'];?></td>
                <td align="center"><?php echo $v['pwd'];?></td>
              </tr>
            <?php }}else{?>
            <tr>
              <td>商品属性</td>
              <td align="center"><?php echo $data['attribute'];?></td>
            </tr>
            <tr>
              <td>收货人姓名</td>
              <td align="center"><?php echo $info['name'];?></td>
            </tr>
            <tr>
              <td>收货人手机号</td>
              <td align="center"><?php echo $info['phone'];?></td>
            </tr>
            <tr>
              <td>收货人地址</td>
              <td align="center"><?php echo $info['address'];?></td>
            </tr>
            <tr>
              <td>收货人邮编</td>
              <td align="center"><?php echo $info['post_num'];?></td>
            </tr>
            <?php
 if ($data['status'] == 1) {?>
            <form name="myform" action="" method="post" id="myform">
            <tr>
              <td>快递公司</td>
              <td align="center"><input type="text" name="company" value="<?php echo $data['company'];?>"/></td>
            </tr>
             <tr>
              <td>快递单号</td>
              <td align="center"><input type="text" name="track_num" value="<?php echo $data['track_num'];?>"/></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                  <input type="text" hidden name="order_id" value="<?php echo $data['order_id'];?>">
                  <input type="text" hidden name="id" value=" <?php echo $data['id'];?>">
                  <input name="submit" type="submit" onclick="return confirm('确认修改吗？修改后无法复原');" value="修改" class="button"/>
                </td>
            </tr>
            </form>
            <?php }else{?>
              <tr>
              <td>快递公司</td>
              <td align="center"><?php echo $data['company'];?></td>
            </tr>
             <tr>
              <td>快递单号</td>
              <td align="center"><?php echo $data['track_num'];?></td>
            </tr>
              <?php }}?>
        </tbody>
      </table>
    
  </div>
</div>
</body></html>