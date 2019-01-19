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


        <div id="main_frameid" class="pad-10 display" style="_margin-right:-12px;_width:98.9%;">
            <script type="text/javascript">
                $(function(){if ($.browser.msie && parseInt($.browser.version) < 7) $('#browserVersionAlert').show();}); 
            </script>
            <div class="explain-col mb10" style="display:none" id="browserVersionAlert">
                使用IE8浏览器可获得最佳视觉效果</div>
            <div class="col-2 lf mr10" style="width:48%">
                <h6>我的个人信息</h6>
                <div class="content">
                    您好，<?php echo ($adminInfo["nickname"]); ?><br />
                    权限：<?php echo ($adminInfo["rights"]); ?><br />
                    你的IP：<?php echo ($adminInfo["ip"]); ?><br />
                    登录本系统次数：<?php echo ($adminInfo["logintimes"]); ?><br />
                </div>
            </div>
            <div class="col-2 col-auto">
                <h6>系统信息</h6>
                <div class="content">
                    操作系统：<?php echo (PHP_OS); ?><br />
                    服务器软件：<?php echo ($_SERVER['SERVER_SOFTWARE']); ?><br />
                    PHP版本：<?php echo (PHP_VERSION); ?><br />
                    PHP运行方式：<?php echo (PHP_SAPI); ?><br />
                    MySQL版本：<?php echo ($sysInfo["mysqlVer"]); ?><br />
                    服务器IP：<?php echo ($_SERVER['SERVER_ADDR']); ?><br />
                    phpinfo：<a href="<?php echo U('Index/phpinfo');?>" target="_blank">[查看]</a><br />
                    <div class="bk20 hr"><hr /></div>
                    ThinkPHP版本：<?php echo (THINK_VERSION); ?><br />
                    APP_DEBUG：<?php echo (APP_DEBUG); ?><br />
                </div>
            </div>
            <div class="bk10"></div>
        </div>
    </body>
</html>
<script type="text/javascript">$("#main_frameid").removeClass("display");</script>