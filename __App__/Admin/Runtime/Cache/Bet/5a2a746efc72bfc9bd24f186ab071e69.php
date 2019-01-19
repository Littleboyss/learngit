<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>图表统计</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/highcharts/highcharts.js"></script>
    <script type="text/javascript">

    $.ajax({
        type: 'post',
        url: "./index.php?g=Bet&m=Player&a=show_datas",
        // data: {
        //     'project_id': project_id
        // },
        dataType: 'json',
        success: function(e) {
            html = '<option value="0">请选择</option>';
            $.each(e,function(index,playerid){
                html += '<option value="'+playerid.only_id+'">'+playerid.only_id+'</option>';
            })
            $('#select_project').html(html);
        }
    });
    var chart;
    $(document).ready(function() {
        function show_datas(match_time, score, salary, ten_time,sum) {
            //曲线图初始化
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container1',
                    type: 'line',
                    marginRight: 220,
                    marginBottom: 25
                },
                title: {
                    text: '数据曲线图表',
                    x: -20 //center
                },
                subtitle: {
                    text: '球员数据',
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
                        return '<b>' + this.series.name + '</b><br/>单位:' + this.y + '<br />时间:'+this.x;
                    }
                },
                legend: {
                   layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [{
                    name: '单场得分',
                    data: score
                }, {
                    name: '工资',
                    data: salary
                }, {
                    name: '近十场平均分',
                    data: ten_time
                }, {
                    name: '计算工资',
                    data: sum
                }]
            });

        }
    $('#puts').on('click',function(){
        var play_id = $('#select_project').val();
        var f = $('#f').val();
        $.ajax({
            type: 'post',
            url: "./index.php?g=Bet&m=Player&a=show_datas",
            data: {
                'play_id': play_id,
                'f': f,
            },
            dataType: 'json',
            success: function(e) {
                console.log(typeof e);
                show_datas(e.match_time,e.score,e.salary,e.ten_time,e.sum);
            }
        });
    })
    });
    </script>
</head>

<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
       <td width="180">球员：
        <select name="player_id" id="select_project">
        </select>
      </td>
      <td width='230'>
          公式：
         <input type="text" name="f" id="f">
      </td>
       <td>
          <input name="submit" type="submit" value="查询" class="button" id="puts" />
      </td>
      </tr>
      </table>
    <div>
         
    </div>
    <div style="margin: 0 2em">
        <div id="container1" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
        <br />
    </div>
</body>

</html>