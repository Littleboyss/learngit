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


<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/highcharts/highcharts.js"></script>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
        <a href='javascript:;' class="on"><em>木头来源分析</em></a>
    </div>
</div>

<div class="subnav">

    <div class="explain-col search-form">
    
            <table width="100%" border="0" cellspacing="0" cellpadding="0">

            <tr>
                <td width="230">
                  起始日期:<input placeholder="起始日期" name="start_time" type="text" class="input-text" />
                </td>
                <td width="230">
                  截止日期:<input placeholder="截止日期" name="end_time" type="text" class="input-text" />
                </td>
                <td width="60">
                    <input type="submit" value="查询" class="button"/>
                </td width="60">
                <td width="60">
                    <input name="button" type="button" onclick="getdata(7)" value="最近7天" class="button"/>
                </td width="60">

                <td>
                    <input name="button" type="button"  onclick="getdata(30)" value="最近30天" class="button"/>
                </td>
            </tr>

        </table>

    </div>

</div>


<div class="pad_10">
    <div style="margin: 0 2em">
        <div id="container1" style="min-width: 500px; height: 500px; margin: 0 auto"></div>
        <br />
    </div>

  <div class="table-list">
    <form name="myform" action="<?php echo U('Dota2/HeroNew/del');?>" method="post" id="myform">
    
    <table id="checkList" class="tableList" width='98%' border='0'
    cellpadding='1' cellspacing='1' align="center">
  <thead>
    <tr align="center" class="h_tr">

        <th align="center">日期</th>
        <th align="center">付费购买</th>
        <th align="center">游戏奖励</th>
    </tr>
  </thead>
    <tbody id="checkList_tbody">



    </tbody>
</table>
      </table>
      <!-- <div class="btn"><a href="#" onclick="javascript:$('input[type=checkbox]').attr('checked', true)">全选</a>/<a href="#" onclick="javascript:$('input[type=checkbox]').attr('checked', false)">取消</a>
        <input name="submit" type="submit" onclick="return confirm('确认删除吗？');" value="删除" id="submit" class="button">
      </div> -->
    </form>
  </div>



</div>
</body>
<script type="text/javascript">
    

function show_datas(match_time, go_nums,back_nums) {
    //曲线图初始化
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container1',
            type: 'line',
            marginRight: 220,
            marginBottom: 25
        },
        title: {
            text: '木头曲线图表',
            x: -20 //center
        },
        subtitle: {
            text: '游戏奖励/付费购买',
            x: -20
        },
        xAxis: {
            scrollbar: {
               enabled: true
            },
            categories: match_time
        },
        yAxis: {
            title: {
                text: '数据'
            },
            plotLines: [{
                value: 0,
                width: 2,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.series.name + '</b><br/>数量:' + this.y + '<br />时间:'+this.x;
            }
        },
        legend: {
           layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: '付费购买',
            data: go_nums
        },{
            name: '游戏奖励',
            data: back_nums
        }]
    });
}


$(function(){
    getdata();

    $('input[type=submit]').click(function(){
        var start_time = $('input[name=start_time]').val();
        var end_time = $('input[name=end_time]').val();
        // alert(start_time);alert(end_time);
        getdata('',start_time,end_time);
    });

});



function getdata(day,start_time,end_time){

    if(typeof(day) != 'undefined' && day != ''){
        var json = {"day":day};
    }else if(typeof(start_time) != 'undefined' && typeof(end_time) != 'undefined'){
        var json = {"start_time":start_time,"end_time":end_time};
    }else{
        var json = {};
    }
    console.log(json);
    $.ajax({
        type:'post',
        url:"__SELF__",
        dataType:'json',
        data:json,
        success:function(e){
            if(e.error == 0){
                show_datas(e.data.time,e.data.back_nums, e.data.go_nums);

                var _data = e.data.time;
                var html = '';
                for(var i in _data){
                    var f = _data[i];
                    html += '<tr align="left"><td align="center">'+f+'</td><td align="center">'+e.data.go_nums[i]+'</td><td align="center">'+e.data.back_nums[i]+'</td></tr>';
                }
                // console.log(htm)
                // alert(html);
                $('#checkList_tbody').html(html);

            }else{
                alert(e.msg);
            }
        }
    });
}


</script>

</html>