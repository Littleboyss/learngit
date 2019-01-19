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



<link rel="stylesheet" type="text/css" href="/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="/js/calendar/win2k.css"/>
<script type="text/javascript" src="/js/calendar/calendar.js"></script>
<script type="text/javascript" src="/js/calendar/lang/en.js"></script>

<style type="text/css">
select optgroup {
    color: #2288cc;
}
select option {
    color: #2288cc;
}
input.iptxt{
    width: 350px;
}
input.sm{
    width: 100px;
}
.sbtn{
    height: 35px;
    line-height: 35px;
    cursor: pointer;
    display: inline-block;
}
.sbtn:hover{
    color: blue;
}
.tRight{
    text-align: right;
}
.tarea{
    width:345px;
    height: 106px;
}
</style>
<script>
    config.filterMode = true;
    config.width= '700px';
    KindEditor.ready(function(K) {
        var editor = K.create('#background',config);
    });
function upload_img(input,out){
    KindEditor.ready(function(K) {
        var editor =  K.editor(config);
        var uploadbutton = K.uploadbutton({
            button : K('#'+input)[0],
            fieldName : 'imgFile',
            url :  './index.php?m=Index&a=upload',
            afterUpload : function(data) {
                if (data.error === 0) {
                    var url = K.formatUrl(data.url, 'absolute');
                    K('#'+out).val(url);
                } else {
                    alert(data.message);
                }
            },
            afterError : function(str) {
                alert('上传失败');
               return false;
            }
        });
        uploadbutton.fileBox.change(function(e) {
            uploadbutton.submit();
        });
    });
}
upload_img('wqico','ico'); 
</script>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>添加称号</em></a>
        <span>|</span>
        <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('add');?>">
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">
                    <tr>
                        <td class="tRight">分类：</td>
                        <td class="tLeft" colspan="3">
                            <select name="class_id" id="sub_id">
                               <?php
 foreach ($projects as $key => $value) { echo '<option value="'.$value['id'].'"'; if($sub_id == $value['id']){ echo 'selected'; } echo '>'.$value['name'].'</option>'; } ?>
                            </select>
                        </td>
                    </tr>
                    <tbody id="list" >
                        
                    </tbody>
                    <tr>
                        <td class="tRight">称号名称：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="name" value="<?php echo ($data["name"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">称号缩略图：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico" name="avatar_img" value="<?php echo ($data["avatar_img"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico" value="上传图片">
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">称号发布者：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="author" value="<?php echo ($data["author"]); ?>" readonly="" class="huge iptxt sm" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">称号描述：</td>
                        <td class="tLeft" colspan="3">
                            <textarea name="depict" class="tarea"  ></textarea>（255字以内包括标点符号）
                        </td>
                    </tr>
                    <tr id="subtn">
                        <td class="center"></td>
                        <td class="center" colspan="3">
                            <input type="submit" value="保 存" />
                        </td>
                    </tr>
                </table>
            </form>
            <br /><br /><br />
    </div>
</div>
</body>
<script>

function checktype(a,b){
    $("." + a).css('display','table-row');
    $("." + b).css('display','none');
}



</script>
</html>