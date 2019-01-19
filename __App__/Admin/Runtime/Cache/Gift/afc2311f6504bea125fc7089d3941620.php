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
                alert('自定义错误信息: ' + str);
            }
        });
        uploadbutton.fileBox.change(function(e) {
            uploadbutton.submit();
        });
    });
}
upload_img('wqico','ico'); 
upload_img('wqico1','ico1'); 
upload_img('wqico2','ico2'); 
upload_img('wqico3','ico3'); 
upload_img('wqico4','ico4'); 
upload_img('wqico5','ico5'); 
</script>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>修改商品</em></a>
        <span>|</span>
        <a href="<?php echo U('index');?>"><em>返回列表</em></a>
    </div>
</div>
<div class="pad_10">
    <div class="table-list">
        <form method="post" id="form1" action="<?php echo U('edit');?>">
                <table width='98%' border='0' cellpadding='0' cellspacing='0'
                       align="center" class="rtable">
                    <tr>
                        <td class="tRight">是否上架：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="state" value="1" checked="checked" />上架　
                            <input type="radio" name="state" value="2" <?php echo $data['state']== 2 ? 'checked="checked"' : ''; ?> />下架　
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">是否为热门推荐：</td>
                        <td class="tLeft" colspan="3">
                            <input type="radio" name="hot_sort" value="1" checked="checked" />是　
                            <input type="radio" name="hot_sort" value="0" <?php echo $data['hot_sort']== 2 ? 'checked="checked"' : ''; ?> />否　
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">分类：</td>
                        <td class="tLeft" colspan="3">
                            <select name="shop_sub_id" id="sub_id">
                                <?php
 foreach ($projects as $key => $value) { if ($value['pid'] == 0 ) { echo '<optgroup label="'.$value['name'].'">'; foreach ($projects as $k => $v) { if ($v['pid'] == $value['id']) { echo '<option value="'.$v['id'].'"'; if($data['shop_sub_id'] == $v['id']){ echo 'selected'; } echo '>'.$v['name'].'</option>'; } } echo "</optgroup>"; } } ?>
                            </select>
                        </td>
                    </tr>
                    <tbody id="list" >
                        <?php  if(!empty($data['attrdata'])){ foreach ($data['attrdata'] as $key => $value) { echo "<tr>"; echo "<td class='tRight'>".$value['attr_name']."：</td>"; echo "<td class='tLeft' colspan='3'>"; echo "<input type='text' name='attr_value[]' value='".$value['value']."' class='huge iptxt sm' />"; echo "<input type='text' hidden name='attr_id[]' value='".$value['id']."' class='huge iptxt sm' />(请以逗号分隔,更改属性之后请清空库存重新添加库存)"; echo "</td>"; echo "</tr>"; } } ?>
                    </tbody>
                    <tr>
                        <td class="tRight">商品名称：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="name" value="<?php echo ($data["name"]); ?>" class="huge iptxt" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">商品缩略图：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico" name="avatar_img" value="<?php echo ($data["avatar_img"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico" value="上传图片">
                        </td>
                    </tr>
                    <tr class="img">
                        <td class="tRight">商品相册图片1：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico1" name="img1" value="<?php echo ($data["img1"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico1" value="上传图片">(至少上传一张)
                        </td>
                    </tr>
                    <tr class="img">
                        <td class="tRight">商品相册图片2：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico2" name="img2" value="<?php echo ($data["img2"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico2" value="上传图片">
                        </td>
                    </tr>
                    <tr class="img">
                        <td class="tRight">商品相册图片3：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico3" name="img3" value="<?php echo ($data["img3"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico3" value="上传图片">
                        </td>
                    </tr>
                    <tr class="img">
                        <td class="tRight">商品相册图片4：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico4" name="img4" value="<?php echo ($data["img4"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico4" value="上传图片">
                        </td>
                    </tr>
                    <tr class="img">
                        <td class="tRight">商品相册图片5：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" class="large iptxt" id="ico5" name="img5" value="<?php echo ($data["img5"]); ?>" style="margin-top: 3px"> 
                            <input type="button" id="wqico5" value="上传图片">
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">单人最大购买数量：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="remain_num" value="<?php echo ($data["remain_num"]); ?>" class="huge iptxt sm" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">排序号：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="sort" value="<?php echo ($data["sort"]); ?>" class="huge iptxt sm" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">商品发布者：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="author" value="<?php echo ($data["author"]); ?>" readonly="" class="huge iptxt sm" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">价格：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="price" value="<?php echo ($data["price"]); ?>" class="huge iptxt sm"   />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">消耗资源类型:</td>
                        <td class="tLeft" colspan="3">
                            <select name="type">
                             <option value='2'>木头</option>
                             <option value='1'>砖石</option>
                            </select>(实物必须为木头)
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">库存：</td>
                        <td class="tLeft" colspan="3">
                            <input type="text" name="has_nums" value="<?php echo ($data["has_nums"]); ?>" class="huge iptxt sm" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">商品描述：</td>
                        <td class="tLeft" colspan="3">
                            <textarea name="intro" class="tarea"  ><?php echo ($data["intro"]); ?></textarea>（255字以内包括标点符号）
                        </td>
                    </tr>
                    <tr>
                        <td class="tRight">商品详情：</td>
                        <td class="tLeft" colspan="3">
                            <textarea name="detail" id="background"  ><?php echo ($data["detail"]); ?></textarea>
                        </td>
                    </tr>
                    <tr id="subtn">
                        <td class="center"></td>
                        <td class="center" colspan="3">

                            <input type="text" name="id" hidden="" value="<?php echo ($data['id']); ?>" />
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
$("#sub_id").change(function(){	
    $('#list').html('');
    var sub_id = $('#sub_id option:selected').val()
	$.ajax({
        url: '/index.php?g=Gift&m=Exchange&a=get_attribute',
        type: 'post',
        data: {
            'sub_id':sub_id
        },
        cache: false,
        dataType: 'json', // 期待后台返回的数据类型 json
        success:function(json){
            if (json.status == 1) {
                var html ='';
                $.each(json.url,function(i,item){
                    html +="<tr>";
                    html +="<td class=\'tRight\'>"+item.attr_name+"：</td>";
                    html +="<td class=\'tLeft\' colspan=\'3\'>";
                    html +="<input type=\'text\' name=\'attr_value[]\' value=\'\' class=\'huge iptxt sm\' />";
                    html +="<input type=\'text\' hidden name=\'attr_id[]\' value=\'"+item.id+"\' class=\'huge iptxt sm\' />(请以逗号分隔)";
                    html +="</td>";
                    html +="</tr>";
                })
                $('#list').html(html);
                $('#list').show();
            }
            
        },
    })
})


</script>
</html>