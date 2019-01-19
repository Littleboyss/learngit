<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 transitional//EN' 'http://www.w3.org/tr/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>页面提示</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='Refresh' content='<?php echo ($waitSecond=3); ?>;URL=<?php echo ($jumpUrl); ?>'>
<style>
* {
	margin: 0;
	padding: 0;
}
.message {
	margin: 50px auto;
	border: 2px solid #498CD0;
	width: 450px;
}
.tCenter {
	background: #498CD0;
	color: #FFF;
	height: 25px;
	line-height: 25px;
}
</style>
</head>
<body>
<table class="message" cellpadding=0 cellspacing=1>
  <tr class="row" >
    <th class="tCenter space"><?php echo ($msgTitle); ?></th>
  </tr>
  <?php if(isset($message)): ?><tr class="row">
      <TD style="color:blue"><?php echo ($message); ?></TD>
    </tr><?php endif; ?>
  <?php if(isset($error)): ?><tr class="row">
      <TD style="color:red"><?php echo ($error); ?></TD>
    </tr><?php endif; ?>
  <?php if(isset($closeWin)): ?><tr class="row">
      <TD>系统将在 <span style="color:blue;font-weight:bold"><?php echo ($waitSecond=3); ?></span> 秒后自动关闭,如果不想等待,直接点击 <A HREF="<?php echo ($jumpUrl); ?>">这里</A> 关闭</TD>
    </tr><?php endif; ?>
  <?php if(!isset($closeWin)): ?><tr class="row">
      <TD>系统将在 <span style="color:blue;font-weight:bold"><?php echo ($waitSecond=3); ?></span> 秒后自动跳转,如果不想等待,直接点击 <A HREF="<?php echo ($jumpUrl); ?>">这里</A> 跳转</TD>
    </tr><?php endif; ?>
  <tr>
    <td height='5' class="bottomTd"></td>
  </tr>
</table>
</body>
</html>