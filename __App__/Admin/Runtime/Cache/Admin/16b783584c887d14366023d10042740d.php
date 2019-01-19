<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
<!--
$(document).ready(function(){
	_MP(0,$('#first_menu_url').val(),$('#first_menu_name').val());
});
//-->
</script>
<ul>
    <?php if(is_array($submenus)): foreach($submenus as $key=>$i): if(key == 0): ?><input type='hidden' id='first_menu_url'  value="<?php echo ($i["url"]); ?>"/><input type='hidden' id='first_menu_name'  value="<?php echo ($i["name"]); ?>"/><?php endif; ?>
    <li id="_MP<?php echo ($key); ?>" class="sub_menu"><a href="javascript:_MP(<?php echo ($key); ?>,'<?php echo ($i["url"]); ?>','<?php echo ($i["name"]); ?>');" hidefocus="true" style="outline:none;"><?php echo ($i["name"]); ?></a></li><?php endforeach; endif; ?>
</ul>